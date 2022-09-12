<?php

namespace Ziffity\ProductAttachments\Controller\Adminhtml\Files;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Ziffity\ProductAttachments\Model\Uploader;

class Upload extends Action
{
    public $imageUploader;

    public function __construct(
        Context $context,
        Uploader $imageUploader
    ) {
        parent::__construct($context);
        $this->imageUploader = $imageUploader;
    }

    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ziffity_ProductAttachments::manage');
    }

    public function execute()
    {
        try {
            $data = $this->getRequest()->getPostValue();
            $result = $this->imageUploader->saveFileToTmpDir('files', $data['param_name']);
            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
