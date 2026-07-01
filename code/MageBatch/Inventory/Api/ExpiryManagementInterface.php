<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Api;

interface ExpiryManagementInterface
{
    public function processExpiredBatches(): int;
}
