<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Block\Adminhtml;

use Magento\Backend\Block\Template;
use MageBatch\Inventory\Model\ResourceModel\Batch\CollectionFactory;

class Dashboard extends Template
{
    public function __construct(
        Template\Context $context,
        private CollectionFactory $batchCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getTotalBatches(): int
    {
        return $this->batchCollectionFactory->create()->getSize();
    }

    public function getActiveBatches(): int
    {
        $collection = $this->batchCollectionFactory->create();
        $collection->addFieldToFilter('status', \MageBatch\Inventory\Api\Data\BatchInterface::STATUS_ACTIVE);
        return $collection->getSize();
    }

    public function getExpiredBatches(): int
    {
        $collection = $this->batchCollectionFactory->create();
        $collection->addFieldToFilter('status', \MageBatch\Inventory\Api\Data\BatchInterface::STATUS_EXPIRED);
        return $collection->getSize();
    }

    public function getNearExpiryBatches(): int
    {
        $collection = $this->batchCollectionFactory->create();
        $collection->addFieldToFilter('status', \MageBatch\Inventory\Api\Data\BatchInterface::STATUS_ACTIVE);
        $collection->addFieldToFilter('expiry_date', ['lteq' => (new \DateTime())->modify('+30 days')->format('Y-m-d')]);
        return $collection->getSize();
    }
}
