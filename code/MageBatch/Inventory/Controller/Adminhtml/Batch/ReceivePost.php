<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Controller\Adminhtml\Batch;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use MageBatch\Inventory\Api\StockReceivingInterface;

class ReceivePost extends Action
{
    const ADMIN_RESOURCE = 'MageBatch_Inventory::inventory_receive';

    public function __construct(
        Context $context,
        private StockReceivingInterface $stockReceiving
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        try {
            $this->stockReceiving->receive(
                $data['product_sku'] ?? '',
                $data['source_code'] ?? '',
                $data['batch_number'] ?? '',
                (float)($data['qty'] ?? 0),
                $data['expiry_date'] ?? '',
                $data['manufacturing_date'] ?? null
            );
            $this->messageManager->addSuccessMessage(__('Stock received successfully.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $result->setPath('*/*/index');
    }
}
