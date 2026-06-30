<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Block\Adminhtml\Report;

use Magento\Backend\Block\Template;
use MageBatch\Inventory\Model\ResourceModel\Batch\CollectionFactory;

class AllocationReport extends Template
{
    public function __construct(
        Template\Context $context,
        private CollectionFactory $collectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getTopAllocatedSku(int $limit = 10): array
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('status', \MageBatch\Inventory\Api\Data\BatchInterface::STATUS_ACTIVE);
        $collection->setOrder('qty_original', \Magento\Framework\Data\Collection::SORT_ORDER_DESC);
        $collection->setPageSize($limit);
        return $collection->getItems();
    }

    public function getAllocationSummary(): array
    {
        $collection = $this->collectionFactory->create();
        $total = $collection->getSize();
        $active = clone $collection;
        $active->addFieldToFilter('status', \MageBatch\Inventory\Api\Data\BatchInterface::STATUS_ACTIVE);

        return [
            'total' => $total,
            'active' => $active->getSize(),
        ];
    }
}
