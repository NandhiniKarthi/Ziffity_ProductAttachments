<?php

namespace Ziffity\ProductAttachments\Model\File\DataProvider;

use Ziffity\ProductAttachments\Model\File as Attachment;
use Ziffity\ProductAttachments\Model\ResourceModel\File\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\File\Size;
use Magento\Backend\Model\UrlInterface;
use Ziffity\ProductAttachments\Helper\Data;
use Magento\Framework\Exception\NoSuchEntityException;

class Form extends AbstractDataProvider
{
    /**
     * @var array
     */
    protected $loadedData;
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var Repository
     */
    private $repository;
    /**
     * @var \Magento\Framework\File\Size
     */
    private $fileSize;
    /**
     * @var FormProductDetails
     */
    private $formProductDetails;
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    private $url;
    /**
     * @var FileInterface
     */
    private $file;
    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        CollectionFactory $fileCollectionFactory,
        Attachment $repository,
        RequestInterface $request,
        DataPersistorInterface $dataPersistor,
        FormProductDetails $formProductDetails,
        Size $fileSize,
        UrlInterface $url,
        Data $helper,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $fileCollectionFactory->create();
        $this->request = $request;
        $this->repository = $repository;
        $this->fileSize = $fileSize;
        $this->formProductDetails = $formProductDetails;
        $this->dataPersistor = $dataPersistor;
        $this->url = $url;
        $this->helper = $helper;
    }
    /**
     * @return array
     */
    public function getData()
    {
        $data = parent::getData();
        if ($data['totalRecords'] > 0) {
            $fileData = $this->file->getData();
            if ($this->file->getPath()) {
                $fileData['file'] = [
                    [
                        'name' => $fileData['basename'],
                        'url'  => $this->helper->getAttachmentUrlByName($this->file->getBasename()),
                        'previewUrl'  => $this->helper->getPreviewIcon($this->file->getExtension()),
                        'previewType' => 'image',
                        'size' => $this->file->getSize()
                    ]
                ];
            }
            if (!empty($fileData['products'])) {
                $this->formProductDetails->addProductDetails($fileData);
            }

            $data[$this->file->getId()] = $fileData;
        }
        if ($savedData = $this->dataPersistor->get('fileData')) {
            $savedFileId = isset($savedData['id']) ? $savedData['id'] : null;
            if (isset($data[$savedFileId])) {
                $data[$savedFileId] = array_merge($data[$savedFileId], $savedData);
            } else {
                $data[$savedFileId] = $savedData;
            }
            $this->dataPersistor->clear('fileData');
        }
        return $data;
    }
    /**
     * getMeta
     *
     * @return void
     */
    public function getMeta()
    {
        $meta = parent::getMeta();
        $this->data['config']['submit_url'] = $this->url->getUrl('*/*/save', ['_current' => true]);
        $meta['general']['children']['file']['arguments']['data']['config']['maxFileSize'] =
            $this->fileSize->getMaxFileSize();
        $meta['general']['children']['file']['arguments']['data']['config']['allowedExtensions'] =
        'pdf, xls, doc, docx, csv, png, gif, jpg';

        $fileId = (int)$this->request->getParam('id');
        $store = (int)$this->request->getParam('store');
        if ($fileId) {
            try {
                $this->file = $this->repository->load($fileId);
            } catch (NoSuchEntityException $e) {
                null;
            }
        }
        return $meta;
    }
}
