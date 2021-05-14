<?php

namespace Excellence\Trello\Model;

use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

class Setupwizard extends \Magento\Framework\Model\AbstractModel {

    const CACHE_TAG = 'excellence_trello_setupwizard';

    protected $_fileFactory;

    public function __construct(
      \Magento\Framework\Model\Context $context,
      \Magento\Framework\Registry $registry,
      \Excellence\Trello\Model\ResourceModel\Setupwizard $resource,
      \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigObject, 
      \Excellence\Trello\Model\ResourceModel\Setupwizard\Collection $resourceCollection,
      \Magento\Config\Model\ResourceModel\Config $resourceConfig,
      \Magento\Store\Model\StoreManagerInterface $storeManager,
      \Excellence\Trello\Model\CardFactory $cardFactory, 
      \Excellence\Trello\Model\TrelloFactory $trelloFactory,
      \Magento\Framework\ObjectManagerInterface $objectManager,
      \Excellence\Trello\Model\ActiontabFactory $ActiontabFactory,
      \Excellence\Trello\Model\RulesFactory $RulesFactory, 
      \Magento\Framework\Filesystem $filesystem,      
       array $data = []
    ) {
        $this->_resourceConfig = $resourceConfig;
        $this->scopeConfigObject = $scopeConfigObject;
        $this->_storeManager = $storeManager;
        $this->cardFactory = $cardFactory;
        $this->trelloFactory = $trelloFactory;
        $this->_objectManager= $objectManager;
        $this->_filesystem = $filesystem;
        $this->actointab = $ActiontabFactory;
        $this->rulesModel = $RulesFactory;
        parent::__construct( $context, $registry, $resource, $resourceCollection, $data );
    }
    
    public function createPdf($orderId, $id, $incrementId, $type, $pdfName) { 
       
       $active = $this->checkActiveEnable();
       $cardModel = $this->cardFactory->create();
        if ($active) { 
            $modelid = $cardModel->getCollection()->addFieldToFilter('order_id', $orderId)->getFirstItem()->getData();
            if (!empty($modelid)) {
                $cardId = $modelid['card_id'];
                if (trim($type) == 'creditmemo') {
                    $pdfUrl = $this->getCreditmemoPdf($id);
                } elseif (trim($type) == 'shipment') {
                    $pdfUrl = $this->getShipmentPdf($id);
                } elseif (trim($type) == 'invoice') {
                  $pdfUrl = $this->getInvoicePdf($id);
                }
                $value['url'] = $pdfUrl;
                $value['name'] = 'Order #'.$incrementId. ' ' . $pdfName; 
                $this->trelloFactory->create()->postAttchment($cardId, $value);
             
            }
        }
    }

