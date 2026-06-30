<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Psr\Log\LoggerInterface;

class FefoAllocated implements ObserverInterface
{
    public function __construct(
        private LoggerInterface $logger
    ) {}

    public function execute(Observer $observer): void
    {
        $allocations = $observer->getData('allocations');
        $this->logger->info(sprintf('MageBatch: FEFO allocation completed: %d batches allocated', count($allocations)));
    }
}
