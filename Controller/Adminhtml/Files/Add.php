<?php
/**
 * Attachments
 *
 */

namespace Ziffity\ProductAttachments\Controller\Adminhtml\Files;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class Add extends Action
{
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    /**
     * execute
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
