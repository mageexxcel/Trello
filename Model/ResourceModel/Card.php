<?php
namespace Excellence\Trello\Model\ResourceModel;
class Card extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('excellence_trello_card','entity_id');
    }
}
