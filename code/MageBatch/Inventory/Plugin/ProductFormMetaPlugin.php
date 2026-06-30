<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Plugin;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\ProductDataProvider;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use MageBatch\Inventory\Api\Data\BatchInterface;
use MageBatch\Inventory\Model\ResourceModel\Batch\CollectionFactory;

class ProductFormMetaPlugin
{
    public function __construct(
        private UrlInterface $urlBuilder,
        private RequestInterface $request,
        private SourceRepositoryInterface $sourceRepository,
        private SearchCriteriaBuilder $searchCriteriaBuilder,
        private CollectionFactory $batchCollectionFactory,
        private ProductRepositoryInterface $productRepository
    ) {}

    public function afterGetMeta(ProductDataProvider $subject, array $meta): array
    {
        $meta = array_merge_recursive($meta, [
            'batch_stock' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Batch Stock Management'),
                            'componentType' => 'fieldset',
                            'collapsible' => true,
                            'opened' => false,
                            'sortOrder' => 50,
                        ],
                    ],
                ],
                'children' => $this->getFields(),
            ],
        ]);

        return $meta;
    }

    private function getFields(): array
    {
        $productId = (int)$this->request->getParam('id');
        $submitUrl = $this->urlBuilder->getUrl('magebatch_inventory/product/receiveStock', [
            'product_id' => $productId,
        ]);

        return [
            'batch_table' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => null,
                            'componentType' => 'container',
                            'component' => 'Magento_Ui/js/form/components/html',
                            'content' => $this->renderBatchTable($productId),
                            'sortOrder' => 5,
                        ],
                    ],
                ],
            ],
            'source_code' => $this->getSelectField('Source', 10, 'source_code'),
            'batch_number' => $this->getInputField('Batch Number', 20, 'batch_number'),
            'qty' => $this->getNumberField('Quantity', 30, 'qty'),
            'expiry_date' => $this->getDateField('Expiry Date', 40, 'expiry_date'),
            'manufacturing_date' => $this->getDateField('Manufacturing Date', 50, 'manufacturing_date'),
            'supplier' => $this->getInputField('Supplier', 60, 'supplier'),
            'purchase_order' => $this->getInputField('Purchase Order', 70, 'purchase_order'),
            'cost_price' => $this->getNumberField('Cost Price', 80, 'cost_price'),
            'notes' => $this->getTextareaField('Notes', 90, 'notes'),
            'receive_button' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => 'container',
                            'component' => 'MageBatch_Inventory/js/product/receive-stock',
                            'template' => 'MageBatch_Inventory/product/receive-stock-button',
                            'ajaxUrl' => $submitUrl,
                            'sortOrder' => 100,
                        ],
                    ],
                ],
            ],
        ];
    }

    private function renderBatchTable(int $productId): string
    {
        try {
            $product = $this->productRepository->getById($productId);
            $sku = $product->getSku();

            $collection = $this->batchCollectionFactory->create();
            $collection->addFieldToFilter(BatchInterface::PRODUCT_SKU, $sku);
            $collection->setOrder(BatchInterface::EXPIRY_DATE, \Magento\Framework\Data\Collection::SORT_ORDER_ASC);

            $rows = '';
            foreach ($collection as $batch) {
                $statusLabels = [
                    BatchInterface::STATUS_ACTIVE => 'Active',
                    BatchInterface::STATUS_SOLD_OUT => 'Sold Out',
                    BatchInterface::STATUS_EXPIRED => 'Expired',
                    BatchInterface::STATUS_RECALLED => 'Recalled',
                    BatchInterface::STATUS_DAMAGED => 'Damaged',
                    BatchInterface::STATUS_QUARANTINED => 'Quarantined',
                    BatchInterface::STATUS_RESERVED => 'Reserved',
                ];
                $status = (int)$batch->getStatus();
                $statusLabel = (string)__($statusLabels[$status] ?? 'Unknown');

                $cls = 'grid-severity-notice';
                if ($status === BatchInterface::STATUS_EXPIRED || $status === BatchInterface::STATUS_RECALLED) {
                    $cls = 'grid-severity-critical';
                } elseif ($status === BatchInterface::STATUS_SOLD_OUT) {
                    $cls = 'grid-severity-minor';
                }

                $rows .= '<tr>'
                    . '<td>' . $batch->getBatchNumber() . '</td>'
                    . '<td>' . $batch->getSourceCode() . '</td>'
                    . '<td>' . number_format((float)$batch->getQtyRemaining(), 2) . '</td>'
                    . '<td>' . $batch->getExpiryDate() . '</td>'
                    . '<td><span class="' . $cls . '"><span>' . $statusLabel . '</span></span></td>'
                    . '</tr>';
            }

            if (empty($rows)) {
                return '<div class="message notice">' . __('No batches found for this product.') . '</div>';
            }

            return '<div class="admin__data-grid-wrap" style="margin-top:15px;max-height:300px;overflow-y:auto">'
                . '<table class="data-grid">'
                . '<thead><tr>'
                . '<th class="data-grid-th">' . __('Batch #') . '</th>'
                . '<th class="data-grid-th">' . __('Source') . '</th>'
                . '<th class="data-grid-th">' . __('Qty Remaining') . '</th>'
                . '<th class="data-grid-th">' . __('Expiry Date') . '</th>'
                . '<th class="data-grid-th">' . __('Status') . '</th>'
                . '</tr></thead>'
                . '<tbody>' . $rows . '</tbody>'
                . '</table></div>';
        } catch (\Exception $e) {
            return '<div class="message error">' . __('Could not load batch data.') . '</div>';
        }
    }

    private function getInputField(string $label, int $sortOrder, string $dataScope): array
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __($label),
                        'componentType' => 'field',
                        'formElement' => 'input',
                        'dataType' => 'text',
                        'dataScope' => $dataScope,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];
    }

    private function getSelectField(string $label, int $sortOrder, string $dataScope): array
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __($label),
                        'componentType' => 'field',
                        'formElement' => 'select',
                        'dataType' => 'text',
                        'dataScope' => $dataScope,
                        'sortOrder' => $sortOrder,
                        'options' => $this->getSourceOptions(),
                    ],
                ],
            ],
        ];
    }

    private function getNumberField(string $label, int $sortOrder, string $dataScope): array
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __($label),
                        'componentType' => 'field',
                        'formElement' => 'input',
                        'dataType' => 'number',
                        'dataScope' => $dataScope,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];
    }

    private function getDateField(string $label, int $sortOrder, string $dataScope): array
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __($label),
                        'componentType' => 'field',
                        'formElement' => 'date',
                        'dataType' => 'date',
                        'dataScope' => $dataScope,
                        'sortOrder' => $sortOrder,
                        'options' => [
                            'dateFormat' => 'Y-m-d',
                            'showsTime' => false,
                        ],
                    ],
                ],
            ],
        ];
    }

    private function getTextareaField(string $label, int $sortOrder, string $dataScope): array
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __($label),
                        'componentType' => 'field',
                        'formElement' => 'textarea',
                        'dataType' => 'text',
                        'dataScope' => $dataScope,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];
    }

    private function getSourceOptions(): array
    {
        $options = [];
        try {
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $sources = $this->sourceRepository->getList($searchCriteria)->getItems();
            foreach ($sources as $source) {
                $options[] = [
                    'value' => $source->getSourceCode(),
                    'label' => $source->getName() . ' (' . $source->getSourceCode() . ')',
                ];
            }
        } catch (\Exception $e) {
            $options[] = ['value' => 'default', 'label' => 'Default'];
        }
        return $options;
    }
}
