<?php

namespace Excellence\Trello\Model;

class Webhook extends \Magento\Framework\DataObject
{  
   
    
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
       \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigObject,     
       \Magento\Config\Model\ResourceModel\Config $resourceConfig,
       \Excellence\Trello\Model\SetupwizardFactory $setupwizardFactory,
       \Excellence\Trello\Model\CardFactory $cardFactory,
       \Magento\Sales\Model\OrderFactory $orderFactory,
       \Magento\Sales\Model\Order\Email\Sender\OrderCommentSender $objectComment, 
        array $data = []
    ) {
         $this->_resourceConfig = $resourceConfig;
         $this->scopeConfigObject = $scopeConfigObject;
         $this->setupwizard = $setupwizardFactory;
         $this->cardFactory = $cardFactory;
         $this->orderFactory = $orderFactory;
         $this->objectComment= $objectComment;
         parent::__construct($data);
    }
   
   
    
    /*
     * call when any event fire in trello , like updatecard , commentCard etc
     * @param: $json   json type
     */
    public function webhookComment($json) {
        $json_object = json_decode($json);
        $boardIdWebhook = $json_object->model->id;
        $action_type = $json_object->action->type;
        $card_id = $json_object->action->data->card->id;
        $comment_enable = $this->scopeConfigObject->getValue('trello/webhooks/comment');
        $is_enable = $this->scopeConfigObject->getValue('trello/advance/enable'); 
        $isActive = $this->scopeConfigObject->getValue('trello/wizard/setupdone'); 
        $boardId = $this->setupwizard->create()->getCollection()->getFirstItem()->getTrelloListId();
      if (trim($boardId) == trim($boardIdWebhook)) {
            if ($action_type == 'commentCard' && ($comment_enable != 0) && ($isActive == 'Yes') && ($is_enable == 1)) {
                $comment = $json_object->action->data->text;
                if ($comment_enable == 1 && (preg_match('/@magento_bot/', $comment))) {
                    $this->saveTrelloComment($card_id, $comment);
                } elseif ($comment_enable == 2) { 
                    $this->saveTrelloComment($card_id, $comment);
                }
            }
        }
    }

    /*
     * web hooks for card comment in trello , submit comment in order (over magento)
     * @param varchar :card_id Trello card id 
     * @param string : comment from  trello
     */

    public function saveTrelloComment($card_id, $comment) {
        $order_sales = $this->getOrderbyCardId($card_id);
        $status = $order_sales->getStatus();
        $order_sales->addStatusHistoryComment($comment, $status);
        $order_sales->save();
        /** notify by email
         */
        $email_notify = $this->scopeConfigObject->getValue('trello/webhooks/emailnotify');
        if ($email_notify == 1) { 
           $this->objectComment->send($order_sales, $notify='1' , $comment);
        }
    }

    /*
     * get the orde by card by using model('trello/card')
     * @param varchar :card_id Trello card id 
     */

    public function getOrderbyCardId($card_id) {
        $cardcollection = $this->cardFactory->create()->getCollection()->addFieldToFilter('card_id', $card_id);
        $order_id = $cardcollection->getFirstItem()->getOrderId();
        $order_sales = $this->orderFactory->create()->load($order_id);
        return $order_sales;
    }

}