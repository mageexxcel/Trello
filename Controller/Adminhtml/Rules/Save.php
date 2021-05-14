<?php

namespace Excellence\Trello\Controller\Adminhtml\Rules;
use Magento\Framework\App\Filesystem\DirectoryList;
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
	public function execute()
    {
		
        $data = $this->getRequest()->getParams();
        if ($data) {
            $model = $this->_objectManager->create('Excellence\Trello\Model\Rules');
		
            
			$id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
            }
            /*
             * for rules 
           */

                if (isset($data['rule']['conditions'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                }
                if (isset($data['rule']['actions'])) {
                    $data['actions'] = $data['rule']['actions'];
                }

                unset($data['rule']);

			/*
            * for stores (store view)
            */
                if (isset($data['website_ids'])) {
                    if (in_array('0', $data['website_ids'])) {
                        $data['rule_website_ids'] = '0';
                    } else {
                        $data['rule_website_ids'] = implode(",", $data['website_ids']);
                    }

                   unset($data['website_ids']);
                }

            if (isset($data['label'])) {
                    $label_array = $data['label'];
                    $label_string = implode(',', $label_array);
                    $data['label'] = $label_string;
                } else {
                    $data['label'] = Null;
                }
                if (isset($data['member'])) {
                    $member_array = $data['member'];
                    $member_string = implode(',', $member_array);
                    $data['member'] = $member_string;
                } else {
                    $data['member'] = Null;
                }
      
            $model->loadPost($data);
         
			
            try {
                $model->save();
                $this->messageManager->addSuccess(__('The Trello Rule Has been Saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId(), '_current' => true));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the banner.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('banner_id' => $this->getRequest()->getParam('banner_id')));
            return;
        }
        $this->_redirect('*/*/');
    }
}
