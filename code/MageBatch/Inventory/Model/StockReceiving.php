<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\InventoryApi\Api\Data\SourceInterfaceFactory;
use Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use MageBatch\Inventory\Api\BatchRepositoryInterface;
use MageBatch\Inventory\Api\Data\BatchInterface;
use MageBatch\Inventory\Api\Data\BatchInterfaceFactory;
use MageBatch\Inventory\Api\HistoryRepositoryInterface;
use MageBatch\Inventory\Api\Data\HistoryInterface;
use MageBatch\Inventory\Api\Data\HistoryInterfaceFactory;
use MageBatch\Inventory\Api\StockReceivingInterface;
use Psr\Log\LoggerInterface;

class StockReceiving implements StockReceivingInterface
{
    public function __construct(
        private BatchInterfaceFactory $batchFactory,
        private BatchRepositoryInterface $batchRepository,
        private HistoryInterfaceFactory $historyFactory,
        private HistoryRepositoryInterface $historyRepository,
        private SourceInterfaceFactory $sourceFactory,
        private SourceRepositoryInterface $sourceRepository,
        private SourceItemInterfaceFactory $sourceItemFactory,
        private SourceItemRepositoryInterface $sourceItemRepository,
        private SourceItemsSaveInterface $sourceItemsSave,
        private SearchCriteriaBuilder $searchCriteriaBuilder,
        private LoggerInterface $logger
    ) {}

    public function receive(
        string $productSku,
        string $sourceCode,
        string $batchNumber,
        float $qty,
        string $expiryDate,
        ?string $manufacturingDate = null,
        ?string $supplier = null,
        ?string $purchaseOrder = null,
        ?float $costPrice = null,
        ?string $notes = null
    ): BatchInterface {
        if (empty($productSku) || empty($batchNumber) || $qty <= 0) {
            throw new InputException(__('Invalid input parameters for stock receiving.'));
        }

        try {
            $this->ensureSourceExists($sourceCode);

            $batch = $this->batchFactory->create();
            $batch->setProductSku($productSku);
            $batch->setSourceCode($sourceCode);
            $batch->setBatchNumber($batchNumber);
            $batch->setQtyReceived($qty);
            $batch->setQtyRemaining($qty);
            $batch->setExpiryDate($expiryDate);
            $batch->setManufacturingDate($manufacturingDate);
            $batch->setSupplier($supplier);
            $batch->setPurchaseOrder($purchaseOrder);
            $batch->setCostPrice($costPrice);
            $batch->setStatus(BatchInterface::STATUS_ACTIVE);
            $batch->setNotes($notes);

            $saved = $this->batchRepository->save($batch);

            $this->updateSourceItem($productSku, $sourceCode, $qty);

            $history = $this->historyFactory->create();
            $history->setBatchId((int)$saved->getBatchId());
            $history->setProductSku($productSku);
            $history->setAction(HistoryInterface::ACTION_RECEIVED);
            $history->setQtyBefore(0.0);
            $history->setQtyAfter($qty);
            $this->historyRepository->save($history);

            return $saved;
        } catch (\Exception $e) {
            $this->logger->error('Stock receiving failed: ' . $e->getMessage());
            throw new CouldNotSaveException(__('Failed to receive stock: %1', $e->getMessage()), $e);
        }
    }

    private function ensureSourceExists(string $sourceCode): void
    {
        try {
            $this->sourceRepository->get($sourceCode);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $source = $this->sourceFactory->create();
            $source->setSourceCode($sourceCode);
            $source->setName(ucfirst($sourceCode));
            $source->setPostcode('00000');
            $source->setCountryId('US');
            $source->setEnabled(true);
            $this->sourceRepository->save($source);
        }
    }

    private function updateSourceItem(string $sku, string $sourceCode, float $qty): void
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('sku', $sku)
            ->addFilter('source_code', $sourceCode)
            ->create();

        $items = $this->sourceItemRepository->getList($searchCriteria)->getItems();
        $sourceItem = reset($items);

        if ($sourceItem) {
            $sourceItem->setQuantity((float)$sourceItem->getQuantity() + $qty);
            $sourceItem->setStatus(1);
        } else {
            $sourceItem = $this->sourceItemFactory->create();
            $sourceItem->setSku($sku);
            $sourceItem->setSourceCode($sourceCode);
            $sourceItem->setQuantity($qty);
            $sourceItem->setStatus(1);
        }

        $this->sourceItemsSave->execute([$sourceItem]);
    }
}
