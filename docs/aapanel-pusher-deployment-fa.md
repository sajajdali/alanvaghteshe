# استقرار Pusher و صف سبد خرید در aaPanel

پروژه از سرویس سازگار با Pusher روی `broadcast.shemiranweb.com` استفاده می‌کند. App این پروژه باید پیش از استقرار روی میزبان وب‌سوکت با شناسه، کلید و Secret مستقل ثبت شده باشد.

## متغیرهای محیطی

مقادیر `.env.broadcasting.example` را به `.env` سرور اضافه کنید و مقدار واقعی `PUSHER_APP_SECRET` را فقط روی سرور قرار دهید. فایل `.env` نباید commit شود.

صف نیز باید فعال باشد:

```dotenv
QUEUE_CONNECTION=database
DB_QUEUE_RETRY_AFTER=300
```

## دستورات هر deploy

دستورات را در ریشه پروژه و با PHP همان سایت اجرا کنید:

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
```

متغیرهای `VITE_` هنگام build وارد خروجی فرانت می‌شوند؛ بنابراین `npm run build` باید بعد از تنظیم `.env` اجرا شود.

## Queue Worker در Supervisor افزونه aaPanel

یک Daemon با مشخصات زیر ایجاد کنید و مسیر PHP و پروژه را با مسیر واقعی سرور جایگزین کنید:

```text
Name: alanvaghteshe-queue
Run directory: /www/wwwroot/PROJECT_PATH
Command: /www/server/php/83/bin/php artisan queue:work database --queue=ai-shopping-list,default --sleep=2 --tries=3 --timeout=240 --max-time=3600
Processes: 1
Autostart: yes
Autorestart: yes
```

بعد از هر deploy، `php artisan queue:restart` باعث می‌شود worker پس از پایان Job جاری با کد جدید بالا بیاید.

## قرارداد اپلیکیشن موبایل

کانال خصوصی:

```text
private-users.{userId}.shopping-list
```

احراز هویت کانال:

```text
POST https://API_DOMAIN/broadcasting/auth
Authorization: Bearer SANCTUM_TOKEN
Accept: application/json
```

نام رویدادها:

```text
shopping-list.processing
shopping-list.ready
shopping-list.failed
```

Payload نمونه:

```json
{
  "shopping_list_id": 15,
  "diet_request_id": 120,
  "period": "daily",
  "status": "ready",
  "message": "سبد خرید شما آماده شد."
}
```

پس از رویداد `shopping-list.ready`، اپ جزئیات را دریافت کند:

```text
GET /diet/{dietRequest}/shopping-list/{shoppingListId}
```

Polling سه‌ثانیه‌ای به‌عنوان fallback حفظ شود تا قطع موقت WebSocket تجربه کاربر را مختل نکند.

## تست بعد از استقرار

```bash
php artisan route:list --path=broadcasting
php artisan queue:monitor ai-shopping-list:100
```

بررسی نهایی:

1. `/broadcasting/auth` با Bearer Token معتبر پاسخ موفق بدهد.
2. اتصال WSS به `broadcast.shemiranweb.com` برقرار شود.
3. کاربر نتواند عضو کانال شناسه کاربر دیگری شود.
4. رویداد `shopping-list.ready` پس از پایان Job دریافت شود.
5. پیام‌های این App در پروژه دیگری دریافت نشوند.
