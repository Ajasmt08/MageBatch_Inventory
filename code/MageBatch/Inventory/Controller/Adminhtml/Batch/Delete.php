<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Controller\Adminhtml\Batch;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Controller\ResultFactory;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;
use MageBatch\Inventory\Api\BatchRepositoryInterface;

class Delete extends Action
{
    const ADMIN_RESOURCE = 'MageBatch_Inventory::inventory_delete';

    public function __construct(
        Context $context,
        private BatchRepositoryInterface $batchRepository,
        private SourceItemRepositoryInterface $sourceItemRepository,
        private SourceItemsSaveInterface $sourceItemsSave,
        private SearchCriteriaBuilder $searchCriteriaBuilder,
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $batchId = (int)$this->getRequest()->getParam('id');

        try {
            $batch = $this->batchRepository->getById($batchId);
            $sku = $batch->getProductSku();
            $sourceCode = $batch->getSourceCode();
            $qty = (float)$batch->getQtyRemaining();

            $this->batchRepository->delete($batch);

            if ($qty > 0) {
                $this->reduceSourceItemQuantity($sku, $sourceCode, $qty);
            }

            $this->messageManager->addSuccessMessage(__('Batch #%1 has been deleted.', $batchId));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $result->setPath('*/*/index');
    }

    private function reduceSourceItemQuantity(string $sku, string $sourceCode, float $qty): void
    {
        try {
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('sku', $sku)
                ->addFilter('source_code', $sourceCode)
                ->create();

            $items = $this->sourceItemRepository->getList($searchCriteria)->getItems();
            $sourceItem = reset($items);

            if ($sourceItem) {
                $newQty = max(0, (float)$sourceItem->getQuantity() - $qty);
                $sourceItem->setQuantity($newQty);
                $this->sourceItemsSave->execute([$sourceItem]);
            }
        } catch (\Exception $e) {
            return;
        }
    }
}