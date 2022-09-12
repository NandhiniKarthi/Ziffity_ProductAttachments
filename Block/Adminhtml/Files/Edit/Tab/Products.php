<?php

namespace Ziffity\ProductAttachments\Block\Adminhtml\Files\Edit\Tab;

use Ziffity\ProductAttachments\Model\FileFactory;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class Products extends Extended
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var AttachmentFactory
     */
    private $attachmentFactory;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        FileFactory $attachmentFactory,
        CollectionFactory $productCollectionFactory,
        array $data = []
    ) {
        $this->attachmentFactory = $attachmentFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

            /**
             * _construct
             * @return void
             */
    public function _construct()
    {
        parent::_construct();
        $this->setId('productsGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('id')) {
            $this->setDefaultFilter(['in_product' => 1]);
        }
    }

            /**
             * @param \Magento\Backend\Block\Widget\Grid\Column $column
             * @return $this
             * @throws \Magento\Framework\Exception\LocalizedException
             */
    public function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_product') {
            $productIds = $this->_getSelectedProducts();

            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

            /**
             * prepare collection
             */
    public function _prepareCollection()
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('sku');
        $collection->addAttributeToSelect('price');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

            /**
             * @return $this
             * @throws \Exception
             */
    public function _prepareColumns()
    {
        $this->addColumn(
            'in_product',
            [
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'name' => 'in_product',
                'align' => 'center',
                'index' => 'entity_id',
                'values' => $this->_getSelectedProducts(),
            ]
        );

        $this->addColumn(
            'entity_id',
            [
                'header' => __('Product ID'),
                'type' => 'number',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name',
                'class' => 'product-name',
                'width' => '50px',
            ]
        );
        $this->addColumn(
            'sku',
            [
                'header' => __('Sku'),
                'index' => 'sku',
                'class' => 'product-sku',
                'width' => '50px',
            ]
        );
        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'type' => 'currency',
                'index' => 'price',
                'width' => '50px',
            ]
        );

        return parent::_prepareColumns();
    }

            /**
             * @return string
             */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/productsgrid', ['_current' => true]);
    }

            /**
             * @param  object $row
             * @return string
             */
    public function getRowUrl($row)
    {
        return '';
    }

    /**
     * _getSelectedProducts
     *
     * @return void
     */
    public function _getSelectedProducts()
    {
        $model = $this->getAttachmentModel();
        $selected = $model->getProducts();

        if (!is_array($selected)) {
            if (strpos($selected, '&') !== 0) {
                $selected = explode("&", $selected);
            }
        }

        return $selected;
    }

            /**
             * Retrieve selected products
             *
             * @return array
             */
    public function getSelectedProducts()
    {
        $model = $this->getAttachmentModel();
        $selected = $model->getProducts();

        if (!is_array($selected)) {
            if (strpos($selected, '&') !== 0) {
                $selected = explode("&", $selected);
            }
        }
        return $selected;
    }

    /**
     * getAttachmentModel
     *
     * @return void
     */
    public function getAttachmentModel()
    {
        $attachmentId = $this->getRequest()->getParam('id');
        $model   = $this->attachmentFactory->create();
        if ($attachmentId) {
            $model->load($attachmentId);
        }
        return $model;
    }

            /**
             * {@inheritdoc}
             */
    public function canShowTab()
    {
        return true;
    }

            /**
             * {@inheritdoc}
             */
    public function isHidden()
    {
        return true;
    }
}
