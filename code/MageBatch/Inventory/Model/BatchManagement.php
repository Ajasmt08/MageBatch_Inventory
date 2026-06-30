<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use MageBatch\Inventory\Api\BatchManagementInterface;
use MageBatch\Inventory\Api\BatchRepositoryInterface;
use MageBatch\Inventory\Api\Data\BatchInterface;
use MageBatch\Inventory\Api\HistoryRepositoryInterface;
use MageBatch\Inventory\Api\Data\HistoryInterface;
use MageBatch\Inventory\Api\Data\HistoryInterfaceFactory;

class BatchManagement implements BatchManagementInterface
{
    public function __construct(
        private BatchRepositoryInterface $batchRepository,
        private HistoryRepositoryInterface $historyRepository,
        private HistoryInterfaceFactory $historyFactory
    ) {}

    public function changeStatus(int $batchId, int $newStatus, ?string $reason = null): BatchInterface
    {
        $batch = $this->batchRepository->getById($batchId);
        $oldStatus = $batch->getStatus();
        $batch->setStatus($newStatus);
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

    public function markDamaged(int $batchId, ?string $reason = null): BatchInterface
    {
        return $this->changeStatus($batchId, BatchInterface::STATUS_DAMAGED, $reason);
    }

    public function quarantine(int $batchId, ?string $reason = null): BatchInterface
    {
        return $this->changeStatus($batchId, BatchInterface::STATUS_QUARANTINED, $reason);
    }

    public function restore(int $batchId, ?string $reason = null): BatchInterface
    {
        return $this->changeStatus($batchId, BatchInterface::STATUS_ACTIVE, $reason);
    }
}
