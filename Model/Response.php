<?php

namespace PocztaPolska\Tracking\Model;

use Magento\Setup\Exception;

class Response implements ResponseInterface
{
    const STATUS_OK = 0;

    const STATUS_MULTIPLE_PARCELS = 1;

    const STATUS_PARCEL_NOT_FOUND = -1;

    const STATUS_INCORRECT_NUMBER = -2;

    const STATUS_ERROR = -99;

    public function convertToArray($apiResponseData): array
    {
        $trackingData = [];

        foreach ($apiResponseData->return->danePrzesylki->zdarzenia->zdarzenie as $zdarzenie) {
            $trackingData[] = [
                'code' => $zdarzenie->kod,
                'date' => $zdarzenie->czas,
                'location' => $zdarzenie->jednostka->nazwa,
                'activity' => $zdarzenie->nazwa,
            ];
        }

        $data = [
            'number' => $apiResponseData->return->danePrzesylki->numer,
            'shipping_date' => $apiResponseData->return->danePrzesylki->dataNadania,
            'package_type' => $apiResponseData->return->danePrzesylki->rodzPrzes,
            'dispatch_country' => $apiResponseData->return->danePrzesylki->krajNadania,
            'destination_country' => $apiResponseData->return->danePrzesylki->krajPrzezn,
            'dispatch_office' => $apiResponseData->return->danePrzesylki->urzadNadania->nazwa,
            'tracks' => $trackingData
        ];

        return $data;
    }

    public function validate($apiResponseData)
    {
        $status = $apiResponseData->return->status;

        if (self::STATUS_OK === $status || self::STATUS_MULTIPLE_PARCELS === $status) {
            return true;
        }

        if (self::STATUS_PARCEL_NOT_FOUND === $status) {
            throw new Exception(__('No data is available for this item within the period of the last 30 days'));
        }

        if (self::STATUS_INCORRECT_NUMBER === $status) {
            throw new Exception(__('The package ID number is wrong'));
        }

        if (self::STATUS_ERROR === $status) {
            throw new Exception(__('Error'));
        }
    }
}
