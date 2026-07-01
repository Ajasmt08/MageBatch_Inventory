<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;
use MageBatch\Inventory\Api\BatchManagementInterface;
use MageBatch\Inventory\Api\BatchRepositoryInterface;
use MageBatch\Inventory\Api\Data\BatchInterface;
use MageBatch\Inventory\Api\HistoryRepositoryInterface;
use MageBatch\Inventory\Api\Data\HistoryInterface;
use MageBatch\Inventory\Api\Data\HistoryInterfaceFactory;
use MageBatch\Inventory\Model\ResourceModel\Batch\CollectionFactory as BatchCollectionFactory;

class BatchManagement implements BatchManagementInterface
{
    public function __construct(
        private BatchRepositoryInterface $batchRepository,
        private HistoryRepositoryInterface $historyRepository,
        private HistoryInterfaceFactory $historyFactory,
        private SourceItemRepositoryInterface $sourceItemRepository,
        private SourceItemsSaveInterface $sourceItemsSave,
        private SearchCriteriaBuilder $searchCriteriaBuilder,
        private BatchCollectionFactory $batchCollectionFactory,
    ) {}

    public function changeStatus(int $batchId, int $newStatus, ?string $reason = null): BatchInterface
    {
        $batch = $this->batchRepository->getById($batchId);
        $batch->setStatus($newStatus);

        $this->applyStatusInventoryChanges($batch);

        $saved = $this->batchRepository->save($batch);

        $history = $this->historyFactory->create();
        $history->setBatchId($batchId);
        $history->setProductSku($batch->getProductSku());
        $history->setAction(HistoryInterface::ACTION_STATUS_CHANGE);
        $history->setReason($reason);
        $this->historyRepository->save($history);

        return $saved;
    }

    public function recall(int $batchId, ?string $reason = null): BatchInterface
    {
        return $this->changeStatus($batchId, BatchInterface::STATUS_RECALLED, $reason);
    }

    public function applyStatusInventoryChanges(BatchInterface $batch): void
    {
        $newStatus = (int)$batch->getStatus();

        if ($newStatus === BatchInterface::STATUS_ACTIVE) {
            $this->setSourceItemStatus($batch, 1);
            return;
        }

        if (in_array($newStatus, [BatchInterface::STATUS_EXPIRED, BatchInterface::STATUS_RECALLED], true)) {
            if ($this->hasOtherActiveBatches($batch)) {
                return;
            }
            $this->setSourceItemStatus($batch, 0);
            return;
        }
    }

    public function syncQuantity(BatchInterface $batch, float $oldQty): void
    {
        $newQty = (float)$batch->getQtyRemaining();
        $delta = $newQty - $oldQty;

        if (abs($delta) < 0.0001) {
            return;
        }

        try {
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('sku', $batch->getProductSku())
                ->addFilter('source_code', $batch->getSourceCode())
                ->create();

            $items = $this->sourceItemRepository->getList($searchCriteria)->getItems();
            $sourceItem = reset($items);

            if ($sourceItem) {
                $newSourceQty = max(0, (float)$sourceItem->getQuantity() + $delta);
                $sourceItem->setQuantity($newSourceQty);
                $this->sourceItemsSave->execute([$sourceItem]);
            }
        } catch (\Exception $e) {
            return;
        }
    }

    private function hasOtherActiveBatches(BatchInterface $batch): bool
    {
        if (!$batch->getBatchId()) {
            return false;
        }

        $collection = $this->batchCollectionFactory->create();
        $collection->addFieldToFilter(BatchInterface::PRODUCT_SKU, $batch->getProductSku());
        $collection->addFieldToFilter(BatchInterface::SOURCE_CODE, $batch->getSourceCode());
        $collection->addFieldToFilter(BatchInterface::BATCH_ID, ['neq' => $batch->getBatchId()]);
        $collection->addFieldToFilter(BatchInterface::STATUS, BatchInterface::STATUS_ACTIVE);
        $collection->addFieldToFilter(BatchInterface::QTY_REMAINING, ['gt' => 0]);

        return $collection->getSize() > 0;
    }

    private function setSourceItemStatus(BatchInterface $batch, int $status): void
    {
        try {
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('sku', $batch->getProductSku())
                ->addFilter('source_code', $batch->getSourceCode())
                ->create();

            $items = $this->sourceItemRepository->getList($searchCriteria)->getItems();
            $sourceItem = reset($items);

            if ($sourceItem) {
                $sourceItem->setStatus($status);
                $this->sourceItemsSave->execute([$sourceItem]);
            }
        } catch (\Exception $e) {
            return;
        }
    }
}