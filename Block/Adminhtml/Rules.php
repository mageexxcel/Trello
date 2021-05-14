<?php
namespace Excellence\Trello\Block\Adminhtml;
class Rules extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
		
        $this->_controller = 'adminhtml_rules';
        $this->_blockGroup = 'Excellence_Trello';
        $this->_headerText = __('Rules');
        $this->_addButtonLabel = __('Add New Rules'); 
        parent::_construct();
		
    }
}