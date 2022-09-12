<?php
/**
 * Attachments
 *
 */
namespace Ziffity\ProductAttachments\Block\Adminhtml\Files\Edit;

use Magento\Backend\Block\Widget\Form\Generic;

class Form extends Generic
{

    protected $_systemStore;
    
    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    
    /**
     * _construct
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('file_form');
        $this->setTitle(__('Product Attachment'));
    }
    
    /**
     * _prepareForm
     *
     * @return void
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id'     => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post',
                    'enctype'=> 'multipart/form-data'
                ]
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
 
        return parent::_prepareForm();
    }
}
