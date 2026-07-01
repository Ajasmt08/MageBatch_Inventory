<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Batch extends AbstractDb
{
    public const TABLE_NAME_BATCH = 'magebatch_inventory_batch';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME_BATCH, \MageBatch\Inventory\Api\Data\BatchInterface::BATCH_ID);
    }
}
