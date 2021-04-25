<?php

namespace PocztaPolska\Tracking\Controller\Index;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\ResultFactory;
use PocztaPolska\Tracking\Model\ResponseInterface;
use PocztaPolska\Tracking\Model\TrackingInterface;

class Tracking extends Action
{
    /** @var ResponseInterface */
    protected $response;

    /** @var TrackingInterface */
    protected $tracking;

    public function __construct(
        Context $context,
        ResponseInterface $response,
        TrackingInterface $tracking
    ) {
        parent::__construct($context);

        $this->response = $response;
        $this->tracking = $tracking;
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     * @throws NotFoundException
     */
    public function execute()
    {
        $trackingNumber = $this->getRequest()->getParam('tracking_number');

        try {
            $this->validate($trackingNumber);

            $trackData = $this->getTrack($trackingNumber);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('tracking/*/', []);
        }

        /** @var \Magento\Framework\View\Result\Page resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        /** @var Template $block */
        $block = $resultPage->getLayout()->getBlock('trackingGrid');
        $block->setData('track_data', $trackData);

        return $resultPage;
    }

    /**
     * @param $trackingNumber
     * @return array
     */
    private function getTrack(string $trackingNumber)
    {
        $apiData = $this->tracking->getTrackByPackageId($trackingNumber);

        $this->response->validate($apiData);

        return $this->response->convertToArray($apiData);
    }

    /**
     * @param string $trackingNumber
     */
    private function validate(string $trackingNumber)
    {
        if (true === empty(trim($trackingNumber))) {
            throw new LocalizedException(__('Enter tracking number and try again.'));
        }
    }
}
