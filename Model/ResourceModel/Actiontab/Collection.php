<?php
namespace Excellence\Trello\Model\ResourceModel\Actiontab;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Excellence\Trello\Model\Actiontab','Excellence\Trello\Model\ResourceModel\Actiontab');
    }
}
