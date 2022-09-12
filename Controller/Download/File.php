<?php
/**
 * Attachments
 *
 */

namespace Ziffity\ProductAttachments\Controller\Download;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Ziffity\ProductAttachments\Model\FileFactory;

class File extends Action
{
    protected $fileFactory;
    protected $resultPageFactory;
    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        FileFactory $fileFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
    /**
     * execute
     *
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $file = $this->fileFactory->get()->load($id);
        if ($file->getId()) {
            $directory = $this->fileFactory->get();
            if (isfile($directory->getRoot().$file->getData('path'))) {
                header('Content-Type: application/download');
                header('Content-Disposition: attachment; filename="'.$file->getData('basename').'"');
                header("Content-Length: ".$file->getData('size'));
                fileread($directory->getRoot().$file->getData('path'));
                return;
            } else {
                $this->_forward('index', 'noroute', 'cms');
            }
        } else {
            $this->_forward('index', 'noroute', 'cms');
        }
    }
}
