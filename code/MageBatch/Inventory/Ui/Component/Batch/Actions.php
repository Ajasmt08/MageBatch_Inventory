<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Ui\Component\Batch;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Actions extends Column
{
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        private UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')] = [
                    'history' => [
                        'href' => $this->urlBuilder->getUrl('magebatch_inventory/batch/history', ['id' => $item['batch_id']]),
                        'label' => __('History'),
                    ],
                ];
            }
        }

        return $dataSource;
    }
}
