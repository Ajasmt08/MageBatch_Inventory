<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Block\Adminhtml\Report;

use Magento\Backend\Block\Template;
use MageBatch\Inventory\Model\ResourceModel\Batch\CollectionFactory;

class ExpiryReport extends Template
{
    public function __construct(
        Template\Context $context,
        private CollectionFactory $collectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getExpiredBatches(): array
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('status', \MageBatch\Inventory\Api\Data\BatchInterface::STATUS_EXPIRED);
        return $collection->getItems();
    }

    public function getExpiringBatches(int $days = 30): array
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('status', \MageBatch\Inventory\Api\Data\BatchInterface::STATUS_ACTIVE);
        $collection->addFieldToFilter('expiry_date', ['lteq' => (new \DateTime())->modify("+{$days} days")->format('Y-m-d')]);
        return $collection->getItems();
    }
}
