<?php

namespace Ziffity\ProductAttachments\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ProductAttachment extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('product_attachments_files_mapping', 'id');
    }
}
