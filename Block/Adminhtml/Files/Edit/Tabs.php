<?php
/**
 * Attachments
 *
 */

namespace Ziffity\ProductAttachments\Block\Adminhtml\Files\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

class Tabs extends WidgetTabs
{
    /**
     * _construct
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('file_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Product Attachment Information'));
    }
}
