<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Plugin;

use Magento\InventorySalesApi\Api\PlaceReservationsForSalesEventInterface;
use Magento\InventorySalesApi\Api\Data\SalesEventInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use MageBatch\Inventory\Api\FefoAllocationInterface;
use MageBatch\Inventory\Model\Config;
use MageBatch\Inventory\Model\ResourceModel\Batch\CollectionFactory;
use MageBatch\Inventory\Api\Data\BatchInterface;

class InventoryReservationPlacing
{
    public function __construct(
        private FefoAllocationInterface $fefoAllocation,
        private Config $config,
        private CollectionFactory $batchCollectionFactory
    ) {}

    public function aroundExecute(
        PlaceReservationsForSalesEventInterface $subject,
        callable $proceed,
        array $items,
        SalesChannelInterface $salesChannel,
        SalesEventInterface $salesEvent
    ): void {
        if (!$this->config->isFefoEnabled()) {
            $proceed($items, $salesChannel, $salesEvent);
            return;
        }

        if ($salesEvent->getType() !== SalesEventInterface::EVENT_ORDER_PLACED) {
            $proceed($items, $salesChannel, $salesEvent);
            return;
        }

        $orderId = (int)$salesEvent->getObjectId();
        $batchSkus = $this->getBatchManagedSkus();
        $batchItems = [];
        $normalItems = [];

        foreach ($items as $item) {
            if (in_array($item->getSku(), $batchSkus, true)) {
                $batchItems[] = $item;
            } else {
                $normalItems[] = $item;
            }
        }

        foreach ($batchItems as $item) {
            $this->fefoAllocation->allocate(
                $item->getSku(),
                [],
                abs((float)$item->getQuantity()),
                $orderId,
                0
            );
        }

        if (!empty($batchItems)) {
            $proceed($batchItems, $salesChannel, $salesEvent);
        }

        if (!empty($normalItems)) {
            $proceed($normalItems, $salesChannel, $salesEvent);
        }
    }

    private function getBatchManagedSkus(): array
    {
        $collection = $this->batchCollectionFactory->create();
        $collection->addFieldToFilter(BatchInterface::QTY_REMAINING, ['gt' => 0]);
        $collection->addFieldToSelect(BatchInterface::PRODUCT_SKU);
        $collection->getSelect()->group(BatchInterface::PRODUCT_SKU);
        return array_map(fn($b) => $b->getProductSku(), $collection->getItems());
    }
}
