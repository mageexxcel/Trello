<?php
namespace Excellence\Trello\Model\ResourceModel;
class Setupwizard extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('excellence_trello_setupwizard','id');
    }
}
