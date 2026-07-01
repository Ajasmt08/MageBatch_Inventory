<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\Data\SourceItemSearchResultsInterface;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;
use MageBatch\Inventory\Model\BatchManagement;
use MageBatch\Inventory\Api\BatchRepositoryInterface;
use MageBatch\Inventory\Api\HistoryRepositoryInterface;
use MageBatch\Inventory\Api\Data\BatchInterface;
use MageBatch\Inventory\Api\Data\HistoryInterface;
use MageBatch\Inventory\Api\Data\HistoryInterfaceFactory;
use MageBatch\Inventory\Model\ResourceModel\Batch\CollectionFactory as BatchCollectionFactory;
use MageBatch\Inventory\Model\ResourceModel\Batch\Collection as BatchCollection;

class BatchManagementTest extends TestCase
{
    public function testRecallSetsSourceItemOutOfStockWhenNoOtherActiveBatches(): void
    {
        $batch = $this->createMock(BatchInterface::class);
        $history = $this->createMock(HistoryInterface::class);
        $sourceItem = $this->createMock(SourceItemInterface::class);

        $batchRepository = $this->createMock(BatchRepositoryInterface::class);
        $historyRepository = $this->createMock(HistoryRepositoryInterface::class);
        $historyFactory = $this->createMock(HistoryInterfaceFactory::class);
        $sourceItemRepository = $this->createMock(SourceItemRepositoryInterface::class);
        $sourceItemsSave = $this->createMock(SourceItemsSaveInterface::class);
        $searchCriteriaBuilder = $this->createMock(SearchCriteriaBuilder::class);
        $batchCollectionFactory = $this->createMock(BatchCollectionFactory::class);

        $batchRepository->method('getById')->with(1)->willReturn($batch);
        $batchRepository->method('save')->willReturn($batch);
        $historyFactory->method('create')->willReturn($history);

        $batch->method('getBatchId')->willReturn(1);
        $batch->method('getStatus')->willReturn(BatchInterface::STATUS_RECALLED);
        $batch->method('getProductSku')->willReturn('24-WB02');
        $batch->method('getSourceCode')->willReturn('default');
        $batch->expects($this->once())->method('setStatus')->with(BatchInterface::STATUS_RECALLED);

        $batchCollection = $this->createMock(BatchCollection::class);
        $batchCollection->method('getSize')->willReturn(0);
        $batchCollectionFactory->method('create')->willReturn($batchCollection);

        $searchCriteriaBuilder->method('addFilter')->willReturnSelf();
        $searchCriteriaBuilder->method('create')->willReturn($this->createMock(SearchCriteria::class));

        $searchResults = $this->createMock(SourceItemSearchResultsInterface::class);
        $searchResults->method('getItems')->willReturn([$sourceItem]);
        $sourceItemRepository->method('getList')->willReturn($searchResults);

        $sourceItem->expects($this->once())->method('setStatus')->with(0);
        $sourceItemsSave->expects($this->once())->method('execute')->with([$sourceItem]);

        $model = new BatchManagement(
            $batchRepository,
            $historyRepository,
            $historyFactory,
            $sourceItemRepository,
            $sourceItemsSave,
            $searchCriteriaBuilder,
            $batchCollectionFactory,
        );

        $result = $model->recall(1, 'Quality issue');

        $this->assertSame($batch, $result);
    }
}