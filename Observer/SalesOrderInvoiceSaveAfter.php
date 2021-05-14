<?php

namespace Excellence\Trello\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SalesOrderInvoiceSaveAfter implements ObserverInterface {

   

    public function __construct(
    \Excellence\Trello\Model\SetupwizardFactory $setupwizardFactory
    ) {
        $this->setupwizardFactory = $setupwizardFactory;
    }

    /*
     * creat shipment pdf on submit shipment
     */

    public function execute(Observer $observer) {
        $id = $observer->getInvoice()->getIncrementId();
        $orderId = $observer->getInvoice()->getOrderId();
        $IncrementId = $observer->getInvoice()->getOrder()->getIncrementId();
        $this->setupwizardFactory->create()->createPdf($orderId, $id, $IncrementId, $type = 'invoice', $pdfName = 'Invoiced');
       
    }

}
