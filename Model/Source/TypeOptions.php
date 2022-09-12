<?php

namespace Ziffity\ProductAttachments\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class TypeOptions implements OptionSourceInterface
{
    /**
     * Return select options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('Regulatory')],
            ['value' => '2', 'label' => __('Formulary')]
        ];
    }
}
