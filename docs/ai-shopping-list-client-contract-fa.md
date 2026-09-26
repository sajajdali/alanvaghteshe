# قرارداد اتصال کلاینت سبد خرید هوشمند

این سند قرارداد نهایی بین اپ و Backend است. همه درخواست‌ها باید با Bearer Token فعلی کاربر ارسال شوند.

## رفتار دکمه

برای دکمه‌های «سبد خرید فردا» و «سبد خرید هفتگی» فقط مقدار `period` فرق می‌کند:

```text
فردا: daily
هفت روز آینده از فردا: weekly
```

با اولین کلیک:

```http
POST /v1/diet/{dietRequest}/shopping-list/generate
Content-Type: application/json

{"period":"daily"}
```

- پاسخ `202`: درخواست در صف است یا در حال محاسبه است؛ پیام پاسخ نمایش داده شود.
- پاسخ `200` با status برابر `ready`: نتیجه قبلاً آماده بوده و باید همان لحظه نمایش داده شود.
- کلیک دوباره همین درخواست را تکرار نمی‌کند؛ Backend همان رکورد جاری را برمی‌گرداند.
- تا زمانی که status برابر `pending` یا `processing` است، متن دکمه «در حال محاسبه» باشد.

نمونه پاسخ وضعیت:

```json
{
  "status": "processing",
  "shopping_list_id": 42,
  "uuid": "...",
  "period": "daily",
  "start_date": "۱۴۰۵-۰۶-۱۹",
  "end_date": "۱۴۰۵-۰۶-۱۹",
  "message": "سبد خرید در حال محاسبه است.",
  "result": null
}
```

ساختار پاسخ تغییری نکرده است؛ فقط مقدار فیلدهای موجود `start_date` و `end_date` برای نمایش در اپلیکیشن به‌صورت شمسی، با ارقام فارسی و ترتیب سال-ماه-روز ارسال می‌شود.

## اتصال سوکت

کلاینت بعد از ورود کاربر به کانال خصوصی زیر subscribe می‌شود:

```text
private-users.{authenticatedUserId}.shopping-list
```

در Laravel Echo عبارت `private-` به‌صورت خودکار اضافه می‌شود؛ بنابراین نام کانال در کد Echo باید این باشد:

```text
users.{authenticatedUserId}.shopping-list
```

Endpoint احراز کانال:

```http
POST /broadcasting/auth
Authorization: Bearer {token}
```

رویدادهای قابل دریافت:

```text
.shopping-list.processing
.shopping-list.ready
.shopping-list.failed
```

نمونه payload:

```json
{
  "shopping_list_id": 42,
  "diet_request_id": 324,
  "period": "daily",
  "status": "ready",
  "message": "سبد خرید شما آماده شد."
}
```

کلاینت فقط رویدادی را اعمال کند که `diet_request_id` و `period` آن با صفحه فعلی برابر است. بعد از رویداد `ready`، نتیجه کامل از API زیر دریافت شود؛ نتیجه کامل داخل event ارسال نمی‌شود:

```http
GET /v1/diet/{dietRequest}/shopping-list/{shoppingListId}
```

## بازیابی وضعیت و fallback

در بازشدن صفحه، برگشت اپ از background یا reconnect سوکت، وضعیت جاری با این API خوانده شود:

```http
GET /v1/diet/{dietRequest}/shopping-list?period=daily
```

- پاسخ `404`: هنوز برای نسخه فعلی رژیم درخواستی ساخته نشده است.
- `pending` یا `processing`: پیام انتظار نمایش داده شود.
- `ready`: مقدار `result` نمایش داده شود.
- `failed`: دکمه تلاش مجدد نمایش داده شود.

اگر سوکت وصل نشد، کلاینت هر ۵ ثانیه همین API وضعیت جاری را poll کند و پس از `ready` یا `failed` polling را متوقف کند.

## تیک‌زدن قلم

```http
PATCH /v1/diet/{dietRequest}/shopping-list/{shoppingListId}/check
Content-Type: application/json

{
  "item_key": "item-key-from-result",
  "is_checked": true
}
```

پاسخ، کل سبد به‌روز‌شده را برمی‌گرداند. کلاینت باید `checked_items` و `progress_percent` پاسخ را جایگزین مقدار محلی کند.

## تلاش مجدد

فقط برای status برابر `failed`:

```http
POST /v1/diet/{dietRequest}/shopping-list/{shoppingListId}/retry
```

پاسخ `202` است و جریان انتظار سوکت/پولینگ دوباره آغاز می‌شود.

## نمایش result

ساختار نتیجه آماده:

```json
{
  "schema_version": 1,
  "engine": "openai",
  "prompt_version": "shopping-list-v1",
  "title": "سبد خرید فردا",
  "total_items": 3,
  "checked_items": 0,
  "progress_percent": 0,
  "groups": [
    {
      "key": "protein",
      "title": "پروتئین‌ها",
      "items": [
        {
          "key": "...",
          "name": "مرغ",
          "quantity": 200,
          "unit": "گرم",
          "display_quantity": "۲۰۰ گرم",
          "is_estimated": false,
          "is_checked": false,
          "sources": [
            {
              "date": "2026-09-10",
              "meal": "ناهار",
              "food_name": "خوراک مرغ و سبزیجات"
            }
          ]
        }
      ]
    }
  ]
}
```

`food_name` همان غذای ترکیبی نهایی تجویزشده و نمایش‌داده‌شده در رژیم است؛ Recipe مستقل پروژه در این قابلیت استفاده نمی‌شود.
