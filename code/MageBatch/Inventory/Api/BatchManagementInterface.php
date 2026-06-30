<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Api;

use MageBatch\Inventory\Api\Data\BatchInterface;

interface BatchManagementInterface
{
    public function changeStatus(int $batchId, int $newStatus, ?string $reason = null): BatchInterface;

    public function recall(int $batchId, ?string $reason = null): BatchInterface;

    public function markDamaged(int $batchId, ?string $reason = null): BatchInterface;

    public function quarantine(int $batchId, ?string $reason = null): BatchInterface;

    public function restore(int $batchId, ?string $reason = null): BatchInterface;
}
