<?php
/**
 * Attachments
 *
 */

namespace Ziffity\ProductAttachments\Block\Adminhtml\Files\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Framework\View\Asset\Repository as AssetRepo;
use Ziffity\ProductAttachments\Block\Adminhtml\Files\Renderer\FileIconAdmin;

class General extends Generic implements TabInterface
{
    protected $_wysiwygConfig;
    protected $_newsStatus;
    protected $assetRepo;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        AssetRepo $assetRepo,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->assetRepo = $assetRepo;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * convertPHPSizeToBytes
     *
     * @param  mixed $sSize
     * @return void
     */
    private function convertPHPSizeToBytes($sSize)
    {
        if (is_numeric($sSize)) {
            return $sSize;
        }
        $sSuffix = substr($sSize, -1);
        $iValue = substr($sSize, 0, -1);
        switch (strtoupper($sSuffix)) {
            case 'P':
                $iValue *= 1024;
                // no break
            case 'T':
                $iValue *= 1024;
                // no break
            case 'G':
                $iValue *= 1024;
                // no break
            case 'M':
                $iValue *= 1024;
                // no break
            case 'K':
                $iValue *= 1024;
                break;
        }
        return $iValue;
    }

    /**
     * _prepareForm
     *
     * @return void
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('attachment');
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General')]
        );

        if ($model->getId()) {
            $fieldset->addField(
                'id',
                'hidden',
                ['name' => 'id',
                 'value' => $model->getId()
                ]
            );
        }
        if ($model->getData('path')) {
            $fieldset->addField(
                'path',
                'text',
                [
                    'name' => 'path',
                    'label'    => __('Path'),
                    'required' => false,
                    'readonly' => true
                ]
            );
        }
        $phpMaxSize = $this->convertPHPSizeToBytes(ini_get('upload_max_filesize'));
        $newValidationJs = "
			<script type='text/javascript'>
			require([
				'jquery',
				'jquery/ui',
				'jquery/validate',
				'mage/translate'
			], function ($) {
				$.validator.addMethod(
					'validate-filesize',
					function (v, elm) {
							var maxSize = ".$phpMaxSize.";
							if (navigator.appName == 'Microsoft Internet Explorer') {
								if (elm.value) {
									var oas = new ActiveXObject('Scripting.FileSystemObject');
									var e = oas.getFile(elm.value);
									var size = e.size;
								}
							} else {
								if (elm.files[0] != undefined) {
									size = elm.files[0].size;
								}
							}
							if (size != undefined && size > maxSize) {
								return false;
							}
							return true;
					},
					$.mage.__('The file size should not exceed ".ini_get('upload_max_filesize')."')
				);
			});
			</script>";

        $fieldset->addField(
            'type',
            'select',
            [
                'name' => 'type',
                'label'    => __('Type'),
                'required' => true,
                'values' => [
                    ['value'=>"1",'label'=>__('Regulatory')],
                    ['value'=>"2",'label'=>__('Formulary')]
                ]
            ]
        );

        $fieldset->addField(
            'file',
            'file',
            [
                'name' => 'file',
                'label'    => __('Upload File'),
                'required' => false,
                'class' => 'validate-filesize',
                'after_element_html' => $newValidationJs
            ]
        );
        $fieldset->addType(
            'uploadedfile',
            FileIconAdmin::class
        );
        $fieldset->addField(
            'basename',
            'uploadedfile',
            [
                'name'  => 'basename',
                'label' => __('Uploaded File'),
                'title' => __('Uploaded File'),
                'value' => $model->getBasename()
            ]
        );
        $fieldset->addField(
            'filename',
            'text',
            [
                'name' => 'filename',
                'label'    => __('Title'),
                'required' => true,
                'note' => __('Attachment Title')
            ]
        );
        $fieldset->addField(
            'active',
            'select',
            [
                'name' => 'active',
                'label'    => __('Active'),
                'required' => true,
                'values' => [
                    ['value'=>"1",'label'=>__('Yes')],
                    ['value'=>"0",'label'=>__('No')]
                ]
            ]
        );
        if (!$model->getData('sort')) {
            $model->setData('sort', 0);
        }
        $fieldset->addField(
            'sort',
            'text',
            [
                'name' => 'sort',
                'label'    => __('Sort'),
                'required' => true
            ]
        );
        if ($model->getId()) {
            $form->setValues($model->getData());
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }
    /**
     * getTabLabel
     *
     * @return void
     */
    public function getTabLabel()
    {
        return __('General');
    }
    /**
     * getTabTitle
     *
     * @return void
     */
    public function getTabTitle()
    {
        return __('General');
    }
    /**
     * canShowTab
     *
     * @return void
     */
    public function canShowTab()
    {
        return true;
    }
    /**
     * isHidden
     *
     * @return void
     */
    public function isHidden()
    {
        return false;
    }
}
