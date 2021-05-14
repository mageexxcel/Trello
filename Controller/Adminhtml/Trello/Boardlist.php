<?php

namespace Excellence\Trello\Controller\Adminhtml\Trello;

class Boardlist extends \Magento\Backend\App\Action {

    public function __construct(
      \Magento\Backend\App\Action\Context $context,
      \Excellence\Trello\Model\Trello $trello,
      \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
      \Magento\Framework\App\Request\Http $request
    ) {
        $this->trello = $trello;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->request = $request;
        parent::__construct($context);
    }

    public function execute() {
        $board_id = $this->request->getParams();
        $resultData = $this->trello->getBoardLists($board_id['id']);
        $result = $this->resultJsonFactory->create();
        return $result->setData($resultData);
    }

}
