<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Api;

use MageBatch\Inventory\Api\Data\HistoryInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;

interface HistoryRepositoryInterface
{
    public function getById(int $id): HistoryInterface;

    public function save(HistoryInterface $history): HistoryInterface;

    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;
}
