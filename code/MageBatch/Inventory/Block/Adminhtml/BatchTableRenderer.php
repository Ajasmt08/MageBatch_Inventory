<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Block\Adminhtml;

use Magento\Framework\View\Element\Template;
use MageBatch\Inventory\Api\Data\BatchInterface;

class BatchTableRenderer extends Template
{
    public function getBatches(): array
    {
        return $this->getData('batches') ?? [];
    }

    public function getStatusLabel(int $status): string
    {
        $labels = [
            BatchInterface::STATUS_ACTIVE => 'Active',
            BatchInterface::STATUS_EXPIRED => 'Expired',
            BatchInterface::STATUS_RECALLED => 'Recalled',
            BatchInterface::STATUS_SOLD_OUT => 'Sold Out',
        ];
        return (string)__($labels[$status] ?? 'Unknown');
    }

    public function getStatusClass(int $status): string
    {
        if ($status === BatchInterface::STATUS_EXPIRED || $status === BatchInterface::STATUS_RECALLED) {
            return 'grid-severity-critical';
        }
        if ($status === BatchInterface::STATUS_SOLD_OUT) {
            return 'grid-severity-minor';
        }
        return 'grid-severity-notice';
    }
}
