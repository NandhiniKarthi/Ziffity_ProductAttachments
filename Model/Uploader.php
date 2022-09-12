<?php

namespace Ziffity\ProductAttachments\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\UrlInterface;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Ziffity\ProductAttachments\Helper\Data;

class Uploader
{
    private $coreFileStorageDatabase;
    private $mediaDirectory;
    private $uploaderFactory;
    private $storeManager;
    private $logger;
    public $baseTmpPath;
    public $basePath;
    public $allowedExtensions;
    protected $helper;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        Database $coreFileStorageDatabase,
        Filesystem $filesystem,
        UploaderFactory $uploaderFactory,
        StoreManagerInterface $storeManager,
        Data $helper,
        LoggerInterface $logger
    ) {
        $this->coreFileStorageDatabase = $coreFileStorageDatabase;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->uploaderFactory = $uploaderFactory;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->baseTmpPath = "attachments/";
        $this->basePath = "attachments/";
        $this->allowedExtensions= ['jpg', 'jpeg', 'gif', 'png','mp4','flv','pdf','doc'];
        $this->helper = $helper;
    }

    /**
     * setBaseTmpPath
     *
     * @param  mixed $baseTmpPath
     * @return void
     */
    public function setBaseTmpPath($baseTmpPath)
    {
        $this->baseTmpPath = $baseTmpPath;
    }

    /**
     * setBasePath
     *
     * @param  mixed $basePath
     * @return void
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * setAllowedExtensions
     *
     * @param  mixed $allowedExtensions
     * @return void
     */
    public function setAllowedExtensions($allowedExtensions)
    {
        $this->allowedExtensions = $allowedExtensions;
    }

    /**
     * getBaseTmpPath
     *
     * @return void
     */
    public function getBaseTmpPath()
    {
        return $this->baseTmpPath;
    }

    /**
     * getBasePath
     *
     * @return void
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * getAllowedExtensions
     *
     * @return void
     */
    public function getAllowedExtensions()
    {
        return $this->allowedExtensions;
    }

    /**
     * getFilePath
     *
     * @param  mixed $path
     * @param  mixed $imageName
     * @return void
     */
    public function getFilePath($path, $imageName)
    {
        return rtrim($path, '/') . '/' . ltrim($imageName, '/');
    }

    /**
     * moveFileFromTmp
     *
     * @param  mixed $imageName
     * @return void
     */
    public function moveFileFromTmp($imageName)
    {
        $baseTmpPath = $this->getBaseTmpPath();
        $basePath = $this->getBasePath();
        $baseImagePath = $this->getFilePath($basePath, $imageName);
        $baseTmpImagePath = $this->getFilePath($baseTmpPath, $imageName);

        try {
            $this->coreFileStorageDatabase->copyFile(
                $baseTmpImagePath,
                $baseImagePath
            );
            $this->mediaDirectory->renameFile(
                $baseTmpImagePath,
                $baseImagePath
            );
        } catch (\Exception $e) {
            throw new LocalizedException(
                __('Something went wrong while saving the file(s).')
            );
        }
        return $imageName;
    }

    /**
     * saveFileToTmpDir
     *
     * @param  mixed $folder
     * @param  mixed $fileId
     * @return void
     */
    public function saveFileToTmpDir($folder, $fileId)
    {
        $this->baseTmpPath = $this->baseTmpPath.$folder;
        $this->basePath = $this->basePath.$folder;
        $baseTmpPath = $this->getBaseTmpPath();
        $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
        $uploader->setAllowedExtensions($this->getAllowedExtensions());
        $uploader->setAllowRenameFiles(true);
        $result = $uploader->save($this->mediaDirectory->getAbsolutePath($baseTmpPath));
        if (!$result) {
            throw new LocalizedException(
                __('File can not be saved to the destination folder.')
            );
        }

        $result['tmp_name'] = str_replace('\\', '/', $result['tmp_name']);
        //$result['path'] = str_replace('\\', '/', $result['path']);
        $result['url'] = $this->storeManager
                ->getStore()
                ->getBaseUrl(
                    UrlInterface::URL_TYPE_MEDIA
                ) . $this->getFilePath($baseTmpPath, $result['file']);
        $result['name'] = $result['file'];
        $result['filename'] = File::getPathInfo($result['name'], PATHINFO_FILENAME);
        $result['file_extension'] = File::getPathInfo($result['name'], PATHINFO_EXTENSION);

        $result['previewUrl'] = $this->helper->getPreviewIcon($result['file_extension'], $result['name']);

        if (isset($result['file'])) {
            try {
                $relativePath = rtrim($baseTmpPath, '/') . '/' . ltrim($result['file'], '/');
                $this->coreFileStorageDatabase->saveFile($relativePath);
            } catch (\Exception $e) {
                $this->logger->critical($e);
                throw new LocalizedException(
                    __('Something went wrong while saving the file(s).')
                );
            }
        }
        return $result;
    }
}
