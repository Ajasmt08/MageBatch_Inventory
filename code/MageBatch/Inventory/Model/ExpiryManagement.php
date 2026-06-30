<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Model;

use MageBatch\Inventory\Api\BatchRepositoryInterface;
use MageBatch\Inventory\Api\Data\BatchInterface;
use MageBatch\Inventory\Api\ExpiryManagementInterface;
use MageBatch\Inventory\Api\HistoryRepositoryInterface;
use MageBatch\Inventory\Api\Data\HistoryInterface;
use MageBatch\Inventory\Api\Data\HistoryInterfaceFactory;
use MageBatch\Inventory\Model\ResourceModel\Batch\CollectionFactory;
use Psr\Log\LoggerInterface;

class ExpiryManagement implements ExpiryManagementInterface
{
    public function __construct(
        private CollectionFactory $collectionFactory,
        private BatchRepositoryInterface $batchRepository,
        private HistoryRepositoryInterface $historyRepository,
        private HistoryInterfaceFactory $historyFactory,
        private LoggerInterface $logger
    ) {}

    public function processExpiredBatches(): int
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(BatchInterface::STATUS, BatchInterface::STATUS_ACTIVE);
        $collection->addFieldToFilter(BatchInterface::EXPIRY_DATE, ['lt' => (new \DateTime())->format('Y-m-d')]);

        $count = 0;
        foreach ($collection as $batch) {
            try {
                $batch->setStatus(BatchInterface::STATUS_EXPIRED);
                $this->batchRepository->save($batch);

                $history = $this->historyFactory->create();
                $history->setBatchId((int)$batch->getBatchId());
                $history->setProductSku($batch->getProductSku());
                $history->setAction(HistoryInterface::ACTION_EXPIRED);
                $this->historyRepository->save($history);

                $count++;
            } catch (\Exception $e) {
                $this->logger->error(sprintf(
                    'Failed to expire batch #%d: %s',
                    $batch->getBatchId(),
                    $e->getMessage()
                ));
            }
        }

        return $count;
    }
}
