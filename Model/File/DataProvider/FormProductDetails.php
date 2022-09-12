<?php

namespace Ziffity\ProductAttachments\Model\File\DataProvider;

use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Api\Data\ProductInterface;

class FormProductDetails
{
    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var Image
     */
    private $imageHelper;

    /**
     * @var Price
     */
    private $priceModifier;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        ProductCollectionFactory $productCollectionFactory,
        Image $imageHelper
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->imageHelper = $imageHelper;
    }

    /**
     * @param array $fileData
     */
    public function addProductDetails(&$fileData)
    {
        $productsArr = explode(",", $fileData['products']);
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addIdFilter($productsArr)
            ->addAttributeToSelect(['status', 'thumbnail', 'name', 'price'], 'left');

        $fileData['fileproducts']['products'] = [];

        /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
        foreach ($productCollection->getItems() as $product) {
            $fileData['fileproducts']['products'][] = $this->fillProductData($product);
        }

        unset($fileData['products']);
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     *
     * @return array
     */
    private function fillProductData(ProductInterface $product)
    {
        return [
            'entity_id' => $product->getId(),
            'thumbnail' => $this->imageHelper->init($product, 'product_listing_thumbnail')->getUrl(),
            'name' => $product->getName(),
            'status' => $product->getStatus(),
            'type_id' => $product->getTypeId(),
            'sku' => $product->getSku(),
            'price' => $product->getPrice() ? $product->getPrice() : ''
        ];
    }
}
