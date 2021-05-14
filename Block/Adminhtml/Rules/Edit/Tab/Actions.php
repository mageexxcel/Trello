<?php
namespace Excellence\Trello\Block\Adminhtml\Rules\Edit\Tab;
class Actions extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Excellence\Trello\Model\ActiontabFactory $actionFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = array()
    ) {
        $this->_systemStore = $systemStore;
        $this->actionFactory = $actionFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
		
        $model = $this->_coreRegistry->registry('trello_rules_conditions');
		$isElementDisabled = false;
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');
         
        $fieldset = $form->addFieldset('actions_section', array('legend' => __('Action')));

        $modelAction = $this->actionFactory->create();
        /*
         * get all labels of board
         */
        $option_labels =$modelAction->getLabelList();
      
        /*
         * get all members of board
         */
        $option_members = $modelAction->getBoardMember();

        /*
         * get all board's  list
         */
        $option_lists = $modelAction->getListBoard();

       $fieldset->addField('label', 'multiselect', array(
            'label' => __('Select Labels'),
            'class' => 'validation-one',
            'name' => 'label[]',
            'values' => $option_labels,
            'disabled' => false,
            'readonly' => false,
            'after_element_html' => '<small></small>',
            'tabindex' => 1
        ));

        $fieldset->addField('member', 'multiselect', array(
            'label' => __('Select Members'),
            'class' => 'validation-one',
           'name' => 'member[]',
            'values' => $option_members,
            'disabled' => false,
            'readonly' => false,
            'after_element_html' => '<small></small>',
            'tabindex' => 1
        ));

        $fieldset->addField('list', 'select', array(
            'label' => __('Select List'),
            'class' => 'validation-one',
            'name' => 'list',
            'values' => $option_lists,
            'after_element_html' => '<small></small>',
            'tabindex' => 1
        ));

        $fieldset->addField('priority', 'text', array(
            'label' => __('Priority'),
            'required' => true,
            'name' => 'priority',
        ));

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();   
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Actions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Actions');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
