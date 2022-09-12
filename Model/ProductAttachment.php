<?php

namespace Ziffity\ProductAttachments\Model;

use Magento\Framework\Model\AbstractModel;

class ProductAttachment extends AbstractModel
{
    /**
     * _construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\ProductAttachment::class);
    }
}
