<?php
namespace Excellence\Trello\Block\Adminhtml\Rules\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
		
        parent::_construct();
        $this->setId('checkmodule_rules_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Trello Rules'));
    }

     protected function _beforeToHtml()
   {
       
        $this->addTab('actions_section', array(
            'label'     => __('Actions'),
            'content'   => $this->getLayout()->createBlock('Excellence\Trello\Block\Adminhtml\Rules\Edit\Tab\Actions')->toHtml(),
        ));

      return parent::_beforeToHtml();
  }
}