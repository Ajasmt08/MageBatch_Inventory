<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Model;

use Magento\Framework\Exception\LocalizedException;
use MageBatch\Inventory\Api\BatchRepositoryInterface;
use MageBatch\Inventory\Api\Data\BatchInterface;
use MageBatch\Inventory\Api\FefoAllocationInterface;
use MageBatch\Inventory\Api\HistoryRepositoryInterface;
use MageBatch\Inventory\Api\Data\HistoryInterface;
use MageBatch\Inventory\Api\Data\HistoryInterfaceFactory;
use MageBatch\Inventory\Model\ResourceModel\Batch\CollectionFactory as BatchCollectionFactory;
use MageBatch\Inventory\Model\ResourceModel\Reservation as ReservationResource;
use Psr\Log\LoggerInterface;

class FefoAllocation implements FefoAllocationInterface
{
    public function __construct(
        private BatchCollectionFactory $batchCollectionFactory,
        private BatchRepositoryInterface $batchRepository,
        private HistoryRepositoryInterface $historyRepository,
        private HistoryInterfaceFactory $historyFactory,
        private ReservationFactory $reservationFactory,
        private ReservationResource $reservationResource,
        private LoggerInterface $logger
    ) {}

    public function allocate(
        string $sku,
        array $sourceCodes,
        float $qty,
        int $orderId,
        int $orderItemId
    ): array {
        $collection = $this->batchCollectionFactory->create();
        $collection->addFieldToFilter(BatchInterface::PRODUCT_SKU, $sku);
        if (!empty($sourceCodes)) {
            $collection->addFieldToFilter(BatchInterface::SOURCE_CODE, ['in' => $sourceCodes]);
        }
        $collection->addFieldToFilter(BatchInterface::STATUS, BatchInterface::STATUS_ACTIVE);
        $collection->addFieldToFilter('qty_remaining', ['gt' => 0]);
        $collection->setOrder(BatchInterface::EXPIRY_DATE, \Magento\Framework\Data\Collection::SORT_ORDER_ASC);
        $collection->setOrder(BatchInterface::BATCH_ID, \Magento\Framework\Data\Collection::SORT_ORDER_ASC);

        $allocations = [];
        $remaining = $qty;

        foreach ($collection as $batch) {
            if ($remaining <= 0) {
                break;
            }

            $available = (float)$batch->getQtyRemaining();
            $deduct = min($available, $remaining);

            $batch->setQtyRemaining($available - $deduct);
            if ($batch->getQtyRemaining() <= 0) {
                $batch->setStatus(BatchInterface::STATUS_SOLD_OUT);
            }

            $this->batchRepository->save($batch);

            $reservation = $this->reservationFactory->create();
            $reservation->setBatchId((int)$batch->getBatchId());
            $reservation->setOrderId($orderId);
            $reservation->setOrderItemId($orderItemId);
            $reservation->setQty($deduct);
            $this->reservationResource->save($reservation);

            $history = $this->historyFactory->create();
            $history->setBatchId((int)$batch->getBatchId());
            $history->setProductSku($sku);
            $history->setAction(HistoryInterface::ACTION_ALLOCATED);
            $history->setQtyBefore($available);
            $history->setQtyAfter($available - $deduct);
            $this->historyRepository->save($history);

            $allocations[] = ['batch_id' => (int)$batch->getBatchId(), 'qty' => $deduct];
            $remaining -= $deduct;
        }

        if ($remaining > 0) {
            throw new LocalizedException(
                __('Insufficient inventory for SKU %1. Short by %2 units.', $sku, $remaining)
            );
        }

        return $allocations;
    }
}
