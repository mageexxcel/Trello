<?php
 
namespace Excellence\Trello\Controller\Adminhtml\Trello;

class Savesetupwizard extends \Magento\Backend\App\Action 
{
     public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Excellence\Trello\Model\SetupwizardFactory $setupwizardFactory , 
        \Magento\Framework\App\Request\Http $request ,
        \Excellence\Trello\Model\ActiontabFactory $actiontab,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory    
    ) {
        $this->request = $request;
        $this->_setupwizardFactory = $setupwizardFactory;  
        $this->resultJsonFactory = $resultJsonFactory;
        $this->actiontab = $actiontab;
        parent::__construct($context);
     }
   public function execute()
    {  
       $data = $this->request->getParams();
       $model= $this->_setupwizardFactory->create();
       $message=$model->setupDone($data);
       $modelAction = $this->actiontab->create();
       $modelAction->saveActionData();
       $this->messageManager->addSuccess(__('You are Done With Setup!'));
       $this->_redirect('*/rules/index');
    }
    
}
