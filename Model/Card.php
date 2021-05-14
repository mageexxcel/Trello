<?php
namespace Excellence\Trello\Model;
class Card extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'excellence_trello_card';

    protected function _construct()
    {
        $this->_init('Excellence\Trello\Model\ResourceModel\Card');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
    
    
}
