<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use MageBatch\Inventory\Api\BatchRepositoryInterface;
use MageBatch\Inventory\Api\Data\BatchInterface;
use MageBatch\Inventory\Model\Batch\Source\Status;

class Edit extends Template
{
    public function __construct(
        Template\Context $context,
        private BatchRepositoryInterface $batchRepository,
        private Status $statusSource,
        private SourceRepositoryInterface $sourceRepository,
        private SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getBatch(): ?BatchInterface
    {
        $batchId = (int)$this->getRequest()->getParam('id');
        if (!$batchId) {
            return null;
        }
        try {
            return $this->batchRepository->getById($batchId);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getStatusOptions(): array
    {
        return $this->statusSource->toOptionArray();
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
}
