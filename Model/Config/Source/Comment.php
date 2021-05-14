<?php

namespace Excellence\Trello\Model\Config\Source;

class Comment implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 2, 'label' => __('All Comments')],['value' => 1, 'label' => __('Only @magento_bot mentions')], ['value' => 0, 'label' => __('Disabled')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [0 => __('Disabled'), 1 => __('Only @magento_bot mentions'), 2 => __('All Comments')];
    }
}
