<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Controller\Adminhtml\Batch;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use MageBatch\Inventory\Api\BatchRepositoryInterface;

class EditPost extends Action
{
    const ADMIN_RESOURCE = 'MageBatch_Inventory::inventory_edit';

    public function __construct(
        Context $context,
        private BatchRepositoryInterface $batchRepository
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        try {
            $batchId = (int)($data['batch_id'] ?? 0);
            $batch = $this->batchRepository->getById($batchId);

            if (isset($data['status'])) {
                $batch->setStatus((int)$data['status']);
            }
            if (!empty($data['expiry_date'])) {
                $batch->setExpiryDate($data['expiry_date']);
            }

            $this->batchRepository->save($batch);
            $this->messageManager->addSuccessMessage(__('Batch #%1 has been updated.', $batchId));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $result->setPath('*/*/index');
    }
}
