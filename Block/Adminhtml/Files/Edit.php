<?php
/**
 * Attachments
 *
 */

namespace Ziffity\ProductAttachments\Block\Adminhtml\Files;

use Magento\Backend\Block\Widget\Form\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

class Edit extends Container
{
    protected $_coreRegistry = null;
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }
    /**
     * _construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'file_id';
        $this->_blockGroup = 'Ziffity_ProductAttachments';
        $this->_controller = 'adminhtml_files';
        parent::_construct();
        if ($this->_isAllowedAction('Ziffity_ProductAttachments::manage')) {
            $this->buttonList->remove('reset');
            $this->buttonList->update('save', 'label', __('Save File'));
            $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                -100
            );
        } else {
            $this->buttonList->remove('save');
        }
        if ($this->_isAllowedAction('Ziffity_ProductAttachments::manage')) {
            $this->buttonList->update('delete', 'label', __('Remove File'));
        } else {
            $this->buttonList->remove('delete');
        }
    }
    /**
     * getHeaderText
     *
     * @return void
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('attachment')->getId()) {
            return __("Edit File '%1'", ($this->_coreRegistry->registry('attachment')->getId()));
        } else {
            return __('Upload File');
        }
    }
    /**
     * _isAllowedAction
     *
     * @param  mixed $resourceId
     * @return void
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
    /**
     * _getSaveAndContinueUrl
     *
     * @return void
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '']);
    }
    /**
     * _prepareLayout
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
			function toggleEditor() {
				if (tinyMCE.getInstanceById('general_content') == null) {
					tinyMCE.execCommand('mceAddControl', false, 'general_content');
				} else {
					tinyMCE.execCommand('mceRemoveControl', false, 'general_content');
				}
			};
		";
        return parent::_prepareLayout();
    }
}
