<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Controller\Adminhtml\Batch;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use MageBatch\Inventory\Api\BatchManagementInterface;
use MageBatch\Inventory\Api\BatchRepositoryInterface;
use MageBatch\Inventory\Api\HistoryRepositoryInterface;
use MageBatch\Inventory\Api\Data\HistoryInterface;
use MageBatch\Inventory\Api\Data\HistoryInterfaceFactory;

class EditPost extends Action
{
    const ADMIN_RESOURCE = 'MageBatch_Inventory::inventory_edit';

    private array $fieldMap = [
        'batch_number' => 'setBatchNumber',
        'source_code' => 'setSourceCode',
        'qty' => 'setQtyRemaining',
        'expiry_date' => 'setExpiryDate',
        'manufacturing_date' => 'setManufacturingDate',
        'supplier' => 'setSupplier',
        'purchase_order' => 'setPurchaseOrder',
        'cost_price' => 'setCostPrice',
        'status' => 'setStatus',
        'notes' => 'setNotes',
    ];

    public function __construct(
        Context $context,
        private BatchRepositoryInterface $batchRepository,
        private BatchManagementInterface $batchManagement,
        private HistoryRepositoryInterface $historyRepository,
        private HistoryInterfaceFactory $historyFactory,
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        try {
            $batchId = (int)($data['batch_id'] ?? 0);
            $batch = $this->batchRepository->getById($batchId);
            $oldQty = $batch->getQtyRemaining();

            foreach ($this->fieldMap as $field => $setter) {
                if (isset($data[$field])) {
                    $value = $data[$field];
                    if (in_array($field, ['qty', 'cost_price'], true)) {
                        $value = (float)$value;
                    }
                    if ($field === 'status') {
                        $value = (int)$value;
                    }
                    $batch->$setter($value);
                }
            }

            $this->batchManagement->applyStatusInventoryChanges($batch);
            $this->batchManagement->syncQuantity($batch, $oldQty);
            $this->batchRepository->save($batch);

            $history = $this->historyFactory->create();
            $history->setBatchId($batchId);
            $history->setProductSku($batch->getProductSku());
            $history->setAction(HistoryInterface::ACTION_EDITED);
            $history->setQtyBefore((float)$oldQty);
            $history->setQtyAfter((float)$batch->getQtyRemaining());
            $history->setAdminId((int)$this->_auth->getUser()->getId());
            $this->historyRepository->save($history);

            $this->messageManager->addSuccessMessage(__('Batch #%1 has been updated.', $batchId));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $result->setPath('*/*/index');
    }
}