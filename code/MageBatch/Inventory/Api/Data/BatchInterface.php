<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Api\Data;

interface BatchInterface
{
    public const BATCH_ID = 'batch_id';
    public const BATCH_NUMBER = 'batch_number';
    public const PRODUCT_SKU = 'product_sku';
    public const SOURCE_CODE = 'source_code';
    public const QTY_RECEIVED = 'qty_received';
    public const QTY_REMAINING = 'qty_remaining';
    public const EXPIRY_DATE = 'expiry_date';
    public const MANUFACTURING_DATE = 'manufacturing_date';
    public const SUPPLIER = 'supplier';
    public const PURCHASE_ORDER = 'purchase_order';
    public const COST_PRICE = 'cost_price';
    public const STATUS = 'status';
    public const NOTES = 'notes';
    public const CREATED_AT = 'created_at';

    public const STATUS_ACTIVE = 1;
    public const STATUS_EXPIRED = 3;
    public const STATUS_RECALLED = 4;
    public const STATUS_SOLD_OUT = 7;

    public function getBatchId(): ?int;

    public function setBatchId(?int $batchId): void;

    public function getBatchNumber(): ?string;

    public function setBatchNumber(?string $batchNumber): void;

    public function getProductSku(): ?string;

    public function setProductSku(?string $productSku): void;

    public function getSourceCode(): ?string;

    public function setSourceCode(?string $sourceCode): void;

    public function getQtyReceived(): ?float;

    public function setQtyReceived(?float $qtyReceived): void;

    public function getQtyRemaining(): ?float;

    public function setQtyRemaining(?float $qtyRemaining): void;

    public function getExpiryDate(): ?string;

    public function setExpiryDate(?string $expiryDate): void;

    public function getManufacturingDate(): ?string;

    public function setManufacturingDate(?string $manufacturingDate): void;

    public function getSupplier(): ?string;

    public function setSupplier(?string $supplier): void;

    public function getPurchaseOrder(): ?string;

    public function setPurchaseOrder(?string $purchaseOrder): void;

    public function getCostPrice(): ?float;

    public function setCostPrice(?float $costPrice): void;

    public function getStatus(): ?int;

    public function setStatus(?int $status): void;

    public function getNotes(): ?string;

    public function setNotes(?string $notes): void;

    public function getCreatedAt(): ?string;

    public function setCreatedAt(?string $createdAt): void;
}
