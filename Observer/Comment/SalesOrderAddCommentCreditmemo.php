<?php
namespace Excellence\Trello\Observer\Comment;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
 
class SalesOrderAddCommentCreditmemo implements ObserverInterface
{
    
     public function __construct(
        \Magento\Framework\App\Request\Http $request,
       \Excellence\Trello\Observer\Comment\SalesOrderAddComment $salesAddComment
    ) {
        $this->request = $request;
         $this->salesAddComment = $salesAddComment;
    }

   

    public function execute(Observer $observer) {
        $post = $data = $this->request->getPostValue('comment');
        $this->salesAddComment->saveStateComments($post, $state = 'creditmemo');
    }
    
 
}
