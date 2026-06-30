<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Model\Batch\Source;

use Magento\Framework\Data\OptionSourceInterface;
use MageBatch\Inventory\Api\Data\BatchInterface;

class Status implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => BatchInterface::STATUS_ACTIVE, 'label' => __('Active')],
            ['value' => BatchInterface::STATUS_SOLD_OUT, 'label' => __('Sold Out')],
            ['value' => BatchInterface::STATUS_EXPIRED, 'label' => __('Expired')],
            ['value' => BatchInterface::STATUS_RECALLED, 'label' => __('Recalled')],
            ['value' => BatchInterface::STATUS_DAMAGED, 'label' => __('Damaged')],
            ['value' => BatchInterface::STATUS_QUARANTINED, 'label' => __('Quarantined')],
            ['value' => BatchInterface::STATUS_RESERVED, 'label' => __('Reserved')],
        ];
    }
}
