<?php
 
 
namespace Excellence\Trello\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
 
 
class SalesOrderShipmentSaveAfter implements ObserverInterface
{
    
     public function __construct(
    \Excellence\Trello\Model\SetupwizardFactory $setupwizardFactory
    ) {
        $this->setupwizardFactory = $setupwizardFactory;
    }

    /*
     * creat shipment pdf on submit shipment
     */

    public function execute(Observer $observer) {
     
       $id = $observer->getShipment()->getIncrementId();
       $orderId = $observer->getShipment()->getOrderId();
       $IncrementId = $observer->getShipment()->getOrder()->getIncrementId();
       $this->setupwizardFactory->create()->createPdf($orderId, $id, $IncrementId, $type = 'shipment', $pdfName = 'Shipped');
  
    }
 
   
}
