<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class History extends AbstractDb
{
    const TABLE_NAME = 'magebatch_inventory_history';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, 'history_id');
    }
}
