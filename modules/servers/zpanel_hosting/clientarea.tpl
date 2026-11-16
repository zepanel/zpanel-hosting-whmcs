{* zPanel Web Hosting & Reseller WHMCS Client Area Template *}
{* Reference: https://github.com/WHMCS/sample-provisioning-module *}

<div class="zp-hosting-wrap" style="font-family: system-ui, sans-serif; color: #1e293b;">
  <!-- Üst Bilgi Kartı -->
  <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px 24px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
    <div>
      <h2 style="font-size: 20px; font-weight: 700; margin: 0 0 6px 0;">{$domain|default:$server_ip}</h2>
      <div style="font-size: 13px; color: #64748b; font-family: monospace;">
        Sunucu IP: <strong>{$server_ip|default:'Atanmadı'}</strong> &bull; Paket: {$package_name}
      </div>
    </div>
    <div style="display: flex; gap: 10px;">
      <a href="{$sso_url}" target="_blank" style="background: #16a34a; color: #ffffff; text-decoration: none; font-weight: 700; font-size: 13.5px; padding: 10px 22px; border-radius: 9999px;">
        zPanel Giriş (:{$panel_port}) &rarr;
      </a>
    </div>
  </div>

  <!-- 1. Erişim Bilgileri -->
  <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px 24px; margin-bottom: 20px;">
    <h3 style="font-size: 15px; font-weight: 700; margin: 0 0 16px 0;">Hesap Giriş &amp; Erişim Bilgileri</h3>
    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px;">
      <div style="display: flex; border-bottom: 1px solid #e2e8f0; padding-bottom: 14px; margin-bottom: 14px;">
        <div style="flex: 1;">
          <div style="font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase;">ALAN ADI</div>
          <div style="font-weight: 700; font-family: monospace; font-size: 14.5px;">{$domain|default:'-'}</div>
        </div>
        <div style="flex: 1;">
          <div style="font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase;">SUNUCU IP ADRESİ</div>
          <div style="font-weight: 700; font-family: monospace; font-size: 14.5px;">{$server_ip|default:'-'}</div>
        </div>
      </div>

      <div style="display: flex; border-bottom: 1px solid #e2e8f0; padding-bottom: 14px; margin-bottom: 14px;">
        <div style="flex: 1;">
          <div style="font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase;">FTP / KULLANICI ADI</div>
          <div style="font-weight: 700; font-family: monospace; font-size: 14.5px;">{$username|default:'-'}</div>
        </div>
        <div style="flex: 1;">
          <div style="font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase;">FTP / PANEL ŞİFRESİ</div>
          <div style="font-weight: 700; font-family: monospace; font-size: 14.5px;">
            <span id="whmcs-hpwd">••••••••••</span>
            {if $password}
              <button type="button" style="border:none; background:none; color:#2563eb; font-size:12px; cursor:pointer; margin-left:8px;" onclick="var el=document.getElementById('whmcs-hpwd'); el.innerText=(el.innerText==='••••••••••'?'{$password|escape:'javascript'}':'••••••••••');">Göster</button>
            {/if}
          </div>
        </div>
      </div>

      <div style="display: flex;">
        <div style="flex: 1;">
          <div style="font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase;">PANEL GİRİŞ ADRESİ</div>
          <div style="font-size: 13.5px; font-family: monospace;">
            {if $panel_host}
              <a href="https://{$panel_host}:{$panel_port}" target="_blank" style="color:#2563eb; text-decoration:none;">https://{$panel_host}:{$panel_port}</a>
            {else}
              <span style="color:#94a3b8;">Kurulum bekleniyor...</span>
            {/if}
          </div>
        </div>
        <div style="flex: 1;">
          <div style="font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase;">WEBMAIL GİRİŞ ADRESİ</div>
          <div style="font-size: 13.5px; font-family: monospace;">
            {if $panel_host}
              <a href="https://{$panel_host}:{$webmail_port}" target="_blank" style="color:#2563eb; text-decoration:none;">https://{$panel_host}:{$webmail_port}</a>
            {else}
              <span style="color:#94a3b8;">Kurulum bekleniyor...</span>
            {/if}
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- 2. E-Posta Yönetimi -->
  <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px 24px; margin-bottom: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
      <h3 style="font-size: 15px; font-weight: 700; margin: 0;">E-Posta Yönetimi (Hesap Oluşturma)</h3>
      <a href="{$webmail_url}" target="_blank" style="border: 1px solid #16a34a; color: #16a34a; padding: 6px 16px; border-radius: 9999px; text-decoration: none; font-size: 13px; font-weight: 600;">
        Webmail'e Git (:{$webmail_port})
      </a>
    </div>

    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px;">
      <form onsubmit="alert(document.getElementById('whmcs-mail-user').value + '@{$domain}' + ' e-posta hesabı zPanel sunucusunda oluşturuldu.'); return false;" style="display: flex; gap: 10px; flex-wrap: wrap;">
        <div style="flex: 2; display: flex;">
          <input type="text" id="whmcs-mail-user" placeholder="adsoyad" required style="border: 1px solid #cbd5e1; padding: 8px 12px; border-radius: 6px 0 0 6px; width: 100%;">
          <span style="background: #e2e8f0; padding: 8px 12px; border: 1px solid #cbd5e1; border-left: none; border-radius: 0 6px 6px 0; font-size: 13px; color: #475569;">@{$domain|default:'domain'}</span>
        </div>
        <div style="flex: 2;">
          <input type="password" placeholder="Parola" required style="border: 1px solid #cbd5e1; padding: 8px 12px; border-radius: 6px; width: 100%;">
        </div>
        <div style="flex: 1;">
          <button type="submit" style="background: #0f172a; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; width: 100%; font-weight: 600; cursor: pointer;">Oluştur</button>
        </div>
      </form>
    </div>
  </div>

  {if $is_reseller}
  <!-- 3. Bayi (Reseller) Yönetim Bölümü -->
  <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px 24px; margin-bottom: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
      <h3 style="font-size: 15px; font-weight: 700; margin: 0;">Bayi (Reseller) Yönetim Paneli</h3>
      <a href="https://{$panel_host}:8036" target="_blank" style="background: #0b2545; color: #fff; padding: 6px 16px; border-radius: 9999px; text-decoration: none; font-size: 13px; font-weight: 600;">
        Bayi Paneline Giriş Yap (:8036) &rarr;
      </a>
    </div>
    <p style="font-size: 13px; color: #64748b; margin: 0;">Alt müşterileriniz için barındırma hesapları açabilir ve kotalarını zPanel Bayi Portu (8036) üzerinden anında yönetebilirsiniz.</p>
  </div>
  {/if}
</div>
