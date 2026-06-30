<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Api;

use MageBatch\Inventory\Api\Data\BatchInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface BatchRepositoryInterface
{
    public function getById(int $batchId): BatchInterface;

    public function save(BatchInterface $batch): BatchInterface;

    public function delete(BatchInterface $batch): bool;

    public function getList(SearchCriteriaInterface $searchCriteria): BatchSearchResultInterface;
}
