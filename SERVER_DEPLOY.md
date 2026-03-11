# Serverda API ishlatish

## 0. Ma'lumotlar bazasi (PostgreSQL)

Loyiha **PostgreSQL** bilan ishlash uchun mos. Barcha migrationlar Laravel Schema builder orqali yozilgan (MySQL/PostgreSQL uchun bir xil).

`.env` da:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=daladan
DB_USERNAME=postgres
DB_PASSWORD=...
```

Bazani yaratib, keyin:
```bash
php artisan migrate:fresh --seed
```

## 1. Document root

**Document root** har doim loyihaning `public` papkasi bo'lishi kerak.

- **Apache:** `DocumentRoot /path/to/start-api/public`
- **Nginx:** `root /path/to/start-api/public;` (sizda `deploy/nginx-start-api.conf` da to'g'ri)

## 2. API manzillari

Laravel API marshrutlari prefiksi: **`/api`**

| So'rov            | URL                    |
|-------------------|------------------------|
| API tekshirish    | `GET https://domen.com/api/v1` |
| Login             | `POST https://domen.com/api/v1/login` |
| Boshqa endpointlar| `https://domen.com/api/v1/...` |

Brauzerda yoki `curl` bilan tekshirish:
```bash
curl https://sizning-domen.com/api/v1
```
Javob: `{"api":"v1","status":"ok","message":"API ishlayapti"}` bo'lishi kerak.

## 3. Serverda cache tozalash

Agar API 404 bersa, serverda quyilarni ishga tushiring:

```bash
cd /path/to/start-api
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

## 4. Apache (.htaccess)

Agar Apache ishlatilsa, `public/.htaccess` borligini va `AllowOverride All` yoqilganligini tekshiring (virtual host da).

## 5. Xulosa

- **Web** ishlayapti, **API** ishlamasa: odatda noto'g'ri URL (masalan `/v1/login` o'rniga `/api/v1/login` kerak) yoki **document root** `public` emas.
- To'g'ri base URL: `https://domen.com/api/v1`
