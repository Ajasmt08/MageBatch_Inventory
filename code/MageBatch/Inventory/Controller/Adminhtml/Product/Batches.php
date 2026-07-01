<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Controller\Result\RawFactory;
use MageBatch\Inventory\Api\Data\BatchInterface;
use MageBatch\Inventory\Model\ResourceModel\Batch\CollectionFactory;

class Batches extends Action
{
    const ADMIN_RESOURCE = 'MageBatch_Inventory::inventory_view';

    public function __construct(
        Context $context,
        private ProductRepositoryInterface $productRepository,
        private CollectionFactory $batchCollectionFactory,
        private RawFactory $resultRawFactory
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultRawFactory->create();

        try {
            $productId = (int)$this->getRequest()->getParam('product_id');
            $product = $this->productRepository->getById($productId);
            $sku = $product->getSku();

            $collection = $this->batchCollectionFactory->create();
            $collection->addFieldToFilter(BatchInterface::PRODUCT_SKU, $sku);
            $collection->setOrder(BatchInterface::EXPIRY_DATE, \Magento\Framework\Data\Collection::SORT_ORDER_ASC);

            $html = $this->renderTable($collection);

            return $result->setContents($html);
        } catch (\Exception $e) {
            return $result->setContents(
                '<div class="message error">' . __('Could not load batch data.') . '</div>'
            );
        }
    }

    private function renderTable($collection): string
    {
        $rows = '';
        foreach ($collection as $batch) {
            $statusLabels = [
                BatchInterface::STATUS_ACTIVE => 'Active',
                BatchInterface::STATUS_SOLD_OUT => 'Sold Out',
                BatchInterface::STATUS_EXPIRED => 'Expired',
                BatchInterface::STATUS_RECALLED => 'Recalled',
            ];
            $status = (int)$batch->getStatus();
            $statusLabel = (string)__($statusLabels[$status] ?? 'Unknown');

            $cls = 'grid-severity-notice';
            if ($status === BatchInterface::STATUS_EXPIRED || $status === BatchInterface::STATUS_RECALLED) {
                $cls = 'grid-severity-critical';
            } elseif ($status === BatchInterface::STATUS_SOLD_OUT) {
                $cls = 'grid-severity-minor';
            }

            $rows .= '<tr>'
                . '<td>' . $batch->getBatchNumber() . '</td>'
                . '<td>' . $batch->getSourceCode() . '</td>'
                . '<td>' . number_format((float)$batch->getQtyRemaining(), 2) . '</td>'
                . '<td>' . $batch->getExpiryDate() . '</td>'
                . '<td><span class="' . $cls . '"><span>' . $statusLabel . '</span></span></td>'
                . '</tr>';
        }

        if (empty($rows)) {
            return '<div class="message notice">' . __('No batches found for this product.') . '</div>';
        }

        return '<div class="admin__data-grid-wrap" style="margin-top:15px;max-height:300px;overflow-y:auto">'
            . '<table class="data-grid">'
            . '<thead><tr>'
            . '<th class="data-grid-th">' . __('Batch #') . '</th>'
            . '<th class="data-grid-th">' . __('Source') . '</th>'
            . '<th class="data-grid-th">' . __('Qty Remaining') . '</th>'
            . '<th class="data-grid-th">' . __('Expiry Date') . '</th>'
            . '<th class="data-grid-th">' . __('Status') . '</th>'
            . '</tr></thead>'
            . '<tbody>' . $rows . '</tbody>'
            . '</table></div>';
    }
}
