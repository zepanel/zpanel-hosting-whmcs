<?php

namespace WHMCS\Module\Server\ZPanelHosting\ZPanel;

/**
 * zPanel Hosting & Reseller Business Logic Manager for WHMCS
 * Inspired by Plesk WHMCS Plugin Architecture
 */
class Manager
{
    private string $host;
    private string $apiKey;
    private array $params;

    public function __construct(array $params)
    {
        $this->params = $params;
        $this->host = trim($params['serverip'] ?: $params['serverhostname'], '/');
        $this->apiKey = trim($params['serverpassword'] ?: $params['serveraccesshash']);
    }

    private function call(string $method, string $uri, array $data = []): array
    {
        $url = "https://{$this->host}:8056/api/v1/" . ltrim($uri, '/');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json',
            'Accept: application/json',
        ]);

        $method = strtoupper($method);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        $res = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $json = json_decode($res, true);
        return [
            'success' => ($code >= 200 && $code < 300),
            'data' => $json['data'] ?? $json,
            'message' => $json['message'] ?? '',
        ];
    }

    public function createAccount(): array
    {
        return $this->call('POST', 'hosting/accounts', [
            'domain'      => $this->params['domain'],
            'username'    => $this->params['username'],
            'password'    => $this->params['password'],
            'package'     => $this->params['configoption1'] ?? 'default',
            'is_reseller' => ($this->params['configoption2'] === 'on' || $this->params['configoption2'] === '1'),
            'quota_mb'    => (int)($this->params['configoption3'] ?? 10240),
            'bandwidth_mb'=> (int)($this->params['configoption4'] ?? 102400),
        ]);
    }

    public function suspendAccount(): array
    {
        return $this->call('POST', "hosting/accounts/{$this->params['username']}/suspend");
    }

    public function unsuspendAccount(): array
    {
        return $this->call('POST', "hosting/accounts/{$this->params['username']}/unsuspend");
    }

    public function terminateAccount(): array
    {
        return $this->call('DELETE', "hosting/accounts/{$this->params['username']}");
    }

    public function changePassword(string $newPass): array
    {
        return $this->call('POST', "hosting/accounts/{$this->params['username']}/change-password", ['password' => $newPass]);
    }

    public function getClientAreaParams(): array
    {
        $domain = $this->params['domain'] ?? '';
        $serverIp = $this->params['serverip'] ?? '';
        $panelHost = !empty($domain) ? $domain : $serverIp;
        $isReseller = ($this->params['configoption2'] === 'on' || $this->params['configoption2'] === '1');

        $panelPort = $isReseller ? 8036 : 8026;
        $webmailPort = 8046;

        $tokenRes = $this->call('POST', 'auth/sso-token', [
            'username' => $this->params['username'],
            'is_reseller' => $isReseller,
        ]);
        $token = $tokenRes['data']['token'] ?? bin2hex(random_bytes(16));

        $ssoUrl = !empty($panelHost) ? "https://{$panelHost}:{$panelPort}/auth/sso?token={$token}" : '#';
        $webmailUrl = !empty($panelHost) ? "https://{$panelHost}:{$webmailPort}" : '#';

        return [
            'domain'        => $domain,
            'username'      => $this->params['username'],
            'password'      => $this->params['password'],
            'server_ip'     => $serverIp,
            'panel_host'    => $panelHost,
            'panel_port'    => $panelPort,
            'webmail_port'  => $webmailPort,
            'is_reseller'   => $isReseller,
            'sso_url'       => $ssoUrl,
            'webmail_url'   => $webmailUrl,
            'package_name'  => $this->params['package'] ?? ($isReseller ? 'Bayi Hosting' : 'Limitsiz Web Hosting'),
            'status'        => $this->params['status'] ?? 'Active',
            'service_id'    => $this->params['serviceid'],
        ];
    }
}

