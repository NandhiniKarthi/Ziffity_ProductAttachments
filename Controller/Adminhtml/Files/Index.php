<?php
/**
 * Attachments
 *
 */

namespace Ziffity\ProductAttachments\Controller\Adminhtml\Files;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    protected $_resultPageFactory;
    protected $_resultPage;

    /**
     * __construct
     *
     * @param  mixed $context
     * @param  mixed $resultPageFactory
     * @return void
     */
    public function __construct(Context $context, PageFactory $resultPageFactory)
    {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
    }

    /**
     * execute
     *
     * @return void
     */
    public function execute()
    {
        $this->_setPageData();
        return $this->getResultPage();
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
     * getResultPage
     *
     * @return void
     */
    public function getResultPage()
    {
        if (($this->_resultPage==null)) {
            $this->_resultPage = $this->_resultPageFactory->create();
        }
        return $this->_resultPage;
    }

    /**
     * _setPageData
     *
     * @return void
     */
    protected function _setPageData()
    {
        $resultPage = $this->getResultPage();
        $resultPage->setActiveMenu('Ziffity_ProductAttachments::manage');
        $resultPage->getConfig()->getTitle()->prepend((__('Product Attachment')));
        $resultPage->addBreadcrumb(__('Ziffity'), __('Product Attachment'));
        return $this;
    }
}
