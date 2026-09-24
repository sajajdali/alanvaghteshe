# گزارش پیشرفت پیاده‌سازی سبد خرید هوشمند

این فایل مرجع ادامه کار بعد از بستن و بازکردن مجدد پروژه است.

## وضعیت خلاصه

```text
آخرین به‌روزرسانی: 2026-09-09
فاز ۱: پیاده‌سازی شده
فاز ۲: پیاده‌سازی شده
فاز ۳: کد Backend پیاده‌سازی و با OpenAI Fake تست شده؛ تست زنده نیازمند API key است
فاز ۴: Backend و ارسال واقعی Pusher تست شده؛ اتصال کلاینت و اصلاح پراکسی محیط مقصد باقی است
```

## Swagger مستقل — انجام‌شده

- [x] صفحه مستقل Swagger UI برای همین قابلیت
- [x] مستندسازی ورود دو مرحله‌ای `login` و `verify`
- [x] ثبت خودکار Sanctum token بعد از Verify موفق
- [x] امکان ورود دستی Bearer Token از Authorize
- [x] مستندسازی هر پنج API سبد خرید
- [x] schema کامل درخواست‌ها، وضعیت‌ها و نتیجه AI
- [x] تست syntax فایل YAML و تمام referenceهای داخلی
- [x] تست HTTP صفحه و specification با پاسخ ۲۰۰

```text
Swagger UI: /docs/shopping-list
OpenAPI YAML: /docs/shopping-list/openapi.yaml
```

## تصمیم‌های ثابت

- فقط یک جدول جدید: `diet_shopping_lists`
- منبع: `diet_request_details.detail.food_snapshot`
- فقط ردیف‌های `replaced_parent_id IS NULL`
- عدم استفاده از `recipes` و `recipe_basic_foods`
- daily برابر فردا
- weekly برابر هفت روز کامل از فردا
- timezone برابر `Asia/Tehran`
- Queue دیتابیسی و Pusher Private Channel

## فاز ۱ — انجام‌شده

- [x] ممیزی دیتابیس و API نمایش رژیم
- [x] ثبت پلن چهارمرحله‌ای
- [x] migration جدول `diet_shopping_lists`
- [x] اجرای migration در batch شماره ۲۱
- [x] مدل `DietShoppingList` با UUID خودکار و castها
- [x] relation به `DietRequest` و `User`
- [x] enum بازه daily/weekly
- [x] enum وضعیت pending/processing/ready/failed
- [x] `DietShoppingSnapshotExtractor`
- [x] استخراج فقط snapshot غذای نهایی بازه
- [x] کنترل مالکیت و فعال‌بودن رژیم
- [x] حفظ نام غذای ترکیبی، وعده، تاریخ و detail ID در source
- [x] `DietShoppingListInputHasher` با نسخه الگوریتم `v1`
- [x] تست واحد بازه‌ها و hash نوشته شده
- [x] lint تمام فایل‌ها
- [x] migration pretend و بررسی SQL با prefix واقعی `jsmno_`
- [x] smoke-test واقعی روزانه و هفتگی

نتیجه smoke-test:

```text
diet_request_id: 324
daily: 5 وعده
weekly: 35 وعده
weekly range: 2026-07-28 تا 2026-08-03
UUID: passed
model casts: passed
ownership guard: passed
```

## فاز ۲ — انجام‌شده

- [x] `GenerateDietShoppingListRequest`
- [x] متد پاسخ `accepted()` با HTTP 202
- [x] `DietShoppingListManager`
- [x] ساخت اتمیک رکورد بر اساس input hash
- [x] جلوگیری از race با unique constraint
- [x] جلوگیری از Job تکراری روی کلیک‌های متوالی
- [x] `GenerateDietShoppingListJob`
- [x] Queue اختصاصی `ai-shopping-list`
- [x] تنظیم backoff برای retryهای Job روی ۵، ۱۵ و ۳۰ ثانیه
- [x] تنظیم `DB_QUEUE_RETRY_AFTER=150` به‌صورت پیش‌فرض، بیشتر از timeout صدوبیست‌ثانیه‌ای Job
- [x] وضعیت‌های pending/processing/ready/failed
- [x] زمان شروع، پایان و error message
- [x] `ShoppingListController`
- [x] Route ساخت و مشاهده وضعیت/نتیجه
- [x] rate limit روی generate و retry
- [x] Routeها در `route:list` تأیید شدند
- [x] smoke-test با Queue جعلی

