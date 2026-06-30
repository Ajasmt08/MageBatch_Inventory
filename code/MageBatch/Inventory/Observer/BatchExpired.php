<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Psr\Log\LoggerInterface;

class BatchExpired implements ObserverInterface
{
    public function __construct(
        private LoggerInterface $logger
    ) {}

    public function execute(Observer $observer): void
    {
        $batch = $observer->getData('batch');
        $this->logger->info(sprintf('MageBatch: Batch #%d has expired', $batch->getBatchId()));
    }
}
