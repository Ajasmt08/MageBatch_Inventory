<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Model;

use MageBatch\Inventory\Api\BatchSearchResultInterface;
use Magento\Framework\Api\SearchResults;

class BatchSearchResult extends SearchResults implements BatchSearchResultInterface
{
    public function getItems(): array
    {
        return parent::getItems();
    }

    public function setItems(array $items): void
    {
        parent::setItems($items);
    }
}
