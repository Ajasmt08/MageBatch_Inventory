<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Api;

interface FefoAllocationInterface
{
    public function allocate(
        string $sku,
        array $sourceCodes,
        float $qty,
        int $orderId,
        int $orderItemId
    ): array;
}
