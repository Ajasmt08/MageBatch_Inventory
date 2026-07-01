<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Controller\Adminhtml\Batch;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Receive extends Action
{
    const ADMIN_RESOURCE = 'MageBatch_Inventory::inventory_receive';

    public function __construct(
        Context $context,
        private PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('MageBatch_Inventory::receive');
        $resultPage->addBreadcrumb(__('Receive Stock'), __('Receive Stock'));
        $resultPage->getConfig()->getTitle()->prepend(__('Receive Stock'));
        return $resultPage;
    }
}
