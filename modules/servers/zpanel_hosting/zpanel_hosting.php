<?php
/**
 * zPanel Web Hosting & Reseller Module for WHMCS
 * Architecture inspired by Plesk WHMCS Plugin (https://github.com/plesk/whmcs-plugin)
 * 
 * zPanel Resmi Listener Portları:
 * - 8056 / TCP (HTTPS): Güvenli REST API Portu
 * - 8026 / TCP (HTTPS): Müşteri Paneli
 * - 8036 / TCP (HTTPS): Bayi / Root Paneli
 * - 8046 / TCP (HTTPS): Webmail
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

require_once __DIR__ . '/lib/ZPanel/Manager.php';
use WHMCS\Module\Server\ZPanelHosting\ZPanel\Manager;
use WHMCS\Database\Capsule;

function zpanel_hosting_MetaData() {
    return [
        'DisplayName' => 'zPanel Web Hosting & Reseller',
        'APIVersion' => '1.1',
        'RequiresServer' => true,
        'DefaultNonSSLPort' => '8056',
        'DefaultSSLPort' => '8056',
        'ServiceSingleSignOnLabel' => 'zPanel Web Hosting Giriş (:8026)',
        'AdminSingleSignOnLabel' => 'zPanel Root / Yönetici Girişi (:8036)',
        'MetricBilling' => true,
    ];
}

function zpanel_hosting_MetricProvider(array $params) {
    require_once __DIR__ . '/lib/ZPanel/MetricsProvider.php';
    return new \WHMCS\Module\Server\ZPanelHosting\ZPanel\MetricsProvider($params);
}

function zpanel_hosting_ConfigOptions() {
    return [
        'package_name' => [
            'FriendlyName' => 'zPanel Paket Adı',
            'Type' => 'text',
            'Size' => '25',
            'Default' => 'Standard-Hosting',
            'Description' => 'Sunucudaki hosting paketi adı veya açılır listeden seçiniz.',
            'Loader' => function(array $params) {
                try {
                    $mgr = new Manager($params);
                    return $mgr->getPackages();
                } catch (\Exception $e) {
                    return [
                        'Standard-Hosting' => 'Standard Hosting',
                        'Pro-Hosting'      => 'Pro Hosting',
                        'Reseller-Hosting' => 'Reseller Hosting',
                    ];
                }
            },
            'SimpleMode' => true,
        ],
        'is_reseller' => [
            'FriendlyName' => 'Bayi (Reseller) Paketi Mi?',
            'Type' => 'yesno',
            'Description' => 'İşaretlenirse bayi yetkili hesap açılır (Giriş portu 8036 olur).',
            'SimpleMode' => true,
        ],
        'quota_mb' => [
            'FriendlyName' => 'Disk Alanı (MB)',
            'Type' => 'text',
            'Size' => '10',
            'Default' => '10240',
            'SimpleMode' => true,
        ],
        'bandwidth_mb' => [
            'FriendlyName' => 'Aylık Trafik (MB)',
            'Type' => 'text',
            'Size' => '10',
            'Default' => '102400',
            'SimpleMode' => true,
        ],
    ];
}

function zpanel_hosting_TestConnection(array $params) {
    try {
        $mgr = new Manager($params);
        $res = $mgr->testConnection();
        return $res['success'] ? ['success' => true] : ['error' => $res['message'] ?: 'Bağlantı kurulamadı.'];
    } catch (\Exception $e) {
        return ['error' => $e->getMessage()];
    }
}

function zpanel_hosting_AdminLink(array $params) {
    $host = $params['serverip'] ?: ($params['serverhostname'] ?? '127.0.0.1');
    $port = 8036;
    $url = "https://{$host}:{$port}";
    return sprintf(
        '<a href="%s" target="_blank" class="btn btn-default btn-sm">' .
        '<i class="fas fa-external-link-alt"></i> zPanel Root/Bayi Paneline Giriş Yap (:8036)' .
        '</a>',
        htmlspecialchars($url, ENT_QUOTES, 'UTF-8')
    );
}

function zpanel_hosting_AdminServicesTabFields(array $params) {
    $domain = $params['domain'] ?? '-';
    $serverIp = $params['serverip'] ?? '-';
    $isReseller = ($params['configoption2'] === 'on' || $params['configoption2'] === '1');
    $panelPort = $isReseller ? 8036 : 8026;

    return [
        'zPanel Durumu' => '<span class="label label-success">Aktif &amp; Senkronize</span>',
        'Panel Portu'   => '<code>' . (int)$panelPort . ' / HTTPS</code>' . ($isReseller ? ' (Bayi Yetkili)' : ' (Bireysel)'),
        'Webmail Portu' => '<code>8046 / HTTPS</code>',
        'Doğrudan Giriş' => '<a href="https://' . htmlspecialchars($serverIp, ENT_QUOTES, 'UTF-8') . ':' . (int)$panelPort . '" target="_blank" class="btn btn-xs btn-info"><i class="fas fa-sign-in-alt"></i> Panele Git</a>',
    ];
}

function zpanel_hosting_ServiceSingleSignOn(array $params) {
    try {
        $mgr = new Manager($params);
        return [
            'success' => true,
            'redirectTo' => $mgr->getSingleSignOnUrl(),
        ];
    } catch (\Exception $e) {
        return [
            'success' => false,
            'errorMsg' => $e->getMessage(),
        ];
    }
}

function zpanel_hosting_AdminSingleSignOn(array $params) {
    try {
        $mgr = new Manager($params);
        return [
            'success' => true,
            'redirectTo' => $mgr->getAdminSingleSignOnUrl(),
        ];
    } catch (\Exception $e) {
        return [
            'success' => false,
            'errorMsg' => $e->getMessage(),
        ];
    }
}

function zpanel_hosting_UsageUpdate(array $params) {
    if (!class_exists('WHMCS\Database\Capsule')) {
        return 'success';
    }

    try {
        $serverId = (int)($params['serverid'] ?? 0);
        $services = Capsule::table('tblhosting')
            ->where('server', $serverId)
            ->whereIn('domainstatus', ['Active', 'Suspended'])
            ->get();

        foreach ($services as $service) {
            $serviceParams = array_merge($params, [
                'username'      => $service->username,
                'domain'        => $service->domain,
                'configoption3' => 10240,
                'configoption4' => 102400,
            ]);

            $mgr = new Manager($serviceParams);
            $usage = $mgr->getUsage();

            Capsule::table('tblhosting')
                ->where('id', $service->id)
                ->update([
                    'diskusage'  => $usage['diskusage'],
                    'disklimit'  => $usage['disklimit'],
                    'bwusage'    => $usage['bwusage'],
                    'bwlimit'    => $usage['bwlimit'],
                    'lastupdate' => date('Y-m-d H:i:s'),
                ]);
        }

        return 'success';
    } catch (\Exception $e) {
        return $e->getMessage();
    }
}

function zpanel_hosting_CreateAccount(array $params) {
    $mgr = new Manager($params);
    $res = $mgr->createAccount();
    return $res['success'] ? 'success' : ($res['message'] ?: 'Hesap açılamadı');
}

function zpanel_hosting_SuspendAccount(array $params) {
    $mgr = new Manager($params);
    $res = $mgr->suspendAccount();
    return $res['success'] ? 'success' : ($res['message'] ?: 'Askıya alınamadı');
}

function zpanel_hosting_UnsuspendAccount(array $params) {
    $mgr = new Manager($params);
    $res = $mgr->unsuspendAccount();
    return $res['success'] ? 'success' : ($res['message'] ?: 'Aktif edilemedi');
}

function zpanel_hosting_TerminateAccount(array $params) {
    $mgr = new Manager($params);
    $res = $mgr->terminateAccount();
    return $res['success'] ? 'success' : ($res['message'] ?: 'Silinemedi');
}

function zpanel_hosting_ChangePassword(array $params) {
    $mgr = new Manager($params);
    $res = $mgr->changePassword($params['password']);
    return $res['success'] ? 'success' : ($res['message'] ?: 'Şifre değiştirilemedi');
}

function zpanel_hosting_ChangePackage(array $params) {
    $mgr = new Manager($params);
    $res = $mgr->changePackage();
    return $res['success'] ? 'success' : ($res['message'] ?: 'Paket güncellenemedi');
}

function zpanel_hosting_ClientArea(array $params) {
    $mgr = new Manager($params);
    $tplVars = $mgr->getClientAreaParams();

    $templateFile = file_exists(__DIR__ . '/templates/overview.tpl') ? 'templates/overview.tpl' : 'clientarea.tpl';

    return [
        'tabOverviewReplacementTemplate' => $templateFile,
        'templateVariables' => $tplVars,
    ];
}
