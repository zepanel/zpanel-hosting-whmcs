{* zPanel Web Hosting & Reseller WHMCS Müşteri Alanı Şablonu *}
{* Dokümantasyon: https://console.zepanel.com/partner/docs *}
{* Port Mimarisi: 8026 (Müşteri Paneli), 8036 (Root/Bayi), 8046 (Webmail), 8056 (API) *}

<link rel="stylesheet" href="modules/servers/zpanel_hosting/assets/css/client.css">

<div class="zp-hosting-wrap">
  
  <!-- Üst Başlık & Giriş Butonu Kartı -->
  <div class="zp-header-card">
    <div>
      <div class="zp-header-title-wrap">
        <h2 class="zp-header-domain">{$domain|default:$server_ip}</h2>
        <span class="zp-badge-ver">zPanel v1.0</span>
      </div>
      <div class="zp-header-meta">
        Sunucu IP: <strong class="zp-code-val">{$server_ip|default:'Atanmadı'}</strong>
        &bull;
        Paket: <strong class="zp-strong-val">{$package_name}</strong>
      </div>
    </div>
    
    <div>
      <a href="{$sso_url}" target="_blank" class="zp-btn-primary">
        zPanel Giriş (:{$panel_port|default:'8026'}) &rarr;
      </a>
    </div>
  </div>

  <!-- 1. Erişim & Hesap Bilgileri Kartı -->
  <div class="zp-card">
    <h3 class="zp-card-title">Hosting Hesap &amp; Erişim Bilgileri</h3>
    
    <div class="zp-info-box">
      <div class="zp-info-row">
        <div class="zp-info-col">
          <div class="zp-info-label">Barındırılan Alan Adı</div>
          <div class="zp-code-val">{$domain|default:'-'}</div>
        </div>
        <div class="zp-info-col">
          <div class="zp-info-label">Sunucu Statik IP</div>
          <div class="zp-code-val">{$server_ip|default:'-'}</div>
        </div>
      </div>

      <div class="zp-info-row">
        <div class="zp-info-col">
          <div class="zp-info-label">Kullanıcı Adı (FTP &amp; Panel)</div>
          <div class="zp-code-val">{$username|default:'-'}</div>
        </div>
        <div class="zp-info-col">
          <div class="zp-info-label">Hesap Şifresi</div>
          <div class="zp-code-val">
            <span id="whmcs-hpwd">••••••••••••</span>
            {if $password}
              <button type="button" class="zp-btn-toggle-pwd" onclick="var el=document.getElementById('whmcs-hpwd'); el.innerText=(el.innerText==='••••••••••••'?'{$password|escape:'javascript'}':'••••••••••••'); this.innerText=(this.innerText==='Göster'?'Gizle':'Göster');">Göster</button>
            {/if}
          </div>
        </div>
      </div>

      <div class="zp-info-row">
        <div class="zp-info-col">
          <div class="zp-info-label">Panel Portu</div>
          <div>
            <a href="https://{$panel_host|default:$server_ip}:{$panel_port|default:'8026'}" target="_blank" class="zp-link-port">
              https://{$panel_host|default:$server_ip}:{$panel_port|default:'8026'}
            </a>
          </div>
        </div>
        <div class="zp-info-col">
          <div class="zp-info-label">Webmail Portu</div>
          <div>
            <a href="https://{$panel_host|default:$server_ip}:{$webmail_port|default:'8046'}" target="_blank" class="zp-link-port">
              https://{$panel_host|default:$server_ip}:{$webmail_port|default:'8046'}
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- 2. Hızlı Erişim & E-Posta Yönetimi -->
  <div class="zp-card">
    <h3 class="zp-card-title">Hızlı Servis Erişimi</h3>
    
    <div class="zp-quick-links">
      <a href="{$sso_url}" target="_blank" class="zp-btn-outline">
        <span>&#x1F4C1;</span> Dosya Yöneticisi
      </a>

      <a href="https://{$panel_host|default:$server_ip}:{$webmail_port|default:'8046'}" target="_blank" class="zp-btn-outline">
        <span>&#x2709;</span> Webmail Gelen Kutusu (:8046)
      </a>

      <a href="{$sso_url}" target="_blank" class="zp-btn-outline">
        <span>&#x1F5C3;</span> Veritabanları (MariaDB)
      </a>
    </div>
  </div>

  {if $is_reseller}
  <!-- 3. Bayi (Reseller) Yönetim Bölümü -->
  <div class="zp-card">
    <div class="zp-reseller-header">
      <h3 class="zp-card-title">Bayi (Reseller) Yönetim Paneli</h3>
      <a href="https://{$panel_host|default:$server_ip}:8036" target="_blank" class="zp-btn-dark">
        Bayi Paneline Git (:8036) &rarr;
      </a>
    </div>
    <p class="zp-desc-text">
      Alt müşterileriniz için yeni web barındırma hesapları tanımlayabilir, disk/trafik kotalarını belirleyebilir ve hesapları zPanel Bayi Portu (8036) üzerinden doğrudan yönetebilirsiniz.
    </p>
  </div>
  {/if}

</div>
