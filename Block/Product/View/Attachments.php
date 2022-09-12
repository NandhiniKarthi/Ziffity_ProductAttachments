<?php

namespace Ziffity\ProductAttachments\Block\Product\View;

use Magento\Catalog\Helper\Data;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Ziffity\ProductAttachments\Model\ResourceModel\File\CollectionFactory;

class Attachments extends Template
{
    protected $_collection;
    protected $connection;
    protected $_coreRegistry;
    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        Context $context,
        FormKey $formKey,
        Data $product,
        CollectionFactory $collectionFactory,
        ResourceConnection $resource,
        array $data = []
    ) {
        $this->connection = $resource->getConnection();
        $this->product = $product;
        $this->_collection = $collectionFactory;
        $this->formKey = $formKey;
        $this->setData('title', __('Regulatory'));

        parent::__construct($context, $data);
    }

    /**
     * getProductAttachment
     *
     * @return void
     */
    protected function getProductAttachment()
    {
        $_product = $this->product->getProduct();
        $select = $this->connection
            ->select()
            ->from($this->connection->getTableName('product_attachments_files_mapping'))
            ->reset(\Zend_Db_Select::COLUMNS)
            ->where('product_id=?', $_product->getId())
            ->columns(['attachment_id']);
        $attach_data = $this->connection->fetchAll($select);
        $ids = ($attach_data) ? array_column($attach_data, 'attachment_id') : '';
        $ids = ($ids) ? implode(',', $ids) : [];
        return $ids;
    }

    /**
     * getCollection
     *
     * @return void
     */
    public function getCollection()
    {
        $ids = $this->getProductAttachment();

        $collection = $this->_collection->create();
        $collection->addFieldToFilter('id', ['in'=>$ids]);
        $collection->addFieldToFilter('type', ['eq'=>'1']);
        $collection->addFieldToFilter('active', true);
        $collection->setOrder('sort', 'asc');

        return $collection;
    }

    /**
     * getFormKey
     *
     * @return void
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    /**
     * getFormAction
     *
     * @return void
     */
    public function getFormAction()
    {
        return $this->getUrl('product_attachments/form/regulatory', ['_secure' => true]);
    }
}
