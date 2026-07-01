<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use MageBatch\Inventory\Api\HistoryRepositoryInterface;
use MageBatch\Inventory\Api\Data\HistoryInterface;
use MageBatch\Inventory\Model\ResourceModel\History as HistoryResource;
use MageBatch\Inventory\Model\ResourceModel\History\CollectionFactory;

class HistoryRepository implements HistoryRepositoryInterface
{
    public function __construct(
        private HistoryFactory $historyFactory,
        private CollectionFactory $collectionFactory,
        private HistoryResource $resource,
        private CollectionProcessorInterface $collectionProcessor
    ) {}

    public function getById(int $id): HistoryInterface
    {
        $history = $this->historyFactory->create();
        $this->resource->load($history, $id);
        if (!$history->getHistoryId()) {
            throw NoSuchEntityException::singleField('history_id', $id);
        }
        return $history;
    }

    public function save(HistoryInterface $history): HistoryInterface
    {
        try {
            $this->resource->save($history);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save history: %1', $e->getMessage()), $e);
        }
        return $history;
    }

    public function getList(SearchCriteriaInterface $searchCriteria): \Magento\Framework\Api\SearchResultsInterface
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResult = new \Magento\Framework\Api\SearchResults();
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());
        return $searchResult;
    }
}
