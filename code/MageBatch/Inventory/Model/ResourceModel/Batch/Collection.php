<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Model\ResourceModel\Batch;

use MageBatch\Inventory\Model\Batch as BatchModel;
use MageBatch\Inventory\Model\ResourceModel\Batch as BatchResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(BatchModel::class, BatchResource::class);
    }
}
