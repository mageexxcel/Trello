<?php
 
namespace Excellence\Trello\Controller\Adminhtml\Trello;
 
class Webhook extends \Magento\Backend\App\Action
{
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Excellence\Trello\Model\SetupwizardFactory $setupwizardFactory , 
        \Magento\Framework\App\Request\Http $request ,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     ) { 
        $this->request = $request;
        $this->_setupwizardFactory = $setupwizardFactory;  
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
     }
   public function execute()
    {  
       $param = $this->request->getParams();
       $model= $this->_setupwizardFactory->create();
       if (isset($param['webhook'])) {
            $webhook_id = $param['webhook'];
            $model->saveWebookId($webhook_id);
        } else {
            $resultData= $model->getWebhookIdToken();
            $result = $this->resultJsonFactory->create();
            return $result->setData($resultData);
         }
    }
    
}
