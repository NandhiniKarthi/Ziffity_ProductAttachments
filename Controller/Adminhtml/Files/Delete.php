<?php
/**
 * Attachments
 *
 */

namespace Ziffity\ProductAttachments\Controller\Adminhtml\Files;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Ziffity\ProductAttachments\Model\File as AttachmentModel;

class Delete extends Action
{
    protected $_resultPageFactory;
    protected $_resultPage;
    protected $attachmentModel;

    /**
     * __construct
     *
     * @param  mixed $context
     * @param  mixed $resultPageFactory
     * @param  mixed $attachmentModel
     * @return void
     */
    public function __construct(Context $context, PageFactory $resultPageFactory, AttachmentModel $attachmentModel)
    {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->attachmentModel = $attachmentModel;
    }

    /**
     * execute
     *
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id>0) {
            $this->attachmentModel->load($id);
            try {
                $this->attachmentModel->delete();
                $this->messageManager->addSuccessMessage(__('Deleted the product attachment.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong.'));
            }
        }
        $this->_redirect('*/*');
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
}
