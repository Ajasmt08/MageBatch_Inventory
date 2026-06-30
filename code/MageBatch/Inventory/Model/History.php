<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Model;

use Magento\Framework\Model\AbstractModel;
use MageBatch\Inventory\Api\Data\HistoryInterface;
use MageBatch\Inventory\Model\ResourceModel\History as HistoryResource;

class History extends AbstractModel implements HistoryInterface
{
    protected function _construct()
    {
        $this->_init(HistoryResource::class);
    }

    public function getHistoryId(): ?int
    {
        return $this->getData(self::HISTORY_ID) ? (int)$this->getData(self::HISTORY_ID) : null;
    }

    public function setHistoryId(int $id): self
    {
        return $this->setData(self::HISTORY_ID, $id);
    }

    public function getBatchId(): ?int
    {
        return $this->getData(self::BATCH_ID) ? (int)$this->getData(self::BATCH_ID) : null;
    }

    public function setBatchId(int $batchId): self
    {
        return $this->setData(self::BATCH_ID, $batchId);
    }

    public function getProductSku(): string
    {
        return (string)$this->getData(self::PRODUCT_SKU);
    }

    public function setProductSku(string $sku): self
    {
        return $this->setData(self::PRODUCT_SKU, $sku);
    }

    public function getAction(): string
    {
        return (string)$this->getData(self::ACTION);
    }

    public function setAction(string $action): self
    {
        return $this->setData(self::ACTION, $action);
    }

    public function getQtyBefore(): ?float
    {
        return $this->getData(self::QTY_BEFORE) !== null ? (float)$this->getData(self::QTY_BEFORE) : null;
    }

    public function setQtyBefore(?float $qty): self
    {
        return $this->setData(self::QTY_BEFORE, $qty);
    }

    public function getQtyAfter(): ?float
    {
        return $this->getData(self::QTY_AFTER) !== null ? (float)$this->getData(self::QTY_AFTER) : null;
    }

    public function setQtyAfter(?float $qty): self
    {
        return $this->setData(self::QTY_AFTER, $qty);
    }

    public function getAdminId(): ?int
    {
        return $this->getData(self::ADMIN_ID) ? (int)$this->getData(self::ADMIN_ID) : null;
    }

    public function setAdminId(?int $adminId): self
    {
        return $this->setData(self::ADMIN_ID, $adminId);
    }

    public function getReason(): ?string
    {
        return $this->getData(self::REASON);
    }

    public function setReason(?string $reason): self
    {
        return $this->setData(self::REASON, $reason);
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
