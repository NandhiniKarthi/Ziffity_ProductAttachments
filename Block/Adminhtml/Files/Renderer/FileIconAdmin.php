<?php

namespace Ziffity\ProductAttachments\Block\Adminhtml\Files\Renderer;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Ziffity\ProductAttachments\Helper\Data;

class FileIconAdmin extends AbstractElement
{
    private $assetRepo;
    private $dataHelper;
    private $helper;
    private $urlBuilder;
    private $coreRegistry = null;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        Repository $assetRepo,
        Data $dataHelper,
        Data $helper,
        UrlInterface $urlBuilder,
        Registry $registry
    ) {
        $this->dataHelper = $dataHelper;
        $this->assetRepo = $assetRepo;
        $this->helper = $helper;
        $this->urlBuilder = $urlBuilder;
        $this->coreRegistry = $registry;
    }

    /**
     * getElementHtml
     *
     * @return void
     */
    public function getElementHtml()
    {
        $fileIcon = __('<h3>No File Uploaded</h3>');

        $file = $this->getValue();
        if ($file) {
            $fileExt = File::getPathInfo($file, PATHINFO_EXTENSION);
            if ($fileExt) {
                $iconImage = $this->assetRepo->getUrl(
                    'Ziffity_ProductAttachments::images/'.$fileExt.'.png'
                );
                $url = $this->dataHelper->getMediaUrl().'attachments/files/'.$file;
                $fileIcon = "<a href=".$url." target='_blank'>
                    <img src='".$iconImage."' style='float: left;' />
                    <div>Click Here</div></a>";
            } else {
                $iconImage = $this->assetRepo->getUrl('Ziffity_ProductAttachments::images/unknown.png');
                $fileIcon = "<img src='".$iconImage."' style='float: left;' />";
            }
        }
        return $fileIcon;
    }
}
