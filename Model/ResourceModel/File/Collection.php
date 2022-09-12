<?php

namespace Ziffity\ProductAttachments\Model\ResourceModel\File;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Ziffity\ProductAttachments\Model\File;
use Ziffity\ProductAttachments\Model\ResourceModel\File as FileResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            File::class,
            FileResourceModel::class
        );
    }
}
