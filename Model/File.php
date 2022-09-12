<?php

namespace Ziffity\ProductAttachments\Model;

use Exception;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Driver\File as MagentoFile;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;

class File extends AbstractModel
{
    protected $urlInterface;
    protected $directoryList;
    protected $fileSystem;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        Context                $context,
        Registry               $registry,
        UrlInterface           $urlInterface,
        DirectoryList          $directoryList,
        MagentoFile            $fileSystem
            ) {
        $this->urlInterface = $urlInterface;
        $this->directoryList = $directoryList;
        $this->fileSystem = $fileSystem;
        parent::__construct($context, $registry);
    }

    /**
     * getUrl
     *
     * @return void
     */
    public function getUrl()
    {
        return $this->urlInterface->getUrl(
            'attachments/download/file',
            [
                    'id' => $this->getData('id')
                ]
        ) . $this->getData('basename');
    }

    /**
     * afterDelete
     *
     * @return void
     */
    public function afterDelete()
    {
        $io = $this->fileSystem;
        try {
            $file = $this->directoryList->getRoot() . '/media/attachments/files/' . $this->getData('file');

            if ($this->fileSystem->isExists($file)) {
                $this->fileSystem->deleteFile($file);
            }
        } catch (Exception $e) {
            $e->addErrorMessage("Does not exsist");
        }
        parent::afterDelete();
    }

    /**
     * _construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\File::class);
    }
}
