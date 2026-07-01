<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Controller\Result\JsonFactory;

class Search extends Action
{
    const ADMIN_RESOURCE = 'Magento_Catalog::products';

    public function __construct(
        Context $context,
        private CollectionFactory $productCollectionFactory,
        private JsonFactory $resultJsonFactory,
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $searchKey = $this->getRequest()->getParam('searchKey');
        $pageNum = (int)$this->getRequest()->getParam('page', 1);
        $limit = (int)$this->getRequest()->getParam('limit', 30);

        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect(ProductInterface::NAME);
        $collection->addAttributeToFilter([
            ['attribute' => ProductInterface::NAME, 'like' => '%' . $searchKey . '%'],
            ['attribute' => ProductInterface::SKU, 'like' => '%' . $searchKey . '%'],
        ]);
        $collection->setFlag('has_stock_status_filter', false);
        $collection->setPage($pageNum, $limit);

        $totalValues = $collection->getSize();
        $products = [];
        foreach ($collection as $product) {
            $products[$product->getId()] = [
                'value' => $product->getId(),
                'label' => $product->getName(),
                'is_active' => $product->getStatus(),
                'path' => $product->getSku(),
                'optgroup' => false,
            ];
        }

        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData([
            'options' => $products,
            'total' => empty($products) ? 0 : $totalValues,
        ]);
    }
}