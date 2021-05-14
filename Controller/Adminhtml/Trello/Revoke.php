<?php

namespace Excellence\Trello\Controller\Adminhtml\Trello;

class Revoke extends \Magento\Backend\App\Action {

    public function __construct(
    \Magento\Backend\App\Action\Context $context,
    \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, 
    \Excellence\Trello\Model\SetupwizardFactory $setupwizardFactory
    ) {
        $this->_setupwizardFactory = $setupwizardFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute() {
        $model = $this->_setupwizardFactory->create();
        $model->setupRemove();
        $resultData = $model->getWebhookIdToken();
        $result = $this->resultJsonFactory->create();
        return $result->setData($resultData);
      }

}
