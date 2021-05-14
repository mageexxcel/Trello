<?php
namespace Excellence\Trello\Observer\Comment;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
 
class SalesOrderAddComment implements ObserverInterface
{
    
     public function __construct(
        \Magento\Framework\App\Request\Http $request,
         \Magento\Backend\Model\Auth\Session $authSession,
         \Excellence\Trello\Model\CardFactory $cardFactory ,
         \Excellence\Trello\Observer\CheckoutSubmitAllAfter $checkoutAll,
        \Excellence\Trello\Model\Trello $trelloFactory,
        \Magento\Sales\Model\Order\InvoiceFactory $InvoiceFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager     
    ) {
        $this->request = $request;
        $this->authSession = $authSession;
        $this->cardFactory = $cardFactory;
        $this->checkoutAll = $checkoutAll;
        $this->trelloFactory = $trelloFactory;
        $this->InvoiceFactory = $InvoiceFactory;
         $this->_objectManager= $objectManager;
    }

   

    public function execute(Observer $observer) {
        $post = $this->request->getPostValue('history');
        $this->saveStateComments($post, $state = 'history');
    }
    
    public function getEntityId() {
        $data = $this->request->getParams();
        $id = $data['id'];
        return $id;
    }

    public function getHistoryCardId() {
        $order = $this->request->getParams();
        $order_id = $order['order_id'];
        return $this->getCardId($order_id);
    }

    public function getInvoiceCardId() {
        $invoice = $this->InvoiceFactory->create()->load($this->getEntityId());
        $order_id = $invoice->getOrderId();
        return $this->getCardId($order_id);
    }

    public function getCredeitMemoCardId() {
        $creditmemo = $this->_objectManager->get('Magento\Sales\Model\Order\Creditmemo')->load($this->getEntityId());
        $order_id = $creditmemo->getOrderId();
        return $this->getCardId($order_id);
    }

    public function getShipmentCardId() { 
        $shipment = $this->_objectManager->get('Magento\Sales\Model\Order\Shipment')->load($this->getEntityId());
        $order_id = $shipment->getOrderId();
        return $this->getCardId($order_id);
    }

    /*
     * get card_id using order id
     */

    public function getCardId($order_id) {
        $card_model = $this->cardFactory->create()->getCollection()->addFieldToFilter('order_id', $order_id);
        $data = $card_model->getFirstItem();
        $card_id = $data['card_id'];
        return $card_id;
    }

     /*
     * save state comment 
     */

    public function saveStateComments($post, $state) {
        $postcomment = $post['comment'];
        if ($post && ($postcomment != '')) { 
            $postcomment.= $this->_getAppend();
             $value['text'] = $postcomment;
            switch ($state) {
                case "history":
                    $card_id = $this->getHistoryCardId();
                    break;
                case "invoice":
                    $card_id = $this->getInvoiceCardId();
                    break;
                case "creditmemo":
                    $card_id = $this->getCredeitMemoCardId();
                    break;
                case "shipment": 
                    $card_id = $this->getShipmentCardId();
                    break;
            }
            $this->saveAdminComments($value, $card_id);
        }
    }

    /*
     * save comment to trello for all admin comment 
     * @param varchar:$card_id
     * @param array: $value 
     */

    public function saveAdminComments($value, $card_id) {
    $active = $this->checkoutAll->checkActiveEnable();
       if ($active == true) {
           $this->trelloFactory->postCommentCards($value, $card_id);
       }
    }

    /*
     * append username to comment
     */

    protected function _getAppend() { 
         $user = $this->authSession->getUser()->getUsername();
        return "\n\r By: " . $user;
    }

    
 
}
