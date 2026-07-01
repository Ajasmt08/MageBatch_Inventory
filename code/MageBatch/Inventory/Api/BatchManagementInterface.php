<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Api;

use MageBatch\Inventory\Api\Data\BatchInterface;

interface BatchManagementInterface
{
    public function changeStatus(int $batchId, int $newStatus, ?string $reason = null): BatchInterface;

    public function recall(int $batchId, ?string $reason = null): BatchInterface;

    public function applyStatusInventoryChanges(BatchInterface $batch): void;

    public function syncQuantity(BatchInterface $batch, float $oldQty): void;
}