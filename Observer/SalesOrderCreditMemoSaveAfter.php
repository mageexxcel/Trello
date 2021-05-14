<?php
 
 
namespace Excellence\Trello\Observer;
 

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
 
 
class SalesOrderCreditMemoSaveAfter implements ObserverInterface
{
    
     public function __construct(
    \Excellence\Trello\Model\SetupwizardFactory $setupwizardFactory
    ) {
        $this->setupwizardFactory = $setupwizardFactory;
    }
 
    public function execute(Observer $observer)
    {  
        $id = $observer->getEvent()->getCreditmemo()->getIncrementId();
        $orderId = $observer->getEvent()->getCreditmemo()->getOrderId();
        $incrementId = $observer->getCreditmemo()->getOrder()->getIncrementId();
        $this->setupwizardFactory->create()->createPdf($orderId, $id, $incrementId, $type = 'creditmemo', $pdfName = 'Creditmemo');
      
    }
}
