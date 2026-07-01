<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Ui\Component\Batch;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use MageBatch\Inventory\Api\Data\BatchInterface;

class StatusColumn extends Column
{
    private const LABELS = [
        BatchInterface::STATUS_ACTIVE => 'Active',
        BatchInterface::STATUS_EXPIRED => 'Expired',
        BatchInterface::STATUS_RECALLED => 'Recalled',
        BatchInterface::STATUS_SOLD_OUT => 'Sold Out',
    ];

    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items']) && is_array($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (is_array($item) && isset($item['status'])) {
                    $item['status'] = self::LABELS[(int)$item['status']] ?? 'Unknown';
                }
            }
        }
        return $dataSource;
    }
}
