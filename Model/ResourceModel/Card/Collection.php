<?php
namespace Excellence\Trello\Model\ResourceModel\Card;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Excellence\Trello\Model\Card','Excellence\Trello\Model\ResourceModel\Card');
    }
}