   public function getInvoicePdf($invoiceId, $download = false) {

       $baseurl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'invoice';
      
        if ($invoiceId) {
           $invoice = $this->_objectManager->get('Magento\Sales\Model\Order\Invoice')->load($invoiceId);
           if ($invoice) {
               $pdf = $this->_objectManager->get(
                   'Magento\Sales\Model\Order\Pdf\Invoice'
               )->getPdf(
                   [$invoice]
               );
               
                if (!file_exists($baseurl)) {
                $mediaDir = $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA);
                $invoiceDir = '/invoice';
                $mediaDir->create($invoiceDir);
            }
            $myModuleDir = BP . '/pub/media/invoice/'.$invoiceId.'.pdf';
            file_put_contents($myModuleDir , $pdf->render());
           $pdfUrl = $baseurl .'/'. $invoiceId.'.pdf';
            return $pdfUrl;
           }
       }
    }

    public function getShipmentPdf($shipmentId, $download = false) {
      $baseurl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'shipment';
        if ($shipmentId) {
           $shipment = $this->_objectManager->get('Magento\Sales\Model\Order\Shipment')->load($shipmentId);
         
           if ($shipment) {
               $pdf = $this->_objectManager->get(
                   'Magento\Sales\Model\Order\Pdf\Shipment'
               )->getPdf(
                   [$shipment]
               );
               
                if (!file_exists($baseurl)) {
                $mediaDir = $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA);
                $invoiceDir = '/shipment';
                $mediaDir->create($invoiceDir);
            }
            
            $myModuleDir = BP . '/pub/media/shipment/'.$shipmentId.'.pdf';
            file_put_contents($myModuleDir , $pdf->render());
             $pdfUrl = $baseurl .'/'. $shipmentId.'.pdf';
             return $pdfUrl;
         
           }
       }
        
   }

     public function getCreditMemoPdf($creditmemoId, $download = false) {
       
       $baseurl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'creditmemo';
       if ($creditmemoId) {
           $creditmemo = $this->_objectManager->get('Magento\Sales\Model\Order\Creditmemo')->load($creditmemoId);
       
           if ($creditmemo) {
               $pdf = $this->_objectManager->get(
                   'Magento\Sales\Model\Order\Pdf\Creditmemo'
               )->getPdf(
                   [$creditmemo]
               );
               
                if (!file_exists($baseurl)) {
                $mediaDir = $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA);
                $invoiceDir = '/creditmemo';
                $mediaDir->create($invoiceDir);
            }
            
            $myModuleDir = BP . '/pub/media/creditmemo/'.$creditmemoId.'.pdf';
            file_put_contents($myModuleDir , $pdf->render());
            $pdfUrl = $baseurl .'/'. $creditmemoId.'.pdf';
            return $pdfUrl;
           }
       } 
        
    }
    
    /*
     * check module is enable and active (setupdone)
     * return boolean true and false
     */

    public function checkActiveEnable() {
        $is_enable = $this->scopeConfigObject->getValue('trello/advance/enable');
        $isActive = $this->scopeConfigObject->getValue('trello/wizard/setupdone');
        $return = (($isActive == 'Yes') && ($is_enable == 1)) ? true : false;
        return $return;
    }
    
    protected function _construct() {
        $this->_init('Excellence\Trello\Model\ResourceModel\Setupwizard');
    }

    public function getIdentities() {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function setupRemove() {
        $collection = $this->getCollection();
        foreach ($collection as $item) {
            $item->delete();
        }
       $actionTabCollection = $this->actointab->create()->getCollection();
         foreach ($actionTabCollection as $item) {
            $item->delete();
        }
        $rulesCollection = $this->rulesModel->create()->getCollection();
         foreach ($rulesCollection as $item) {
            $item->delete();
        }
        $this->_resourceConfig->saveConfig('trello/wizard/setupdone', 'No', \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->_resourceConfig->saveConfig('trello/setting/tokenkey', '', \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->_resourceConfig->saveConfig('trello/setupwizard/webhookid', '', \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        return true;
    }

    public function getToken() {
        $value = $this->scopeConfigObject->getValue('trello/setting/tokenkey');
        return $value;
    }
    public function getKeyValue() {
        $value = $this->scopeConfigObject->getValue('trello/advance/trellokey');
        return $value;
    }
    
    public function saveWebookId($webhook_id){
       $this->_resourceConfig->saveConfig('trello/setupwizard/webhookid', $webhook_id, \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        return true;  
    }
    
    public function getWebhookIdToken(){
        $result['webhook_id'] = $this->scopeConfigObject->getValue('trello/setupwizard/webhookid');
        $result['token'] = $this->scopeConfigObject->getValue('trello/setting/tokenkey');
        return $result;
    }
    
    public function setupDone($data) {
        /*
         * tirm this array remove extra value like  [form_key] => i0Th8G3UYh25c1R8
         */
        if (array_key_exists('key', $data)) {
            array_shift($data);
        }
        
        if (array_key_exists('form_key', $data)) {
            array_shift($data);
        }

        $ass_array = array();
        /*
         * convert array to multidimation array of two value , key (order_stat) and value (trello_list_id)
         */
        $i = 0;
        foreach ($data as $key => $value) {
            $ass_array[$i]['order_state'] = $key;
            $ass_array[$i]['trello_list_id'] = $value;
            $i++;
        }
        $setup_data = $this->getCollection();
        $order_item = $setup_data->getData();

        /*
         * get the collection and check exist record or not if exist the update record otherwise insert
         */
        $k = 0;
        if (!empty($order_item)) { 
            if (sizeof($order_item) != sizeof($ass_array)) {
                array_shift($order_item);
                
            }
           
            foreach ($order_item as $item) {
                $order_state = $item['order_state'];
                $id = $item['id'];

                $ass = $ass_array[$k];
                $this->load($id)->addData($ass);
                $this->save();
                $k++;
            }
         } else { 

         foreach ($ass_array as $ass) {
                $this->setData($ass);
                $this->save();
            }
        }
        
         /*
         * set value setupdone (in core_config_data table ) Yes after setupwizard done
         */
        $this->_resourceConfig->saveConfig('trello/wizard/setupdone', 'Yes', \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
    }

}
