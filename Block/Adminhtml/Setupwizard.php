<?php

namespace Excellence\Trello\Block\Adminhtml;

class Setupwizard extends \Magento\Backend\Block\Widget\Form\Container {

    public function __construct(
    \Magento\Backend\Block\Widget\Context $context, \Excellence\Trello\Model\SetupwizardFactory $setupwizardFactory, \Magento\Sales\Model\Order\Config $statuslists
    ) {
        $this->statuslist = $statuslists;
        $this->_setupwizardFactory = $setupwizardFactory;
        parent::__construct($context);
    }

    protected function _prepareLayout() {
        
    }

    public function statusList() {
        return $this->statuslist->getStatuses();
    }

    public function getToken() {
        $model = $this->_setupwizardFactory->create();
        return $model->getToken();
    }

    public function getActionUrl() {
        return $setupurl = $this->_urlBuilder->getUrl("trello/trello/savesetupwizard/");
    }

    public function getActionBoardUrl() {
        return $setupurl = $this->_urlBuilder->getUrl("trello/trello/board/");
    }

    public function getActionUserUrl() {
        return $setupurl = $this->_urlBuilder->getUrl("trello/trello/user/");
    }

    public function getBoardListActionUrl() {
        return $setupurl = $this->_urlBuilder->getUrl("trello/trello/boardlist/");
    }

    public function getWebhookActionUrl() {
        return $setupurl = $this->_urlBuilder->getUrl("trello/trello/webhook/");
    }

    public function getBaseUrl() {
        return $setupurl = $this->_urlBuilder->getBaseUrl();
    }

    public function getSetupCollection() {
        $model = $this->_setupwizardFactory->create();
        return $model->getCollection();
    }

    public function getKeyValue() {
        $model = $this->_setupwizardFactory->create();
        return $model->getKeyValue();
    }

}
