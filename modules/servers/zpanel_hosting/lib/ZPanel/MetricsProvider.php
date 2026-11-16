<?php

namespace WHMCS\Module\Server\ZPanelHosting\ZPanel;

/**
 * zPanel Web Hosting Metric Billing Provider for WHMCS
 * Supports: Disk Space, Bandwidth, Email Accounts, MySQL Databases, Subdomains, Addon Domains
 * WHMCS Version: 7.9+ / 8.x / 9.x
 */
class MetricsProvider
{
    private array $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Define the metrics available for products under this module.
     */
    public function metrics(): array
    {
        $metrics = [];

        if (class_exists('WHMCS\UsageBilling\Metrics\Metric')) {
            $unitGb = class_exists('WHMCS\UsageBilling\Metrics\Units\Gigabytes') ? new \WHMCS\UsageBilling\Metrics\Units\Gigabytes() : null;
            $unitAccounts = class_exists('WHMCS\UsageBilling\Metrics\Units\Accounts') ? new \WHMCS\UsageBilling\Metrics\Units\Accounts() : null;
            $statType = defined('WHMCS\UsageBilling\Contracts\Metrics\MetricInterface::TYPE_STAT') ? \WHMCS\UsageBilling\Contracts\Metrics\MetricInterface::TYPE_STAT : 'stat';

            $disk = new \WHMCS\UsageBilling\Metrics\Metric('disk_space', 'Disk Space');
            $disk->setDescription('Web Alanı Depolama Tüketimi (GB)');
            if ($unitGb) $disk->setUnit($unitGb);
            $disk->setType($statType);
            $metrics[] = $disk;

            $bw = new \WHMCS\UsageBilling\Metrics\Metric('bandwidth', 'Bandwidth');
            $bw->setDescription('Aylık Veri Trafiği Tüketimi (GB)');
            if ($unitGb) $bw->setUnit($unitGb);
            $bw->setType($statType);
            $metrics[] = $bw;

            $email = new \WHMCS\UsageBilling\Metrics\Metric('email_accounts', 'Email Accounts');
            $email->setDescription('Açılan E-Posta Hesap Sayısı');
            if ($unitAccounts) $email->setUnit($unitAccounts);
            $email->setType($statType);
            $metrics[] = $email;

            $mysql = new \WHMCS\UsageBilling\Metrics\Metric('mysql_databases', 'MySQL Databases');
            $mysql->setDescription('Oluşturulan MariaDB / MySQL Veritabanı Sayısı');
            if ($unitAccounts) $mysql->setUnit($unitAccounts);
            $mysql->setType($statType);
            $metrics[] = $mysql;

            $subdomains = new \WHMCS\UsageBilling\Metrics\Metric('sub_domains', 'Sub Domains');
            $subdomains->setDescription('Oluşturulan Alt Alan Adı (Subdomain) Sayısı');
            if ($unitAccounts) $subdomains->setUnit($unitAccounts);
            $subdomains->setType($statType);
            $metrics[] = $subdomains;
        }

        return $metrics;
    }

    public function usage(): array
    {
        return [];
    }

    public function tenantUsage($tenant): array
    {
        return [];
    }
}

