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
use Magento\InventoryApi\Api\Data\SourceInterfaceFactory;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Psr\Log\LoggerInterface;

class StockReceivingTest extends TestCase
{
    private StockReceiving $model;
    private BatchInterfaceFactory $batchFactory;
    private BatchRepositoryInterface $batchRepository;
    private HistoryInterfaceFactory $historyFactory;
    private HistoryRepositoryInterface $historyRepository;
    private SourceInterfaceFactory $sourceFactory;
    private SourceRepositoryInterface $sourceRepository;
    private SourceItemInterfaceFactory $sourceItemFactory;
    private SourceItemRepositoryInterface $sourceItemRepository;
    private SourceItemsSaveInterface $sourceItemsSave;
    private SearchCriteriaBuilder $searchCriteriaBuilder;
    private LoggerInterface $logger;

    protected function setUp(): void
    {
        $this->batchFactory = $this->createMock(BatchInterfaceFactory::class);
        $this->batchRepository = $this->createMock(BatchRepositoryInterface::class);
        $this->historyFactory = $this->createMock(HistoryInterfaceFactory::class);
        $this->historyRepository = $this->createMock(HistoryRepositoryInterface::class);
        $this->sourceFactory = $this->createMock(SourceInterfaceFactory::class);
        $this->sourceRepository = $this->createMock(SourceRepositoryInterface::class);
        $this->sourceItemFactory = $this->createMock(SourceItemInterfaceFactory::class);
        $this->sourceItemRepository = $this->createMock(SourceItemRepositoryInterface::class);
        $this->sourceItemsSave = $this->createMock(SourceItemsSaveInterface::class);
        $this->searchCriteriaBuilder = $this->createMock(SearchCriteriaBuilder::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->model = new StockReceiving(
            $this->batchFactory,
            $this->batchRepository,
            $this->historyFactory,
            $this->historyRepository,
            $this->sourceFactory,
            $this->sourceRepository,
            $this->sourceItemFactory,
            $this->sourceItemRepository,
            $this->sourceItemsSave,
            $this->searchCriteriaBuilder,
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

        $this->searchCriteriaBuilder->method('addFilter')->willReturnSelf();
        $searchCriteria = $this->createMock(\Magento\Framework\Api\SearchCriteria::class);
        $this->searchCriteriaBuilder->method('create')->willReturn($searchCriteria);

        $searchResults = $this->createMock(\Magento\InventoryApi\Api\Data\SourceItemSearchResultsInterface::class);
        $searchResults->method('getItems')->willReturn([]);
        $this->sourceItemRepository->method('getList')->willReturn($searchResults);
        $this->sourceItemFactory->method('create')->willReturn(
            $this->createMock(\Magento\InventoryApi\Api\Data\SourceItemInterface::class)
        );

        $this->batchRepository->expects($this->once())
            ->method('save')
            ->willReturn($batch);

        $history->expects($this->once())
            ->method('setBatchId')
            ->with(1);
        $this->historyRepository->expects($this->once())
            ->method('save')
            ->with($history);

        $result = $this->model->receive('SKU-1', 'default', 'BATCH-001', 50.0, '2027-12-31');

        $this->assertSame($batch, $result);
    }
}
