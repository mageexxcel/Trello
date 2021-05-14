<?php

namespace Excellence\Trello\Controller\Adminhtml\Trello;

class User extends \Magento\Backend\App\Action {

    public function __construct(
      \Magento\Backend\App\Action\Context $context,
      \Excellence\Trello\Model\Trello $trello, 
      \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->trello = $trello;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute() {
        $resultData = $this->trello->getMemberDetail();
        $result = $this->resultJsonFactory->create();
        return $result->setData($resultData);
    }

}
