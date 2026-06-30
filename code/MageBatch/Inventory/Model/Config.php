<?php
declare(strict_types=1);

namespace MageBatch\Inventory\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    const XML_PATH_ENABLED = 'magebatch_inventory/general/enabled';
    const XML_PATH_ENABLE_BATCH_TRACKING = 'magebatch_inventory/general/enable_batch_tracking';
    const XML_PATH_ENABLE_FEFO = 'magebatch_inventory/general/enable_fefo';
    const XML_PATH_ENABLE_EXPIRY_PROCESSING = 'magebatch_inventory/general/enable_expiry_processing';
    const XML_PATH_ENABLE_EXPIRY_ALERTS = 'magebatch_inventory/general/enable_expiry_alerts';
    const XML_PATH_ENABLE_BATCH_RECALL = 'magebatch_inventory/general/enable_batch_recall';
    const XML_PATH_EXPIRY_WARNING_DAYS = 'magebatch_inventory/general/expiry_warning_days';
    const XML_PATH_NEAR_EXPIRY_THRESHOLD = 'magebatch_inventory/general/near_expiry_threshold';
    const XML_PATH_NOTIFICATION_EMAIL = 'magebatch_inventory/general/notification_email';

    public function __construct(
        private ScopeConfigInterface $scopeConfig
    ) {}

    public function isEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_ENABLED);
    }

    public function isBatchTrackingEnabled(): bool
    {
        return $this->isEnabled() && (bool)$this->scopeConfig->getValue(self::XML_PATH_ENABLE_BATCH_TRACKING);
    }

    public function isFefoEnabled(): bool
    {
        return $this->isBatchTrackingEnabled() && (bool)$this->scopeConfig->getValue(self::XML_PATH_ENABLE_FEFO);
    }

    public function isExpiryProcessingEnabled(): bool
    {
        return $this->isEnabled() && (bool)$this->scopeConfig->getValue(self::XML_PATH_ENABLE_EXPIRY_PROCESSING);
    }

    public function isExpiryAlertsEnabled(): bool
    {
        return $this->isExpiryProcessingEnabled() && (bool)$this->scopeConfig->getValue(self::XML_PATH_ENABLE_EXPIRY_ALERTS);
    }

    public function isBatchRecallEnabled(): bool
    {
        return $this->isEnabled() && (bool)$this->scopeConfig->getValue(self::XML_PATH_ENABLE_BATCH_RECALL);
    }

    public function getExpiryWarningDays(): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_EXPIRY_WARNING_DAYS);
    }

    public function getNearExpiryThreshold(): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_NEAR_EXPIRY_THRESHOLD);
    }

    public function getNotificationEmail(): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_NOTIFICATION_EMAIL);
    }
}
