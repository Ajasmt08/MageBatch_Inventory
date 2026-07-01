<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Ui\DataProvider\History;

use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use MageBatch\Inventory\Model\ResourceModel\History\CollectionFactory;

class ListingDataProvider extends DataProvider
{
    private CollectionFactory $historyCollectionFactory;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\Api\Search\ReportingInterface $reporting,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        CollectionFactory $historyCollectionFactory,
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
        $this->historyCollectionFactory = $historyCollectionFactory;
    }

    public function getData(): array
    {
        $collection = $this->historyCollectionFactory->create();
        return $collection->toArray();
    }
}
