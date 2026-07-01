<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Api\Data;

interface HistoryInterface
{
    const HISTORY_ID = 'history_id';
    const BATCH_ID = 'batch_id';
    const PRODUCT_SKU = 'product_sku';
    const ACTION = 'action';
    const QTY_BEFORE = 'qty_before';
    const QTY_AFTER = 'qty_after';
    const ADMIN_ID = 'admin_id';
    const REASON = 'reason';
    const CREATED_AT = 'created_at';

    const ACTION_RECEIVED = 'received';
    const ACTION_ALLOCATED = 'allocated';
    const ACTION_EXPIRED = 'expired';
    const ACTION_EDITED = 'edited';
    const ACTION_STATUS_CHANGE = 'status_change';

    public function getHistoryId(): ?int;
    public function setHistoryId(int $id): self;
    public function getBatchId(): ?int;
    public function setBatchId(int $batchId): self;
    public function getProductSku(): string;
    public function setProductSku(string $sku): self;
    public function getAction(): string;
    public function setAction(string $action): self;
    public function getQtyBefore(): ?float;
    public function setQtyBefore(?float $qty): self;
    public function getQtyAfter(): ?float;
    public function setQtyAfter(?float $qty): self;
    public function getAdminId(): ?int;
    public function setAdminId(?int $adminId): self;
    public function getReason(): ?string;
    public function setReason(?string $reason): self;
    public function getCreatedAt(): string;
    public function setCreatedAt(string $createdAt): self;
}
