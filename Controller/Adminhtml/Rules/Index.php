<?php
namespace Excellence\Trello\Controller\Adminhtml\Rules;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;


class Index extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\Page
     */
    protected $resultPage;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Excellence\Trello\Model\SetupwizardFactory $setupwizardFactory,
        PageFactory $resultPageFactory
    )
    {   
        $this->setupwizardModel = $setupwizardFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {   
        $setupWizard = $this->setupwizardModel->create()->getCollection()->count();
        if ($setupWizard == 0) {
            $this->messageManager->addError(__('Setup Wizard is not done . First do Setup Wizard.'));
         } else {
              }
        
		$this->resultPage = $this->resultPageFactory->create();  
		$this->resultPage->setActiveMenu('Excellence_Rules::rules');
		$this->resultPage ->getConfig()->getTitle()->set((__('Rules')));
		return $this->resultPage;
       
        
    }
}
