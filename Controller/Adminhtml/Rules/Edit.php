<?php
namespace Excellence\Trello\Controller\Adminhtml\Rules;

class Edit extends \Magento\CatalogRule\Controller\Adminhtml\Promo\Catalog
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
	public function execute()
    {
		//  Get ID and create model
        $id = $this->getRequest()->getParam('id');
		
        $model = $this->_objectManager->create('Excellence\Trello\Model\Rules');
		
		$registryObject = $this->_objectManager->get('Magento\Framework\Registry');
		
        //  Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This row no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }
        // Set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $model->getConditions()->setFormName('sales_rule_form');
        $model->getConditions()->setJsFormObject(
            $model->getConditionsFieldSetId($model->getConditions()->getFormName())
        );

       $registryObject->register('trello_rules_conditions', $model);
        
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Trello Rule'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getId() ? $model->getName() : __('New Trello Rule')
        );

	
        $this->_view->renderLayout();
    }
}
