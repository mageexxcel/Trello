<?php
namespace Excellence\Trello\Controller\Index;
 
 
class WebhookBoard extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Excellence\Trello\Model\Webhook $webhook
        )
    { 
        $this->webhook = $webhook; 
        return parent::__construct($context);
    }
     
    public function execute()
    {  
      $json = file_get_contents('php://input');
      $this->webhook->webhookComment($json);
     } 
}

