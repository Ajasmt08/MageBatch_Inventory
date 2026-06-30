<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface BatchSearchResultInterface extends SearchResultsInterface
{
    public function getItems(): array;
    public function setItems(array $items): void;
}
