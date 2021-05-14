<?php
namespace Excellence\Trello\Model\ResourceModel\Setupwizard;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Excellence\Trello\Model\Setupwizard','Excellence\Trello\Model\ResourceModel\Setupwizard');
    }
}
