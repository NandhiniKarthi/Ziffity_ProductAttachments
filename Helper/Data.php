<?php

namespace Ziffity\ProductAttachments\Helper;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\View\Asset\Repository;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    private $backendUrl;
    protected $storeManager;
    protected $assetRepo;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        Context $context,
        UrlInterface $backendUrl,
        Repository $assetRepo,
        StoreManagerInterface $storeManager
    ) {
        $this->backendUrl = $backendUrl;
        $this->assetRepo = $assetRepo;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * getConfigValue
     *
     * @param  mixed $field
     * @param  mixed $storeId
     * @return void
     */
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue('zy_attachments/'.$field, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * getProductsGridUrl
     *
     * @return void
     */
    public function getProductsGridUrl()
    {
        return $this->backendUrl->getUrl('zy_attachments/files/products', ['_current' => true]);
    }

    /**
     * getMediaUrl
     *
     * @return void
     */
    public function getMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * getPreviewIcon
     *
     * @param  mixed $extension
     * @return void
     */
    public function getPreviewIcon($extension)
    {
        if ($extension) {
            $iconImage = $this->assetRepo->getUrl(
                'Ziffity_ProductAttachments::images/'.$extension.'.png'
            );
            /* $url = $this->getMediaUrl().'attachments/files/'.$file;
             $fileIcon = "<a href=".$url." target='_blank'>
              <img src='".$iconImage."' style='float: left;' />
              <div>Click Here</div></a>";*/
        } else {
            $iconImage = $this->assetRepo->getUrl('Ziffity_ProductAttachments::images/unknown.png');
            //$fileIcon = "<img src='".$iconImage."' style='float: left;' />";
        }
        return $iconImage;
    }

    /**
     * getAttachmentUrlByName
     *
     * @param  mixed $name
     * @return void
     */
    public function getAttachmentUrlByName($name)
    {
        ;
    }
}
