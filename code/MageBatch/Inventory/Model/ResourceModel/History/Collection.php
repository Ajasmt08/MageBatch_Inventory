<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Model\ResourceModel\History;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use MageBatch\Inventory\Model\History;
use MageBatch\Inventory\Model\ResourceModel\History as HistoryResource;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(History::class, HistoryResource::class);
    }
}
