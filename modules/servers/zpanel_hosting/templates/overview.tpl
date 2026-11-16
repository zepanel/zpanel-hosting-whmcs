{* zPanel Web Hosting & Reseller WHMCS Müşteri Alanı Şablonu *}
{* Dokümantasyon: https://console.zepanel.com/partner/docs *}
{* Port Mimarisi: 8026 (Müşteri Paneli), 8036 (Root/Bayi), 8046 (Webmail), 8056 (API) *}

<div class="zp-hosting-wrap" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #0f172a; max-width: 960px; margin: 0 auto;">
  
  <!-- Üst Başlık & Giriş Butonu Kartı -->
  <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 22px 26px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
    <div>
      <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
        <h2 style="font-size: 20px; font-weight: 700; margin: 0; color: #0f172a;">{$domain|default:$server_ip}</h2>
        <span style="background: #e0f2fe; color: #0369a1; font-size: 11.5px; font-weight: 700; padding: 3px 10px; border-radius: 9999px;">zPanel v1.0</span>
      </div>
      <div style="font-size: 13.5px; color: #64748b;">
        Sunucu IP: <strong style="font-family: monospace; color: #0f172a;">{$server_ip|default:'Atanmadı'}</strong>
        &bull;
        Paket: <strong style="color: #0f172a;">{$package_name}</strong>
      </div>
    </div>
    
    <div style="display: flex; gap: 10px; align-items: center;">
      <a href="{$sso_url}" target="_blank" style="background: #2563eb; color: #ffffff; text-decoration: none; font-weight: 600; font-size: 13px; padding: 10px 20px; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px; transition: background 0.15s ease;">
        zPanel Giriş (:{$panel_port|default:'8026'}) &rarr;
      </a>
    </div>
  </div>

  <!-- 1. Erişim & Hesap Bilgileri Kartı -->
  <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 22px 26px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
    <h3 style="font-size: 15px; font-weight: 700; margin: 0 0 16px 0; color: #1e293b;">Hosting Hesap &amp; Erişim Bilgileri</h3>
    
    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px 22px;">
      <div style="display: flex; border-bottom: 1px solid #e2e8f0; padding-bottom: 14px; margin-bottom: 14px; flex-wrap: wrap; gap: 16px;">
        <div style="flex: 1; min-width: 200px;">
          <div style="font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 4px;">Barındırılan Alan Adı</div>
          <div style="font-weight: 700; font-family: ui-monospace, monospace; font-size: 14.5px; color: #0f172a;">{$domain|default:'-'}</div>
        </div>
        <div style="flex: 1; min-width: 200px;">
          <div style="font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 4px;">Sunucu Statik IP</div>
          <div style="font-weight: 700; font-family: ui-monospace, monospace; font-size: 14.5px; color: #0f172a;">{$server_ip|default:'-'}</div>
        </div>
      </div>

      <div style="display: flex; border-bottom: 1px solid #e2e8f0; padding-bottom: 14px; margin-bottom: 14px; flex-wrap: wrap; gap: 16px;">
        <div style="flex: 1; min-width: 200px;">
          <div style="font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 4px;">Kullanıcı Adı (FTP &amp; Panel)</div>
          <div style="font-weight: 700; font-family: ui-monospace, monospace; font-size: 14.5px; color: #0f172a;">{$username|default:'-'}</div>
        </div>
        <div style="flex: 1; min-width: 200px;">
          <div style="font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 4px;">Hesap Şifresi</div>
          <div style="font-weight: 700; font-family: ui-monospace, monospace; font-size: 14.5px; color: #0f172a;">
            <span id="whmcs-hpwd">••••••••••••</span>
            {if $password}
              <button type="button" style="border: none; background: #e2e8f0; color: #334155; font-size: 11.5px; font-weight: 600; padding: 2px 8px; border-radius: 4px; cursor: pointer; margin-left: 8px;" onclick="var el=document.getElementById('whmcs-hpwd'); el.innerText=(el.innerText==='••••••••••••'?'{$password|escape:'javascript'}':'••••••••••••'); this.innerText=(this.innerText==='Göster'?'Gizle':'Göster');">Göster</button>
            {/if}
          </div>
        </div>
      </div>

      <div style="display: flex; flex-wrap: wrap; gap: 16px;">
        <div style="flex: 1; min-width: 200px;">
          <div style="font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 4px;">Panel Portu</div>
          <div style="font-size: 13.5px; font-family: ui-monospace, monospace;">
            <a href="https://{$panel_host|default:$server_ip}:{$panel_port|default:'8026'}" target="_blank" style="color: #2563eb; text-decoration: none; font-weight: 600;">
              https://{$panel_host|default:$server_ip}:{$panel_port|default:'8026'}
            </a>
          </div>
        </div>
        <div style="flex: 1; min-width: 200px;">
          <div style="font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 4px;">Webmail Portu</div>
          <div style="font-size: 13.5px; font-family: ui-monospace, monospace;">
            <a href="https://{$panel_host|default:$server_ip}:{$webmail_port|default:'8046'}" target="_blank" style="color: #2563eb; text-decoration: none; font-weight: 600;">
              https://{$panel_host|default:$server_ip}:{$webmail_port|default:'8046'}
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- 2. Hızlı Erişim & E-Posta Yönetimi -->
  <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 22px 26px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
    <h3 style="font-size: 15px; font-weight: 700; margin: 0 0 14px 0; color: #1e293b;">Hızlı Servis Erişimi</h3>
    
    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
      <a href="{$sso_url}" target="_blank" style="background: #f8fafc; border: 1px solid #cbd5e1; color: #0f172a; padding: 10px 18px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
        <span>&#x1F4C1;</span> Dosya Yöneticisi
      </a>

      <a href="https://{$panel_host|default:$server_ip}:{$webmail_port|default:'8046'}" target="_blank" style="background: #f8fafc; border: 1px solid #cbd5e1; color: #0f172a; padding: 10px 18px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
        <span>&#x2709;</span> Webmail Gelen Kutusu (:8046)
      </a>

      <a href="{$sso_url}" target="_blank" style="background: #f8fafc; border: 1px solid #cbd5e1; color: #0f172a; padding: 10px 18px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
        <span>&#x1F5C3;</span> Veritabanları (MariaDB)
      </a>
    </div>
  </div>

  {if $is_reseller}
  <!-- 3. Bayi (Reseller) Yönetim Bölümü -->
  <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 22px 26px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 10px;">
      <h3 style="font-size: 15px; font-weight: 700; margin: 0; color: #1e293b;">Bayi (Reseller) Yönetim Paneli</h3>
      <a href="https://{$panel_host|default:$server_ip}:8036" target="_blank" style="background: #0f172a; color: #ffffff; padding: 8px 18px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 600;">
        Bayi Paneline Git (:8036) &rarr;
      </a>
    </div>
    <p style="font-size: 13.5px; color: #64748b; margin: 0; line-height: 1.5;">
      Alt müşterileriniz için yeni web barındırma hesapları tanımlayabilir, disk/trafik kotalarını belirleyebilir ve hesapları zPanel Bayi Portu (8036) üzerinden doğrudan yönetebilirsiniz.
    </p>
  </div>
  {/if}

</div>
