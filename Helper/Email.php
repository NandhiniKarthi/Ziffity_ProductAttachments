<?php

namespace Ziffity\ProductAttachments\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Ziffity\ProductAttachments\Model\Mail\Template\TransportBuilder;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\AbstractHelper;

class Email extends AbstractHelper
{
    public const XML_PATH_EMAIL_TEMPLATE_FIELD  = 'regulatory_document/general/email_templates';

    protected $_storeManager;
    protected $inlineTranslation;
    protected $_transportBuilder;
    protected $scopeConfig;
    protected $fileSystem;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Context $context,
        StoreManagerInterface $storeManager,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        Filesystem $fileSystem
        //File $reader
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->logger = $context->getLogger();
        $this->fileSystem = $fileSystem;
    }

    /**
     * getConfigValue
     *
     * @param  mixed $path
     * @param  mixed $storeId
     * @return void
     */
    protected function getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * getStore
     *
     * @return void
     */
    public function getStore()
    {
        return $this->_storeManager->getStore();
    }

    /**
     * getTemplateId
     *
     * @param  mixed $xmlPath
     * @return void
     */
    public function getTemplateId($xmlPath)
    {
        return $this->getConfigValue($xmlPath, $this->getStore()->getStoreId());
    }

    /**
     * sendEmail
     *
     * @param  mixed $emailTemplateVariables
     * @param  mixed $receiver
     * @param  mixed $file
     * @return void
     */
    public function sendEmail($emailTemplateVariables, $receiver, $file)
    {
        $this->inlineTranslation->suspend();

        $sender = ['name' => 'Presperse','email' => 'support@presperse.com'];

        $emailTemplate = $this->getTemplateId(self::XML_PATH_EMAIL_TEMPLATE_FIELD);

        $this->_transportBuilder->setTemplateIdentifier($emailTemplate)
                ->setTemplateOptions(
                    [
                        'area' => Area::AREA_FRONTEND,
                        'store' => $this->_storeManager->getStore()->getId(),
                    ]
                )
                ->setTemplateVars($emailTemplateVariables)
                 ->setFrom($sender)
                ->addTo($receiver['email']);

        if ($file) {
            $mediaPath = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
            $file_path = $mediaPath.'attachments/files/'.$file;
            //$file_path = '/app/pub/media/attachments/files/'.$file;

            $this->_transportBuilder->addAttachment(url_get_contents($file_path), $file, null);
            $transport = $this->_transportBuilder->getTransport();
        }

        //create the transport
        if (!isset($transport)) {
            $transport = $this->_transportBuilder->getTransport();
        }

        try {
            $transport->sendMessage();
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }

        $this->inlineTranslation->resume();
        return true;
    }
}