نتیجه smoke-test جلوگیری از تکرار:

```text
first_created: true
second_created: false
same_id: true
queued_jobs: 1
```

APIهای فاز ۲:

```http
POST /v1/diet/{dietRequest}/shopping-list/generate
GET  /v1/diet/{dietRequest}/shopping-list?period=daily
GET  /v1/diet/{dietRequest}/shopping-list/{shoppingList}
```

## فاز ۳ — Backend پیاده‌سازی شده

- [x] تنظیمات OpenAI در `config/services.php`
- [x] `OpenAiShoppingListService`
- [x] Responses API
- [x] Structured Output با JSON Schema
- [x] `strict=true`
- [x] `store=false`
- [x] Prompt نسخه `shopping-list-v1`
- [x] دسته‌های ثابت سبد
- [x] پاک‌سازی payload ورودی
- [x] عدم ارسال user ID، diet ID، detail ID، بیماری و اطلاعات پروفایل
- [x] timeout اتصال و کل درخواست
- [x] retry محدود HTTP
- [x] validation خروجی
- [x] جلوگیری از item key و group key تکراری
- [x] بررسی اینکه source واقعاً در رژیم ورودی وجود داشته باشد
- [x] whitelist خروجی و حذف فیلدهای اضافه AI
- [x] بازنویسی عنوان دسته توسط Backend
- [x] محاسبه total/progress توسط Backend
- [x] ذخیره model و token usage
- [x] عدم فراخوانی AI برای بازه بدون وعده
- [x] `DietShoppingListResultManager` با lock دیتابیس
- [x] API تیک‌زدن قلم داخل JSON
- [x] API retry برای وضعیت failed
- [x] تست کامل با HTTP Fake
- [ ] تنظیم API key واقعی در محیط مقصد
- [ ] تست زنده یک سبد daily و یک سبد weekly

نتیجه تست Fake:

```text
status: ready
engine: openai
store: false
strict: true
input_tokens: 120
output_tokens: 40
checked_items: 1
progress_percent: 100
user_id sent: false
diet_request_id sent: false
```

APIهای تکمیل‌شده:

```http
PATCH /v1/diet/{dietRequest}/shopping-list/{shoppingList}/check
POST  /v1/diet/{dietRequest}/shopping-list/{shoppingList}/retry
```

## فاز ۴ — Backend انجام‌شده، Client باقی است

- [x] فعال‌سازی `BroadcastServiceProvider`
- [x] ثبت `/broadcasting/auth`
- [x] middlewareهای `api` و `auth:sanctum` برای احراز Broadcast
- [x] کانال خصوصی `users.{userId}.shopping-list`
- [x] کنترل مالکیت کانال
- [x] event واحد `ShoppingListStatusChanged`
- [x] نام eventهای `shopping-list.processing/ready/failed`
- [x] استفاده از `ShouldBroadcastNow`
- [x] ارسال event بعد از ذخیره وضعیت دیتابیس
- [x] payload سبک شامل shopping list ID، diet ID، period، status و message
- [x] smoke-test نهایی نام کانال، نام event و payload سوکت
- [x] ثبت قرارداد کامل اتصال کلاینت، fallback polling و رفتار کلیک تکراری
- [x] ارسال موفق event آزمایشی به Pusher واقعی از محیط محلی
- [ ] تنظیم/حذف `PROXY_URL` نامعتبر در پردازش‌های PHP محیط مقصد
- [ ] اتصال کلاینت موبایل/وب به Private Channel
- [ ] واکنش کلاینت به processing/ready/failed
- [ ] دریافت نتیجه از API بعد از ready
- [ ] بازیابی status بعد از reconnect
- [ ] تست قطع و وصل اینترنت
- [ ] اجرای Worker دائمی Queue در deployment

## مشکل زیرساخت تست موجود پروژه

`artisan test` قبل از اجرای تست‌ها با خطای زیر متوقف می‌شود:

```text
Class "SebastianBergmann\Environment\Console" not found
```

