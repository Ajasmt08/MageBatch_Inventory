<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use MageBatch\Inventory\Model\FefoAllocation;
use MageBatch\Inventory\Api\BatchRepositoryInterface;
use MageBatch\Inventory\Api\HistoryRepositoryInterface;
use MageBatch\Inventory\Api\Data\BatchInterface;
use MageBatch\Inventory\Api\Data\HistoryInterface;
use MageBatch\Inventory\Api\Data\HistoryInterfaceFactory;
use MageBatch\Inventory\Model\ResourceModel\Batch\CollectionFactory;
use MageBatch\Inventory\Model\ResourceModel\Batch\Collection;
use MageBatch\Inventory\Model\ReservationFactory;
use MageBatch\Inventory\Model\ResourceModel\Reservation as ReservationResource;
use Psr\Log\LoggerInterface;

class FefoAllocationTest extends TestCase
{
    private FefoAllocation $model;
    private CollectionFactory $collectionFactory;
    private BatchRepositoryInterface $batchRepository;
    private HistoryRepositoryInterface $historyRepository;
    private HistoryInterfaceFactory $historyFactory;
    private ReservationFactory $reservationFactory;
    private ReservationResource $reservationResource;
    private LoggerInterface $logger;

    protected function setUp(): void
    {
        $this->collectionFactory = $this->createMock(CollectionFactory::class);
        $this->batchRepository = $this->createMock(BatchRepositoryInterface::class);
        $this->historyRepository = $this->createMock(HistoryRepositoryInterface::class);
        $this->historyFactory = $this->createMock(HistoryInterfaceFactory::class);
        $this->reservationFactory = $this->createMock(ReservationFactory::class);
        $this->reservationResource = $this->createMock(ReservationResource::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->model = new FefoAllocation(
            $this->collectionFactory,
            $this->batchRepository,
            $this->historyRepository,
            $this->historyFactory,
            $this->reservationFactory,
            $this->reservationResource,
            $this->logger
        );
    }

    public function testAllocateFromSingleBatch(): void
    {
        $batch = $this->createMock(BatchInterface::class);
        $batch->method('getBatchId')->willReturn(1);
        $batch->method('getQtyRemaining')->willReturn(50.0);

        $collection = $this->createMock(Collection::class);
        $collection->method('addFieldToFilter')->willReturnSelf();
        $collection->method('setOrder')->willReturnSelf();
        $collection->method('getIterator')->willReturn(new \ArrayIterator([$batch]));

        $this->collectionFactory->method('create')->willReturn($collection);

        $history = $this->createMock(HistoryInterface::class);
        $this->historyFactory->method('create')->willReturn($history);

        $reservation = $this->createMock(\MageBatch\Inventory\Model\Reservation::class);
        $this->reservationFactory->method('create')->willReturn($reservation);

        $this->batchRepository->expects($this->once())->method('save');

        $result = $this->model->allocate('SKU-1', ['default'], 30.0, 101, 1);

        $this->assertCount(1, $result);
        $this->assertEquals(1, $result[0]['batch_id']);
        $this->assertEquals(30.0, $result[0]['qty']);
    }
}
