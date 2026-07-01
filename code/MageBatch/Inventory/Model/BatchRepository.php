<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use MageBatch\Inventory\Api\BatchRepositoryInterface;
use MageBatch\Inventory\Api\BatchSearchResultInterface;
use MageBatch\Inventory\Api\Data\BatchInterface;
use MageBatch\Inventory\Model\ResourceModel\Batch as BatchResource;
use MageBatch\Inventory\Model\ResourceModel\Batch\CollectionFactory;

class BatchRepository implements BatchRepositoryInterface
{
    public function __construct(
        private BatchFactory $batchFactory,
        private CollectionFactory $collectionFactory,
        private BatchResource $resource,
        private CollectionProcessorInterface $collectionProcessor,
        private BatchSearchResultFactory $searchResultFactory
    ) {}

    public function getById(int $batchId): BatchInterface
    {
        $batch = $this->batchFactory->create();
        $this->resource->load($batch, $batchId);
        if (!$batch->getBatchId()) {
            throw NoSuchEntityException::singleField('batch_id', $batchId);
        }
        return $batch;
    }

    public function save(BatchInterface $batch): BatchInterface
    {
        try {
            $originalStatus = null;
            if ($batch->getBatchId()) {
                $original = $this->batchFactory->create();
                $this->resource->load($original, $batch->getBatchId());
                if ($original->getBatchId()) {
                    $originalStatus = (int)$original->getStatus();
                }
            }

            $this->resource->save($batch);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save batch: %1', $e->getMessage()), $e);
        }
        return $batch;
    }

    public function delete(BatchInterface $batch): bool
    {
        try {
            $this->resource->delete($batch);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete batch: %1', $e->getMessage()), $e);
        }
        return true;
    }

    public function getList(SearchCriteriaInterface $searchCriteria): BatchSearchResultInterface
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResult = $this->searchResultFactory->create();
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());
        return $searchResult;
    }
}
