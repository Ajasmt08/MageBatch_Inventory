<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Controller\Adminhtml\Batch;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use MageBatch\Inventory\Api\BatchManagementInterface;

class Recall extends Action
{
    const ADMIN_RESOURCE = 'MageBatch_Inventory::inventory_recall';

    public function __construct(
        Context $context,
        private BatchManagementInterface $batchManagement
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $batchId = (int)$this->getRequest()->getParam('id');
        $reason = $this->getRequest()->getParam('reason');

        try {
            $this->batchManagement->recall($batchId, $reason);
            $this->messageManager->addSuccessMessage(__('Batch #%1 has been recalled.', $batchId));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $result->setPath('*/*/index');
    }
}
