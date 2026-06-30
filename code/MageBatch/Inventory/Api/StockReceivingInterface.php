<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Api;

use MageBatch\Inventory\Api\Data\BatchInterface;

interface StockReceivingInterface
{
    public function receive(
        string $productSku,
        string $sourceCode,
        string $batchNumber,
        float $qty,
        string $expiryDate,
        ?string $manufacturingDate = null,
        ?string $supplier = null,
        ?string $purchaseOrder = null,
        ?float $costPrice = null,
        ?string $notes = null
    ): BatchInterface;
}
