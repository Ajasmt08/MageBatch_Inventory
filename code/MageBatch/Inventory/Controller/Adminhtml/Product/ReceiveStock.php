<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use MageBatch\Inventory\Api\StockReceivingInterface;

class ReceiveStock extends Action
{
    const ADMIN_RESOURCE = 'MageBatch_Inventory::inventory_receive';

    public function __construct(
        Context $context,
        private ProductRepositoryInterface $productRepository,
        private StockReceivingInterface $stockReceiving,
        private JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        try {
            $productId = (int)$this->getRequest()->getParam('product_id');
            $sourceCode = (string)$this->getRequest()->getParam('source_code', 'default');
            $batchNumber = (string)$this->getRequest()->getParam('batch_number');
            $qty = (float)$this->getRequest()->getParam('qty', 0);
            $expiryDate = (string)$this->getRequest()->getParam('expiry_date');
            $manufacturingDate = $this->getRequest()->getParam('manufacturing_date');
            $supplier = $this->getRequest()->getParam('supplier');
            $purchaseOrder = $this->getRequest()->getParam('purchase_order');
            $costPrice = $this->getRequest()->getParam('cost_price');
            $notes = $this->getRequest()->getParam('notes');

            $product = $this->productRepository->getById($productId);
            $sku = $product->getSku();

            $this->stockReceiving->receive(
                $sku,
                $sourceCode,
                $batchNumber,
                $qty,
                $expiryDate,
                !empty($manufacturingDate) ? $manufacturingDate : null,
                !empty($supplier) ? $supplier : null,
                !empty($purchaseOrder) ? $purchaseOrder : null,
                !empty($costPrice) ? (float)$costPrice : null,
                !empty($notes) ? $notes : null
            );

            return $result->setData([
                'success' => true,
                'message' => (string)__('Stock received successfully. %1 units of %2 added to batch %3 at %4.',
                    $qty, $sku, $batchNumber, $sourceCode),
            ]);
        } catch (\Exception $e) {
            return $result->setData([
                'success' => false,
                'message' => (string)$e->getMessage(),
            ]);
        }
    }
}
