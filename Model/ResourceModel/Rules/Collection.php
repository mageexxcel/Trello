<?php

namespace Excellence\Trello\Model\ResourceModel\Rules;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Excellence\Trello\Model\Rules', 'Excellence\Trello\Model\ResourceModel\Rules');
    }
}
