<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Model;

use MageBatch\Inventory\Api\Data\BatchInterface;
use MageBatch\Inventory\Model\ResourceModel\Batch as BatchResource;
use Magento\Framework\Model\AbstractModel;

class Batch extends AbstractModel implements BatchInterface
{
    protected function _construct()
    {
        $this->_init(BatchResource::class);
    }

    public function getBatchId(): ?int
    {
        return $this->getData(self::BATCH_ID) === null ? null : (int)$this->getData(self::BATCH_ID);
    }

    public function setBatchId(?int $batchId): void
    {
        $this->setData(self::BATCH_ID, $batchId);
    }

    public function getBatchNumber(): ?string
    {
        return $this->getData(self::BATCH_NUMBER);
    }

    public function setBatchNumber(?string $batchNumber): void
    {
        $this->setData(self::BATCH_NUMBER, $batchNumber);
    }

    public function getProductSku(): ?string
    {
        return $this->getData(self::PRODUCT_SKU);
    }

    public function setProductSku(?string $productSku): void
    {
        $this->setData(self::PRODUCT_SKU, $productSku);
    }

    public function getSourceCode(): ?string
    {
        return $this->getData(self::SOURCE_CODE);
    }

    public function setSourceCode(?string $sourceCode): void
    {
        $this->setData(self::SOURCE_CODE, $sourceCode);
    }

    public function getQtyReceived(): ?float
    {
        return $this->getData(self::QTY_RECEIVED) === null ? null : (float)$this->getData(self::QTY_RECEIVED);
    }

    public function setQtyReceived(?float $qtyReceived): void
    {
        $this->setData(self::QTY_RECEIVED, $qtyReceived);
    }

    public function getQtyRemaining(): ?float
    {
        return $this->getData(self::QTY_REMAINING) === null ? null : (float)$this->getData(self::QTY_REMAINING);
    }

    public function setQtyRemaining(?float $qtyRemaining): void
    {
        $this->setData(self::QTY_REMAINING, $qtyRemaining);
    }

    public function getExpiryDate(): ?string
    {
        return $this->getData(self::EXPIRY_DATE);
    }

    public function setExpiryDate(?string $expiryDate): void
    {
        $this->setData(self::EXPIRY_DATE, $expiryDate);
    }

    public function getManufacturingDate(): ?string
    {
        return $this->getData(self::MANUFACTURING_DATE);
    }

    public function setManufacturingDate(?string $manufacturingDate): void
    {
        $this->setData(self::MANUFACTURING_DATE, $manufacturingDate);
    }

    public function getSupplier(): ?string
    {
        return $this->getData(self::SUPPLIER);
    }

    public function setSupplier(?string $supplier): void
    {
        $this->setData(self::SUPPLIER, $supplier);
    }

    public function getPurchaseOrder(): ?string
    {
        return $this->getData(self::PURCHASE_ORDER);
    }

    public function setPurchaseOrder(?string $purchaseOrder): void
    {
        $this->setData(self::PURCHASE_ORDER, $purchaseOrder);
    }

    public function getCostPrice(): ?float
    {
        return $this->getData(self::COST_PRICE) === null ? null : (float)$this->getData(self::COST_PRICE);
    }

    public function setCostPrice(?float $costPrice): void
    {
        $this->setData(self::COST_PRICE, $costPrice);
    }

    public function getStatus(): ?int
    {
        return $this->getData(self::STATUS) === null ? null : (int)$this->getData(self::STATUS);
    }

    public function setStatus(?int $status): void
    {
        $this->setData(self::STATUS, $status);
    }

    public function getNotes(): ?string
    {
        return $this->getData(self::NOTES);
    }

    public function setNotes(?string $notes): void
    {
        $this->setData(self::NOTES, $notes);
    }

    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATED_AT);
    }

    public function setCreatedAt(?string $createdAt): void
    {
        $this->setData(self::CREATED_AT, $createdAt);
    }
}
