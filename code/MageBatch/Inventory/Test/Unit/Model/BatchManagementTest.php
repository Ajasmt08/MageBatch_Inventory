<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use MageBatch\Inventory\Model\BatchManagement;
use MageBatch\Inventory\Api\BatchRepositoryInterface;
use MageBatch\Inventory\Api\HistoryRepositoryInterface;
use MageBatch\Inventory\Api\Data\BatchInterface;
use MageBatch\Inventory\Api\Data\HistoryInterface;
use MageBatch\Inventory\Api\Data\HistoryInterfaceFactory;

class BatchManagementTest extends TestCase
{
    private BatchManagement $model;
    private BatchRepositoryInterface $batchRepository;
    private HistoryRepositoryInterface $historyRepository;
    private HistoryInterfaceFactory $historyFactory;

    protected function setUp(): void
    {
        $this->batchRepository = $this->createMock(BatchRepositoryInterface::class);
        $this->historyRepository = $this->createMock(HistoryRepositoryInterface::class);
        $this->historyFactory = $this->createMock(HistoryInterfaceFactory::class);

        $this->model = new BatchManagement(
            $this->batchRepository,
            $this->historyRepository,
            $this->historyFactory
        );
    }

    public function testRecallChangesStatus(): void
    {
        $batch = $this->createMock(BatchInterface::class);
        $history = $this->createMock(HistoryInterface::class);

        $this->batchRepository->method('getById')->with(1)->willReturn($batch);
        $this->batchRepository->method('save')->willReturn($batch);
        $this->historyFactory->method('create')->willReturn($history);

        $batch->expects($this->once())->method('setStatus')->with(BatchInterface::STATUS_RECALLED);

        $result = $this->model->recall(1, 'Quality issue');

        $this->assertSame($batch, $result);
    }

    public function testMarkDamagedChangesStatus(): void
    {
        $batch = $this->createMock(BatchInterface::class);
        $history = $this->createMock(HistoryInterface::class);

        $this->batchRepository->method('getById')->with(1)->willReturn($batch);
        $this->batchRepository->method('save')->willReturn($batch);
        $this->historyFactory->method('create')->willReturn($history);

        $batch->expects($this->once())->method('setStatus')->with(BatchInterface::STATUS_DAMAGED);

        $result = $this->model->markDamaged(1, 'Broken packaging');

        $this->assertSame($batch, $result);
    }

    public function testRestoreChangesStatus(): void
    {
        $batch = $this->createMock(BatchInterface::class);
        $history = $this->createMock(HistoryInterface::class);

        $this->batchRepository->method('getById')->with(1)->willReturn($batch);
        $this->batchRepository->method('save')->willReturn($batch);
        $this->historyFactory->method('create')->willReturn($history);

        $batch->expects($this->once())->method('setStatus')->with(BatchInterface::STATUS_ACTIVE);

        $result = $this->model->restore(1, 'Cleared by QA');

        $this->assertSame($batch, $result);
    }
}
