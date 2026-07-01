<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\InventoryApi\Api\SourceRepositoryInterface;

class Receive extends Template
{
    public function __construct(
        Template\Context $context,
        private SourceRepositoryInterface $sourceRepository,
        private SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getSourceOptions(): array
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
            $options[] = ['value' => 'default', 'label' => __('Default (default)')];
        }
        return $options;
    }

    public function getSourceManageUrl(): string
    {
        return $this->getUrl('inventory/source/index');
    }

    public function getProductSearchUrl(): string
    {
        return $this->getUrl('magebatch_inventory/product/search');
    }
}
