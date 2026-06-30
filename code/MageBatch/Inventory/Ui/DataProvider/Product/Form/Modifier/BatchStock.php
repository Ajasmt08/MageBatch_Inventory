<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;

class BatchStock extends AbstractModifier
{
    public function __construct(
        private UrlInterface $urlBuilder,
        private RequestInterface $request,
        private SourceRepositoryInterface $sourceRepository,
        private SearchCriteriaBuilder $searchCriteriaBuilder
    ) {}

    public function modifyData(array $data): array
    {
        return $data;
    }

    public function modifyMeta(array $meta): array
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
                            'dataScope' => 'data',
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
            'source_code' => $this->getSelectField('Source', 10),
            'batch_number' => $this->getInputField('Batch Number', 20),
            'qty' => $this->getNumberField('Quantity', 30),
            'expiry_date' => $this->getDateField('Expiry Date', 40),
            'manufacturing_date' => $this->getDateField('Manufacturing Date', 50),
            'supplier' => $this->getInputField('Supplier', 60),
            'purchase_order' => $this->getInputField('Purchase Order', 70),
            'cost_price' => $this->getNumberField('Cost Price', 80),
            'notes' => $this->getTextareaField('Notes', 90),
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

    private function getInputField(string $label, int $sortOrder): array
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __($label),
                        'componentType' => 'field',
                        'formElement' => 'input',
                        'dataType' => 'text',
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];
    }

    private function getSelectField(string $label, int $sortOrder): array
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __($label),
                        'componentType' => 'field',
                        'formElement' => 'select',
                        'dataType' => 'text',
                        'sortOrder' => $sortOrder,
                        'options' => $this->getSourceOptions(),
                    ],
                ],
            ],
        ];
    }

    private function getNumberField(string $label, int $sortOrder): array
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __($label),
                        'componentType' => 'field',
                        'formElement' => 'input',
                        'dataType' => 'number',
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];
    }

    private function getDateField(string $label, int $sortOrder): array
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __($label),
                        'componentType' => 'field',
                        'formElement' => 'date',
                        'dataType' => 'date',
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

    private function getTextareaField(string $label, int $sortOrder): array
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __($label),
                        'componentType' => 'field',
                        'formElement' => 'textarea',
                        'dataType' => 'text',
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
