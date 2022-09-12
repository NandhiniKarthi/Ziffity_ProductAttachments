<?php
/**
 * Attachments
 *
 */

namespace Ziffity\ProductAttachments\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\View\LayoutFactory;
use Ziffity\ProductAttachments\Block\Adminhtml\Catalog\Product\Edit\Tab\Attachments;

class Grid extends Action
{
    protected $resultRawFactory;
    protected $layoutFactory;
    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        Context $context,
        RawFactory $resultRawFactory,
        LayoutFactory $layoutFactory
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
    }
    /**
     * execute
     *
     * @return void
     */
    public function execute()
    {
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                Attachments::class,
                'catalog.product.attachments.grid'
            )->toHtml()
        );
    }
    /**
     * _isAllowed
     *
     * @return void
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ziffity_ProductAttachments::index');
    }
}
