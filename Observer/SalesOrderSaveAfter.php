<?php
namespace Excellence\Trello\Observer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
 
class SalesOrderSaveAfter implements ObserverInterface
{
    
     public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigObject,
        \Excellence\Trello\Model\SetupwizardFactory $setupwizardFactory,
        \Excellence\Trello\Model\CardFactory $cardFactory,
        \Excellence\Trello\Model\Trello $trelloFactory     
    ) {
        $this->scopeConfigObject = $scopeConfigObject; 
        $this->setupwizardFactory = $setupwizardFactory;
        $this->cardFactory = $cardFactory;
        $this->trelloFactory = $trelloFactory;
    }

    

    public function execute(Observer $observer) {
       
      $orderData = $observer->getEvent()->getOrder();
      $trello_list_enable = $this->scopeConfigObject->getValue("trello/webhooks/trellolist");
       
        $active = $this->checkActiveEnable();
        if (($active == true) && ($trello_list_enable == 1)) {
           
            $oldstatus = $orderData->getOrigData('status');
            $Newstatus = $orderData->getData('status');
            $order_id = $orderData->getId();

            $modelid = $this->setupwizardFactory->create()->getCollection();
            $setupwizard_data = $modelid->getData();
            $idList = '';
            foreach ($setupwizard_data as $setwizd) {
                if ($setwizd['order_state'] == $Newstatus) {
                    $idList = $setwizd['trello_list_id'];
                }
            }
            $newListId = $idList;
  
     
            if (($oldstatus != $Newstatus) && ($newListId != '') && ($oldstatus != '')) {
                
                $cardcollection = $this->cardFactory->create()->getCollection()->addFieldToFilter('order_id', $order_id);
                $card_id = $cardcollection->getFirstItem()->getCardId();
                /*
                 * move  card one list to another according to state (new list id $value['value'])
                 */
                $value = array();
                $value['value'] = $newListId;
                $this->trelloFactory->putCard($value, $card_id);
                /*
                 * write comment on moved card 
                 */
                $valueComment = array();
                $valueComment['text'] = __('@magento_bot: Card Moved Due To State Change')."";
                $this->trelloFactory->postCommentCards($valueComment, $card_id);
                
                /*
                 * move the buttom to top in new list
                 */
                $valuemove = array();
                $valuemove['value'] = 'top';
                $this->trelloFactory->putCardTop($valuemove, $card_id);
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
}
