<?php
/**
 * Attachments
 *
 */

namespace Ziffity\ProductAttachments\Controller\Adminhtml\Files;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Ziffity\ProductAttachments\Model\File;

class Edit extends Action
{
    protected $_coreRegistry = null;
    protected $resultPageFactory;
    protected $attachmentModel;
    protected $session;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        File $attachmentModel,
        Session $session,
        Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->attachmentModel = $attachmentModel;
        $this->session = $session;
        parent::__construct($context);
    }

    /**
     * _isAllowed
     *
     * @return void
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ziffity_ProductAttachments::manage');
    }

    /**
     * _initAction
     *
     * @return void
     */
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ziffity_ProductAttachments::manage')
            ->addBreadcrumb(__('Product Attachments'), __('Files'))
            ->addBreadcrumb(__('Edit'), __('Edit'));
        return $resultPage;
    }

    /**
     * execute
     *
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $this->attachmentModel->load($id);
            if (!$this->attachmentModel->getId()) {
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('attachment', $this->attachmentModel);

        $resultPage = $this->_initAction();
        if ($id) {
            $resultPage->getConfig()->getTitle()->prepend(__('Edit - ').$this->attachmentModel->getFilename());
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Product Attachment'));
        }
        return $resultPage;
    }
}