فایل‌های `vendor/bin/pest` و `vendor/bin/phpunit` نیز در vendor فعلی وجود ندارند. به همین دلیل تست‌های نوشته‌شده هنوز با test runner رسمی اجرا نشده‌اند؛ در عوض lint، assertion مستقل، transaction smoke-test، Queue Fake و HTTP Fake موفق اجرا شده‌اند.

## فایل‌های ایجادشده

```text
docs/ai-shopping-list-design-fa.md
docs/ai-shopping-list-progress-fa.md
Modules/Api/Http/Controllers/ShoppingListController.php
Modules/Api/Http/Controllers/ShoppingListDocsController.php
Modules/Api/Http/Requests/GenerateDietShoppingListRequest.php
Modules/Api/Http/Requests/CheckDietShoppingListItemRequest.php
Modules/Diet/Database/Migrations/2026_09_09_120000_create_diet_shopping_lists_table.php
Modules/Diet/Entities/DietShoppingList.php
Modules/Diet/Enum/ShoppingListPeriodEnum.php
Modules/Diet/Enum/ShoppingListStatusEnum.php
Modules/Diet/Service/DietShoppingListInputHasher.php
Modules/Diet/Service/DietShoppingListManager.php
Modules/Diet/Service/DietShoppingListResultManager.php
Modules/Diet/Service/DietShoppingSnapshotExtractor.php
Modules/Diet/Service/OpenAiShoppingListService.php
Modules/Diet/app/Events/ShoppingListStatusChanged.php
Modules/Diet/app/Jobs/GenerateDietShoppingListJob.php
Modules/Diet/Tests/Unit/ShoppingListPeriodEnumTest.php
Modules/Diet/Tests/Unit/DietShoppingListInputHasherTest.php
docs/ai-shopping-list-client-contract-fa.md
Modules/Api/Docs/shopping-list-openapi.yaml
Modules/Api/Resources/views/shopping-list-docs.blade.php
```

## فایل‌های تغییرکرده

```text
app/Providers/BroadcastServiceProvider.php
config/app.php
config/queue.php
config/services.php
routes/channels.php
Modules/Api/Routes/api_v1_user.php
Modules/Api/Trait/ApiHandlerTrait.php
Modules/Diet/Entities/DietRequest.php
Modules/User/Entities/User.php
routes/web.php
```

## نقطه ادامه بعدی

1. تعیین/تنظیم `OPENAI_API_KEY` و `OPENAI_SHOPPING_LIST_MODEL` در محیط مقصد.
2. تست زنده OpenAI برای daily و weekly.
3. حذف/اصلاح `PROXY_URL` برای پردازش PHP و اجرای Worker دائمی Queue در محیط مقصد.
4. اتصال کلاینت به کانال خصوصی و تست reconnect.
5. اصلاح dependencyهای test runner و اجرای تست‌های رسمی.

نتیجه smoke-test نهایی WebSocket:

```text
channel: private-users.7.shopping-list
event: shopping-list.ready
payload status: ready
payload period: daily
```

## نکات محیط اجرا

- PHP پیش‌فرض XAMPP نسخه 8.2.4 است و vendor فعلی PHP 8.3 یا بالاتر می‌خواهد.
- دستورات با `/opt/homebrew/opt/php@8.3/bin/php` اجرا شوند.
- migration جدول سبد در batch شماره ۲۱ اجرا شده است.
- وضعیت بررسی‌شده محیط محلی: `QUEUE_CONNECTION=database`، درایور Broadcast برابر Pusher و جدول‌های `jobs`، `failed_jobs` و `diet_shopping_lists` موجود هستند.
- تنظیمات شناسه، کلید، secret و cluster مربوط به Pusher در محیط محلی موجود است؛ مقدار محرمانه در این گزارش ثبت نشده است.
- ارسال واقعی رویداد Pusher با غیرفعال‌کردن پراکسی موفق بود؛ پراکسی SOCKS فعلی اتصال Pusher را با `cURL error 97` رد می‌کند.
- `OPENAI_API_KEY` در محیط محلی هنوز تنظیم نشده و تنها مانع تست زنده AI است.
- Worker پیشنهادی: `php artisan queue:work database --queue=ai-shopping-list --tries=3 --timeout=120`
