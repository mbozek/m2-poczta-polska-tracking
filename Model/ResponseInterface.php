<?php

namespace PocztaPolska\Tracking\Model;

interface ResponseInterface
{
    public function convertToArray($apiResponseData): array;

    public function validate($apiResponseData);
}
