<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Model;

use Magento\Framework\Model\AbstractModel;
use MageBatch\Inventory\Model\ResourceModel\Reservation as ReservationResource;

class Reservation extends AbstractModel
{
    const RESERVATION_ID = 'reservation_id';
    const BATCH_ID = 'batch_id';
    const ORDER_ID = 'order_id';
    const ORDER_ITEM_ID = 'order_item_id';
    const QTY = 'qty';
    const CREATED_AT = 'created_at';

    protected function _construct()
    {
        $this->_init(ReservationResource::class);
    }

    public function getReservationId(): ?int
    {
        return $this->getData(self::RESERVATION_ID) ? (int)$this->getData(self::RESERVATION_ID) : null;
    }

    public function setReservationId(int $id): self
    {
        return $this->setData(self::RESERVATION_ID, $id);
    }

    public function getBatchId(): int
    {
        return (int)$this->getData(self::BATCH_ID);
    }

    public function setBatchId(int $batchId): self
    {
        return $this->setData(self::BATCH_ID, $batchId);
    }

    public function getOrderId(): int
    {
        return (int)$this->getData(self::ORDER_ID);
    }

    public function setOrderId(int $orderId): self
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    public function getOrderItemId(): int
    {
        return (int)$this->getData(self::ORDER_ITEM_ID);
    }

    public function setOrderItemId(int $itemId): self
    {
        return $this->setData(self::ORDER_ITEM_ID, $itemId);
    }

    public function getQty(): float
    {
        return (float)$this->getData(self::QTY);
    }

    public function setQty(float $qty): self
    {
        return $this->setData(self::QTY, $qty);
    }

    public function getCreatedAt(): string
    {
        return (string)$this->getData(self::CREATED_AT);
    }

    public function setCreatedAt(string $createdAt): self
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}
