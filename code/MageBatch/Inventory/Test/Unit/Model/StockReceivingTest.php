<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use MageBatch\Inventory\Model\StockReceiving;
use MageBatch\Inventory\Api\BatchRepositoryInterface;
use MageBatch\Inventory\Api\HistoryRepositoryInterface;
use MageBatch\Inventory\Api\Data\BatchInterface;
use MageBatch\Inventory\Api\Data\BatchInterfaceFactory;
use MageBatch\Inventory\Api\Data\HistoryInterface;
use MageBatch\Inventory\Api\Data\HistoryInterfaceFactory;
use Psr\Log\LoggerInterface;

class StockReceivingTest extends TestCase
{
    private StockReceiving $model;
    private BatchInterfaceFactory $batchFactory;
    private BatchRepositoryInterface $batchRepository;
    private HistoryInterfaceFactory $historyFactory;
    private HistoryRepositoryInterface $historyRepository;
    private LoggerInterface $logger;

    protected function setUp(): void
    {
        $this->batchFactory = $this->createMock(BatchInterfaceFactory::class);
        $this->batchRepository = $this->createMock(BatchRepositoryInterface::class);
        $this->historyFactory = $this->createMock(HistoryInterfaceFactory::class);
        $this->historyRepository = $this->createMock(HistoryRepositoryInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->model = new StockReceiving(
            $this->batchFactory,
            $this->batchRepository,
            $this->historyFactory,
            $this->historyRepository,
            $this->logger
        );
    }

    public function testReceiveCreatesBatchAndHistory(): void
    {
        $batch = $this->createMock(BatchInterface::class);
        $history = $this->createMock(HistoryInterface::class);

        $batch->method('getBatchId')->willReturn(1);

        $this->batchFactory->method('create')->willReturn($batch);
        $this->historyFactory->method('create')->willReturn($history);

        $this->batchRepository->expects($this->once())->method('save')->with($batch)->willReturn($batch);
        $this->historyRepository->expects($this->once())->method('save');

        $result = $this->model->receive('TEST-SKU', 'default', 'BATCH-001', 100.0, '2027-12-31');

        $this->assertSame($batch, $result);
    }

    public function testReceiveThrowsOnEmptySku(): void
    {
        $this->expectException(\Magento\Framework\Exception\InputException::class);
        $this->model->receive('', 'default', 'BATCH-001', 100.0, '2027-12-31');
    }
}
