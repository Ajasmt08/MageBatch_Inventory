<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Cron;

use MageBatch\Inventory\Api\ExpiryManagementInterface;
use Psr\Log\LoggerInterface;

class ProcessExpiredBatches
{
    public function __construct(
        private ExpiryManagementInterface $expiryManagement,
        private LoggerInterface $logger
    ) {}

    public function execute(): void
    {
        try {
            $count = $this->expiryManagement->processExpiredBatches();
            $this->logger->info(sprintf('MageBatch: Processed %d expired batches.', $count));
        } catch (\Exception $e) {
            $this->logger->error('MageBatch: Expiry processing failed: ' . $e->getMessage());
        }
    }
}
