<?php

namespace WHMCS\Module\Server\ZPanelHosting\ZPanel;

/**
 * zPanel Web Hosting İş Mantığı Yöneticisi (WHMCS)
 * zPanel Core API v1 Entegrasyonu
 * Port Mimarisi: 8056 (API), 8026 (Müşteri Paneli), 8036 (Root), 8046 (Webmail)
 */
class Manager
{
    private string $host;
    private string $apiKey;
    private int $port = 8056;
    private array $params;

    public function __construct(array $params)
    {
        $this->params = $params;
        $this->host = trim($params['serverip'] ?: $params['serverhostname'] ?: $params['domain'] ?? '', '/');
        $this->apiKey = trim($params['serverpassword'] ?: $params['serveraccesshash'] ?? '');
    }

    private function call(string $method, string $uri, array $data = []): array
    {
        $cleanUri = ltrim($uri, '/');
        if (!str_starts_with($cleanUri, 'v1/')) {
            $cleanUri = 'v1/' . $cleanUri;
        }

        $url = "https://{$this->host}:{$this->port}/{$cleanUri}";
        $ch = curl_init();

        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: WHMCS-zPanel-Hosting/1.0',
        ];

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 25);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $method = strtoupper($method);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'PATCH' || $method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        $res = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return ['success' => false, 'message' => "Bağlantı Hatası: {$err}", 'code' => 0];
        }

        $json = json_decode((string)$res, true);
        $isOk = ($code >= 200 && $code < 300);

        return [
            'success' => $isOk,
            'code' => $code,
            'data' => $json,
            'message' => $json['message'] ?? ($isOk ? 'Başarılı' : "HTTP Hata {$code}"),
        ];
    }

    private function findCustomerId(string $username): ?string
    {
        $res = $this->call('GET', "customers?q=" . urlencode($username));
        if ($res['success'] && !empty($res['data']['items'])) {
            foreach ($res['data']['items'] as $item) {
                if (strcasecmp($item['username'], $username) === 0) {
                    return $item['id'];
                }
            }
        }
        return null;
    }

    public function createAccount(): array
    {
        $username = $this->params['username'];
        $password = $this->params['password'];
        $domain   = $this->params['domain'];
        $email    = $this->params['clientsdetails']['email'] ?? "{$username}@{$domain}";
        $fullName = trim(($this->params['clientsdetails']['firstname'] ?? '') . ' ' . ($this->params['clientsdetails']['lastname'] ?? ''));

        // 1. Müşteri Hesabı Aç (POST /v1/customers)
        $custRes = $this->call('POST', 'customers', [
            'username'    => $username,
            'password'    => $password,
            'displayName' => $fullName ?: $username,
            'email'       => $email,
        ]);

        if (!$custRes['success']) {
            return $custRes;
        }

        $customerId = $custRes['data']['customer']['id'] ?? $this->findCustomerId($username);

        // 2. Alan Adını Ekle (POST /v1/domains)
        if (!empty($domain) && !empty($customerId)) {
            $domRes = $this->call('POST', 'domains', [
                'name'    => $domain,
                'ownerId' => $customerId,
                'kind'    => 'domain',
            ]);
            if (!$domRes['success']) {
                return $domRes;
            }
        }

        return ['success' => true, 'message' => 'Hosting hesabı ve alan adı başarıyla açıldı.'];
    }

    public function suspendAccount(): array
    {
        $customerId = $this->findCustomerId($this->params['username']);
        if (!$customerId) {
            return ['success' => true, 'message' => 'Müşteri hesabı bulunamadı.'];
        }
        return $this->call('PATCH', "customers/{$customerId}", ['status' => 'suspended']);
    }

    public function unsuspendAccount(): array
    {
        $customerId = $this->findCustomerId($this->params['username']);
        if (!$customerId) {
            return ['success' => true, 'message' => 'Müşteri hesabı bulunamadı.'];
        }
        return $this->call('PATCH', "customers/{$customerId}", ['status' => 'active']);
    }

    public function terminateAccount(): array
    {
        $customerId = $this->findCustomerId($this->params['username']);
        if (!$customerId) {
            return ['success' => true, 'message' => 'Müşteri hesabı zaten silinmiş.'];
        }
        return $this->call('DELETE', "customers/{$customerId}");
    }

    public function changePassword(string $newPass): array
    {
        $customerId = $this->findCustomerId($this->params['username']);
        if (!$customerId) {
            return ['success' => false, 'message' => 'Kullanıcı hesabı bulunamadı.'];
        }
        return $this->call('PATCH', "customers/{$customerId}", ['password' => $newPass]);
    }

    public function changePackage(): array
    {
        $customerId = $this->findCustomerId($this->params['username']);
        if (!$customerId) {
            return ['success' => false, 'message' => 'Kullanıcı hesabı bulunamadı.'];
        }
        $payload = [
            'package'      => $this->params['configoption1'] ?? 'Standard-Hosting',
            'quotaMb'      => (int)($this->params['configoption3'] ?? 10240),
            'bandwidthMb'  => (int)($this->params['configoption4'] ?? 102400),
        ];
        return $this->call('PATCH', "customers/{$customerId}", $payload);
    }

    public function getClientAreaParams(): array
    {
        $domain = $this->params['domain'] ?? '';
        $serverIp = $this->params['serverip'] ?? '';
        $panelHost = !empty($domain) ? $domain : $serverIp;
        $isReseller = ($this->params['configoption2'] === 'on' || $this->params['configoption2'] === '1');

        $panelPort = $isReseller ? 8036 : 8026;
        $webmailPort = 8046;

        $ssoUrl = !empty($panelHost) ? "https://{$panelHost}:{$panelPort}" : '#';
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
