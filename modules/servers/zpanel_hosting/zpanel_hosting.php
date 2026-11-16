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

function zpanel_hosting_MetaData() {
    return [
        'DisplayName' => 'zPanel Web Hosting & Reseller',
        'APIVersion' => '1.1',
        'RequiresServer' => true,
        'DefaultNonSSLPort' => '8056',
        'DefaultSSLPort' => '8056',
        'ServiceSingleSignOnLabel' => 'zPanel Web Hosting Giriş (:8026)',
    ];
}

function zpanel_hosting_ConfigOptions() {
    return [
        'package_name' => [
            'FriendlyName' => 'zPanel Paket Adı',
            'Type' => 'text',
            'Size' => '25',
            'Default' => 'Standard-Hosting',
        ],
        'is_reseller' => [
            'FriendlyName' => 'Bayi (Reseller) Paketi Mi?',
            'Type' => 'yesno',
            'Description' => 'İşaretlenirse bayi yetkili hesap açılır (Giriş portu 8036 olur).',
        ],
        'quota_mb' => [
            'FriendlyName' => 'Disk Alanı (MB)',
            'Type' => 'text',
            'Size' => '10',
            'Default' => '10240',
        ],
        'bandwidth_mb' => [
            'FriendlyName' => 'Aylık Trafik (MB)',
            'Type' => 'text',
            'Size' => '10',
            'Default' => '102400',
        ],
    ];
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

function zpanel_hosting_ClientArea(array $params) {
    $mgr = new Manager($params);
    $tplVars = $mgr->getClientAreaParams();

    return [
        'tabOverviewReplacementTemplate' => 'clientarea.tpl',
        'templateVariables' => $tplVars,
    ];
}
