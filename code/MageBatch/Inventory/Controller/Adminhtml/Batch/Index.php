<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Controller\Adminhtml\Batch;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    const ADMIN_RESOURCE = 'MageBatch_Inventory::inventory_view';

    public function __construct(
        Context $context,
        private PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('MageBatch_Inventory::inventory');
        $resultPage->addBreadcrumb(__('Inventory Batches'), __('Inventory Batches'));
        $resultPage->getConfig()->getTitle()->prepend(__('Inventory Batches'));
        return $resultPage;
    }
}
