<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Controller\Adminhtml\Batch;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action
{
    const ADMIN_RESOURCE = 'MageBatch_Inventory::inventory_edit';

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
        $resultPage->addBreadcrumb(__('Edit Batch'), __('Edit Batch'));
        $resultPage->getConfig()->getTitle()->prepend(__('Edit Batch'));
        return $resultPage;
    }
}
