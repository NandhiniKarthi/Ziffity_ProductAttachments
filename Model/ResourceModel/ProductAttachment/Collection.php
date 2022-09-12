<?php

namespace Ziffity\ProductAttachments\Model\ResourceModel\ProductAttachment;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Ziffity\ProductAttachments\Model\ProductAttachment;
use Ziffity\ProductAttachments\Model\ResourceModel\ProductAttachment as ProductAttachmentResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            ProductAttachment::class,
            ProductAttachmentResourceModel::class
        );
    }
}
