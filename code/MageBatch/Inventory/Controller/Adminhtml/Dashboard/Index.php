<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Controller\Adminhtml\Dashboard;

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
        $resultPage->setActiveMenu('MageBatch_Inventory::dashboard');
        $resultPage->addBreadcrumb(__('Inventory Dashboard'), __('Inventory Dashboard'));
        $resultPage->getConfig()->getTitle()->prepend(__('Inventory Dashboard'));
        return $resultPage;
    }
}
