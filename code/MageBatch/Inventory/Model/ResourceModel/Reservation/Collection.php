<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Model\ResourceModel\Reservation;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use MageBatch\Inventory\Model\Reservation;
use MageBatch\Inventory\Model\ResourceModel\Reservation as ReservationResource;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Reservation::class, ReservationResource::class);
    }
}
