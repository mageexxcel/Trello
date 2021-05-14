<?php
namespace Excellence\Trello\Model\ResourceModel;
class Actiontab extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('excellence_trello_actiontab','tab_id');
    }
}
