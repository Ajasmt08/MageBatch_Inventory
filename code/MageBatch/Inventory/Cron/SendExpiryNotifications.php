<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Cron;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use MageBatch\Inventory\Model\Config;
use MageBatch\Inventory\Model\ResourceModel\Batch\CollectionFactory;

class SendExpiryNotifications
{
    public function __construct(
        private Config $config,
        private CollectionFactory $collectionFactory,
        private TransportBuilder $transportBuilder,
        private StoreManagerInterface $storeManager,
        private LoggerInterface $logger
    ) {}

    public function execute(): void
    {
        if (!$this->config->isExpiryAlertsEnabled()) {
            return;
        }

        try {
            $threshold = $this->config->getNearExpiryThreshold();
            $date = (new \DateTime())->modify("+{$threshold} days")->format('Y-m-d');

            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('status', \MageBatch\Inventory\Api\Data\BatchInterface::STATUS_ACTIVE);
            $collection->addFieldToFilter('expiry_date', ['lteq' => $date]);

            if ($collection->getSize() === 0) {
                return;
            }

            $email = $this->config->getNotificationEmail();
            if (empty($email)) {
                return;
            }

            $store = $this->storeManager->getStore();
            $transport = $this->transportBuilder
                ->setTemplateIdentifier('magebatch_inventory_expiry_alert')
                ->setTemplateVars(['batches' => $collection->getItems()])
                ->setFromByScope('general')
                ->addTo($email)
                ->getTransport();

            $transport->sendMessage();
        } catch (\Exception $e) {
            $this->logger->error('MageBatch: Expiry notification failed: ' . $e->getMessage());
        }
    }
}
