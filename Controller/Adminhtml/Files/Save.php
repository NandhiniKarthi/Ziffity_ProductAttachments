<?php
/**
 * Attachments
 *
 */

namespace Ziffity\ProductAttachments\Controller\Adminhtml\Files;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\View\Result\PageFactory;
use Ziffity\ProductAttachments\Model\File as AttachmentModel;
use Ziffity\ProductAttachments\Model\ProductAttachment;
use Ziffity\ProductAttachments\Model\ResourceModel\File;
use Ziffity\ProductAttachments\Model\Uploader;

class Save extends Action
{
    protected $resultPageFactory;
    protected $attachmentFactory;
    protected $imageUploader;
    protected $dataPersistor;
    protected $productAttachment;
    protected $connection;
    protected $resource;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        AttachmentModel $attachmentFactory,
        DataPersistorInterface $dataPersistor,
        ProductAttachment $productAttachment,
        ResourceConnection $resourceConnection,
        Uploader $imageUploader,
        File $resource
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->dataPersistor = $dataPersistor;
        $this->attachmentFactory = $attachmentFactory;
        $this->imageUploader = $imageUploader;
        $this->productAttachment = $productAttachment;
        $this->connection = $resourceConnection->getConnection();
        $this->resource = $resource;
    }

    /**
     * execute
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');
        $model = $this->attachmentFactory;
        $older = false;
        $basename = false;
        $products = [];

        if ($id) {
            $this->resource->load($id);
            $older = $model->getData('products');
            $basename = $model->getData('basename');
        }

        if (isset($data['fileproducts']['products'])) {
            $products = array_column($data['fileproducts']['products'], 'entity_id');
            $data["products"] = implode(",", $products);
        } else {
            $data['products'] = '';
        }

        try {
            if (isset($data['file'][0]['name'])) {
                $fileInfo = $data['file'][0];
                $data['file'] = $fileInfo['name'];
                $data['extension'] = $fileInfo['file_extension'];
                $data['size'] = $fileInfo['size'];
                $data['path'] = $fileInfo['url'];
            } else {
                unset($data['file']);
            }

            $model->setData($data);
            $this->resource->save($model);
            $attachment_id = $model->getId();

            // Attachment mapping
            $tableName = $this->connection->getTableName('product_attachments_files_mapping');
            if ($older) {
                //$products = array_column($data['products'],'entity_id')

                $productArr = $products;
                $olderProductsArr = explode(",", $older);

                $newProductArr = array_diff($productArr, $olderProductsArr);
                $newProductArr = array_filter($newProductArr);
                foreach ($newProductArr as $product) {
                    $data = ['attachment_id' => $attachment_id,'product_id' => $product ];
                    $this->connection->insert($tableName, $data);
                }

                $deleteProductArr = array_diff($olderProductsArr, $productArr);
                $deleteProductArr = array_filter($deleteProductArr);
                foreach ($deleteProductArr as $product) {
                    $sql = "DELETE  FROM $tableName WHERE product_id=$product AND attachment_id=$attachment_id";
                    $this->connection->query($sql);
                }
            } else {
                //$products = isset($data["products"])?$data['products']:'';
                if ($products) {
                    $productArr = array_filter($products);

                    foreach ($productArr as $product) {
                        $data = ['attachment_id' => $attachment_id,'product_id' => $product ];
                        $this->connection->insert($tableName, $data);
                    }
                }
            }

            $this->messageManager->addSuccessMessage(__('Attachment Saved Successfully.'));
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong.'));
            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
            }
            return $resultRedirect->setPath('*/*/');
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong.'));
        }

        $data = $model->getData();
        if (!isset($data['basename'])) {
            $data['basename'] = $basename;
        }
        $this->_getSession()->setFormData($data);

        //check for `back` parameter
        if ($this->getRequest()->getParam('back')) {
            return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
        }

        return $resultRedirect->setPath('*/*/');
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
}
