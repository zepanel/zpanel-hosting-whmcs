# zPanel Web Hosting & Reseller API Dokümantasyonu

zPanel Web Hosting modülü, zPanel Core REST API v1 mimarisi üzerinden çalışır. Web sunucusu olarak yüksek performanslı **zMaxServer (OpenLiteSpeed & LSPHP / LSAPI)** kullanılmaktadır. Kesinlikle PHP-FPM kullanılmaz.

---

## 1. Resmi Port ve Dinleyici Mimarisi

| Port | Protokol | Servis / Görev | Açıklama |
|---|---|---|---|
| **8026** | TCP / HTTPS | Müşteri Kontrol Paneli | Son kullanıcıların web hosting yönetim arayüzü |
| **8036** | TCP / HTTPS | Bayi & Root Paneli | Bayi (Reseller) ve sistem yöneticisi yönetim paneli |
| **8046** | TCP / HTTPS | Webmail Servisi | Roundcube / Rainloop tabanlı webmail erişimi |
| **8056** | TCP / HTTPS | Core REST API | WHMCS, WISECP ve PNLCS otomasyon çağrıları |

---

## 2. Kimlik Doğrulama (Authentication)

Tüm API çağrıları HTTPS üzerinden `Authorization: Bearer <API_INTERNAL_TOKEN>` başlığı ile gerçekleştirilir.

```http
Authorization: Bearer <API_INTERNAL_TOKEN>
Content-Type: application/json
Accept: application/json
```

---

## 3. Core REST API Uç Noktaları

### 3.1. Sağlık Kontrolü / Bağlantı Testi
- **Uç Nokta:** `GET https://<server-ip>:8056/v1/health`
- **Başarılı Yanıt (200 OK):**
```json
{
  "status": "healthy",
  "version": "1.0.0",
  "webserver": "zMaxServer (OpenLiteSpeed / LSAPI)"
}
```

### 3.2. Yeni Müşteri / Hosting Hesabı Oluşturma
- **Uç Nokta:** `POST https://<server-ip>:8056/v1/customers`
- **İstek Gövdesi:**
```json
{
  "username": "clientuser",
  "password": "StrongPassword123!",
  "displayName": "Ahmet Yılmaz",
  "email": "ahmet@example.com"
}
```
- **Başarılı Yanıt (201 Created):**
```json
{
  "success": true,
  "customer": {
    "id": "cust_892837192",
    "username": "clientuser",
    "email": "ahmet@example.com",
    "status": "active"
  }
}
```

### 3.3. Hesaba Alan Adı Ekleme
- **Uç Nokta:** `POST https://<server-ip>:8056/v1/domains`
- **İstek Gövdesi:**
```json
{
  "name": "example.com",
  "ownerId": "cust_892837192",
  "kind": "domain"
}
```

### 3.4. Hesap Askıya Alma / Aktif Etme (Suspend / Unsuspend)
- **Uç Nokta:** `PATCH https://<server-ip>:8056/v1/customers/:id`
- **Askıya Alma İsteği:**
```json
{
  "status": "suspended"
}
```
- **Aktif Etme İsteği:**
```json
{
  "status": "active"
}
```

### 3.5. Şifre Değiştirme (Change Password)
- **Uç Nokta:** `PATCH https://<server-ip>:8056/v1/customers/:id`
- **İstek Gövdesi:**
```json
{
  "password": "NewStrongPassword456!"
}
```

### 3.6. Paket ve Kota Güncelleme (Change Package)
- **Uç Nokta:** `PATCH https://<server-ip>:8056/v1/customers/:id`
- **İstek Gövdesi:**
```json
{
  "package": "Gold-Hosting",
  "quotaMb": 20480,
  "bandwidthMb": 204800
}
```

### 3.7. Hesap Silme / Sonlandırma (Terminate)
- **Uç Nokta:** `DELETE https://<server-ip>:8056/v1/customers/:id`
- **Başarılı Yanıt (200 OK):**
```json
{
  "success": true,
  "message": "Müşteri ve ilişkili tüm alan adları/veritabanları silindi."
}
```

