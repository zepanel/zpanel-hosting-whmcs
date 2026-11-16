<?php
/**
 * zPanel Web Hosting Module Hooks for WHMCS
 * Reference & Standards: https://developers.whmcs.com/hooks/
 */

use WHMCS\View\Menu\Item as MenuItem;

add_hook('ClientAreaPrimarySidebar', 10, function (MenuItem $primarySidebar) {
    $service = Menu::context('service');
    if (!$service || $service->product->servertype !== 'zpanel_hosting') {
        return;
    }

    if ($service->domainstatus !== 'Active') {
        return;
    }

    $panelHost = $service->domain ?: $service->serverip;
    $panelPort = 8026;
    $webmailPort = 8046;

    $sidebar = $primarySidebar->addChild('zPanelHostingActions', [
        'label' => 'zPanel Hızlı İşlemler',
        'order' => 15,
        'icon'  => 'fas fa-server',
    ]);

    $sidebar->addChild('zpanel_login', [
        'label' => 'zPanel Giriş (Port 8026)',
        'uri'   => "https://{$panelHost}:{$panelPort}",
        'order' => 10,
        'icon'  => 'fas fa-sign-in-alt',
        'attributes' => ['target' => '_blank'],
    ]);

    $sidebar->addChild('zpanel_webmail', [
        'label' => 'Webmail Giriş (Port 8046)',
        'uri'   => "https://{$panelHost}:{$webmailPort}",
        'order' => 20,
        'icon'  => 'fas fa-envelope',
        'attributes' => ['target' => '_blank'],
    ]);

    $sidebar->addChild('zpanel_filemanager', [
        'label' => 'Dosya Yöneticisi',
        'uri'   => "https://{$panelHost}:{$panelPort}/files",
        'order' => 30,
        'icon'  => 'fas fa-folder-open',
        'attributes' => ['target' => '_blank'],
    ]);
});

