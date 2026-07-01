<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use MageBatch\Inventory\Model\ExpiryManagement;
use MageBatch\Inventory\Api\BatchRepositoryInterface;
use MageBatch\Inventory\Api\HistoryRepositoryInterface;
use MageBatch\Inventory\Api\Data\BatchInterface;
use MageBatch\Inventory\Api\Data\HistoryInterface;
use MageBatch\Inventory\Api\Data\HistoryInterfaceFactory;
use MageBatch\Inventory\Model\ResourceModel\Batch\CollectionFactory;
use MageBatch\Inventory\Model\ResourceModel\Batch\Collection;
use Psr\Log\LoggerInterface;

class ExpiryManagementTest extends TestCase
{
    private ExpiryManagement $model;
    private CollectionFactory $collectionFactory;
    private BatchRepositoryInterface $batchRepository;
    private HistoryRepositoryInterface $historyRepository;
    private HistoryInterfaceFactory $historyFactory;
    private LoggerInterface $logger;

    protected function setUp(): void
    {
        $this->collectionFactory = $this->createMock(CollectionFactory::class);
        $this->batchRepository = $this->createMock(BatchRepositoryInterface::class);
        $this->historyRepository = $this->createMock(HistoryRepositoryInterface::class);
        $this->historyFactory = $this->createMock(HistoryInterfaceFactory::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->model = new ExpiryManagement(
            $this->collectionFactory,
            $this->batchRepository,
            $this->historyRepository,
            $this->historyFactory,
            $this->logger
        );
    }

    public function testProcessExpiredBatches(): void
    {
        $batch = $this->createMock(BatchInterface::class);
        $batch->method('getBatchId')->willReturn(1);
        $batch->method('getProductSku')->willReturn('SKU-1');

        $collection = $this->createMock(Collection::class);
        $collection->method('addFieldToFilter')->willReturnSelf();
        $collection->method('getIterator')->willReturn(new \ArrayIterator([$batch]));

        $this->collectionFactory->method('create')->willReturn($collection);
        $this->batchRepository->method('save')->willReturn($batch);

        $history = $this->createMock(HistoryInterface::class);
        $this->historyFactory->method('create')->willReturn($history);

        $result = $this->model->processExpiredBatches();

        $this->assertEquals(1, $result);
    }
}
