<?php

namespace Ziffity\ProductAttachments\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class File extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('product_attachments_files', 'id');
    }
}
