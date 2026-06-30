<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Ui\DataProvider\Batch;

use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use MageBatch\Inventory\Model\ResourceModel\Batch\CollectionFactory;

class ListingDataProvider extends DataProvider
{
    private CollectionFactory $batchCollectionFactory;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\Api\Search\ReportingInterface $reporting,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        CollectionFactory $batchCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        $this->batchCollectionFactory = $batchCollectionFactory;
    }

    public function getData(): array
    {
        $collection = $this->batchCollectionFactory->create();
        return $collection->toArray();
    }
}
