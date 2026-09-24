# پلن نهایی سبد خرید هوشمند رژیم

## تصمیم‌های قطعی

- سیستم فعلی تولید، جایگزینی و نمایش رژیم تغییر نمی‌کند.
- فقط جدول `diet_shopping_lists` اضافه می‌شود.
- منبع داده فقط `diet_request_details.detail.food_snapshot` و ردیف‌های `replaced_parent_id IS NULL` است.
- `recipes`، `recipe_basic_foods` و قابلیت دستور پخت در این ویژگی استفاده نمی‌شوند.
- پردازش با Queue انجام و نتیجه با Pusher روی Private Channel اعلام می‌شود.
- `daily` دقیقاً فردا و `weekly` از فردا تا هفت روز کامل در timezone `Asia/Tehran` است.
- کلاینت فقط `period` را می‌فرستد و اجازه تعیین تاریخ ندارد.
- اطلاعات هویتی، پزشکی و پروفایل کاربر برای AI ارسال نمی‌شود.

## تنها جدول جدید

```text
diet_shopping_lists
id
uuid
user_id
diet_request_id
period: daily | weekly
start_date
end_date
status: pending | processing | ready | failed
input_hash
result JSON nullable
model nullable
input_tokens nullable
output_tokens nullable
error_message nullable
started_at nullable
generated_at nullable
created_at
updated_at
```

محدودیت‌ها:

```text
INDEX(user_id, diet_request_id)
INDEX(status)
UNIQUE(diet_request_id, period, input_hash)
FOREIGN KEY user_id -> users.id
FOREIGN KEY diet_request_id -> diet_requests.id
```

تمام گروه‌ها، اقلام، منابع و تیک‌ها در `result` ذخیره می‌شوند.

## قرارداد بازه

اگر امروز `2026-09-09` باشد:

```text
daily:  2026-09-10 تا 2026-09-10
weekly: 2026-09-10 تا 2026-09-16
```

## منبع استخراج

```sql
WHERE diet_request_id = ?
  AND date_of_day BETWEEN ? AND ?
  AND replaced_parent_id IS NULL
```

از هر ردیف این داده‌ها استخراج می‌شود:

```text
diet_request_detail_id
meal_id و meal_name
date_of_day
food_snapshot.food_id
food_snapshot.food_name
food_snapshot.basic_foods
```

`food_snapshot.food_name` غذای ترکیبی نهایی تجویز‌شده و منبع اقلام خرید است. متن نمایشی API Parse نمی‌شود.

---

# فاز ۱: دیتابیس و استخراج دقیق رژیم

## هدف

ایجاد هسته سبد بدون AI و بدون تغییر API فعلی رژیم.

## کارها

1. تست محافظ برای `DietController::detail` و `DietService::makeDietJsonFile`.
2. ساخت migration تنها جدول جدید و مدل `DietShoppingList`.
3. تعریف enumهای period و status و castهای JSON/تاریخ.
4. تعریف relation با `User` و `DietRequest`.
5. ساخت `DietShoppingSnapshotExtractor`.
6. کنترل مالکیت و معتبر بودن رژیم.
7. محاسبه دقیق daily و weekly در timezone تهران.
8. استخراج snapshot فقط از ردیف نهایی بازه.
9. حفظ غذای ترکیبی، وعده، تاریخ و detail ID به‌عنوان source.
10. تولید `input_hash` پایدار.

Hash شامل رژیم، period، تاریخ شروع و پایان، detail ID، وعده، تاریخ، `food_snapshot` و نسخه الگوریتم است. تیک اقلام در hash نیست؛ جایگزینی غذا hash را تغییر می‌دهد.

## تست‌ها

- عدم دسترسی به رژیم کاربر دیگر.
- daily فقط فردا و weekly دقیقاً هفت روز باشد.
- غذای قبلی جایگزین‌شده حذف و غذای نهایی استفاده شود.
- snapshot منبع باشد، نه اطلاعات جاری `foods`.
- Recipe در هیچ Query استفاده نشود.
- hash ثابت و نسبت به جایگزینی حساس باشد.

## تحویل فاز ۱

جدول، مدل و سرویس استخراج مواد خام روزانه/هفتگی آماده و تست‌شده است.

---

# فاز ۲: API و Queue غیرهمزمان

## هدف

ثبت فوری درخواست و انجام پردازش در پس‌زمینه، بدون معطل‌کردن کاربر.

## APIها

```http
POST /v1/diet/{diet_request}/shopping-list/generate
GET  /v1/diet/{diet_request}/shopping-list?period=daily
GET  /v1/diet/{diet_request}/shopping-list/{shopping_list}
```

Body ساخت:

```json
{"period":"daily"}
```

پاسخ فوری:

```http
202 Accepted
```

```json
{
  "status": "pending",
  "shopping_list_id": 42,
  "message": "درخواست ساخت سبد خرید ارسال شد."
}
```

## کارها

1. Form Request برای `daily/weekly`.
2. Policy/کنترل مالکیت DietRequest و ShoppingList.
3. افزودن متد پاسخ `accepted()` به الگوی API.
4. ساخت `ShoppingListController` مستقل.
5. ساخت/بازیابی اتمیک رکورد بر اساس `input_hash` داخل transaction.
6. جلوگیری از race condition با unique constraint.
7. ساخت `GenerateDietShoppingListJob` روی Queue به نام `ai-shopping-list`.
8. وضعیت‌های `pending -> processing -> ready/failed`.
9. ثبت زمان شروع، پایان و خطای کنترل‌شده.
10. ذخیره خروجی آزمایشی معتبر، بدون AI، برای تست مسیر کامل.
11. rate limit روی generate و retry.

رفتار کلیک تکراری:

```text
pending     همان رکورد؛ درخواست در صف است
processing  همان رکورد؛ در حال محاسبه است
ready       همان نتیجه برگردد
failed      امکان retry داده شود
```

## تست‌ها

- پاسخ سریع ۲۰۲ و قرارگرفتن Job در Database Queue.
- چند کلیک فقط یک رکورد و Job بسازد.
- daily و weekly مستقل باشند.
- خطا وضعیت را failed کند.
- تغییر رژیم درخواست جدید با hash جدید بسازد.

## تحویل فاز ۲

مسیر `HTTP -> Database -> Queue` با نتیجه آزمایشی کامل کار می‌کند.

---

# فاز ۳: OpenAI و سبد واقعی

## هدف

تبدیل غذاهای تجویز‌شده به مواد قابل خرید، تجمیع نتیجه و ذخیره آن در `result`.

## ورودی AI

فقط تاریخ، نام وعده، نام غذای ترکیبی و `food_snapshot.basic_foods` ارسال می‌شود. نام، موبایل، وزن، بیماری، user ID و پروفایل ارسال نمی‌شود.

نمونه:

```json
{
  "period": "daily",
  "start_date": "2026-09-10",
  "end_date": "2026-09-10",
  "meals": [{
    "date": "2026-09-10",
    "meal": "صبحانه",
    "food_name": "املت گوجه‌فرنگی با نان تافتون",
    "foods": [{
      "basic_food_id": 1411,
      "name": "املت گوجه‌فرنگی",
      "quantity": 12,
      "unit": "قاشق غذاخوری",
      "grams": 240
    }]
  }]
}
```

## وظیفه AI

- تجزیه غذای ترکیبی و عدم تجزیه بی‌دلیل قلم ساده.
- تجمیع مواد تکراری در روز یا هفته.
- محاسبه مقدار و واحد قابل فهم.
- علامت `is_estimated` برای مقادیر تخمینی.
- حفظ تاریخ، وعده و غذای تجویز‌شده به‌عنوان source.
- استفاده فقط از دسته‌های ثابت قرارداد.

دسته‌ها:

```text
protein, dairy, vegetables, fruits, bread_grains,
legumes, nuts_seeds, condiments, beverages, other
```

## شکل result

```json
{
  "title": "سبد خرید فردا",
  "total_items": 1,
  "checked_items": 0,
  "progress_percent": 0,
  "groups": [{
    "key": "protein",
    "title": "پروتئین‌ها",
    "items": [{
      "key": "egg",
      "name": "تخم‌مرغ",
      "quantity": 2,
      "unit": "عدد",
      "display_quantity": "۲ عدد",
      "is_estimated": true,
      "is_checked": false,
      "sources": [{
        "date": "2026-09-10",
        "meal": "صبحانه",
        "food_name": "املت گوجه‌فرنگی با نان تافتون"
      }]
    }]
  }]
}
```

## کارها

1. سرویس مستقل OpenAI و Prompt نسخه‌دار.
2. Structured Output با JSON Schema و `strict=true`.
3. درخواست با `store=false`، timeout و retry محدود.
4. validation کامل category، key، مقادیر و sourceها.
5. یکتا بودن item key در کل نتیجه.
6. محاسبه مجدد total/progress در Laravel، نه اعتماد به AI.
7. ثبت مدل و token usage.
8. ذخیره result فقط بعد از validation موفق.
9. مدیریت parse، validation، timeout و provider error با وضعیت failed.
10. API تیک‌زدن item داخل JSON و API retry.

```http
PATCH /v1/diet/{diet_request}/shopping-list/{shopping_list}/check
POST  /v1/diet/{diet_request}/shopping-list/{shopping_list}/retry
```

## تست‌ها

- تجزیه صحیح غذای ترکیبی و حفظ غذای منبع.
- عدم تجزیه بی‌دلیل غذای ساده.
- یک item برای مواد تکراری هفته.
- رد category، key و مقدار نامعتبر.
- عدم نمایش پاسخ نامعتبر AI.
- محاسبه progress در Laravel.
- عدم وجود اطلاعات شخصی در payload.

## تحویل فاز ۳

سبد واقعی daily/weekly تولید، ذخیره، مشاهده، تیک و retry می‌شود.

---

# فاز ۴: WebSocket، کلاینت و انتشار

## هدف

اعلام لحظه‌ای نتیجه و پایداری کامل در قطع و وصل اینترنت.

## وضعیت زیرساخت

```text
Pusher PHP SDK نصب است.
broadcasting.default روی pusher است.
queue.default روی database است.
BroadcastServiceProvider هنوز لود نشده است.
Route احراز Broadcast هنوز ثبت نشده است.
```

## کارهای Backend

1. فعال‌کردن `BroadcastServiceProvider`.
2. ثبت احراز Broadcast با `auth:sanctum`.
3. انتقال تنظیمات Pusher به env و حذف کلید hardcode‌شده.
4. تعریف `private-users.{userId}.shopping-list`.
5. مجاز بودن اتصال فقط وقتی `(int)$user->id === (int)$userId`.
6. ساخت eventهای `shopping-list.processing`، `shopping-list.ready` و `shopping-list.failed`.
7. ارسال event با `ShouldBroadcastNow` بعد از commit دیتابیس.
8. ارسال فقط ID، period، status و message؛ نتیجه کامل از API خوانده شود.

## رفتار کلاینت

1. هنگام ورود وضعیت را از API بخواند.
2. با Bearer Token وارد Private Channel شود.
3. generate را با HTTP بفرستد و پاسخ ۲۰۲ را فوراً نمایش دهد.
4. هنگام processing دکمه را غیرفعال کند.
5. با event ready، نتیجه کامل را از API بگیرد.
6. با event failed، خطا و retry نمایش دهد.
7. بعد از reconnect دوباره status را از API بررسی کند.
8. نتیجه وابسته به بازبودن صفحه یا اتصال دائم Socket نباشد.

## تست و انتشار

- هر کاربر فقط event خودش را دریافت کند.
- اتصال بدون Token یا با Token کاربر دیگر رد شود.
- قطع Socket نتیجه را از بین نبرد و reconnect آن را بازیابی کند.
- event بعد از ذخیره موفق دیتابیس ارسال شود.
- کلیک تکراری هنگام processing Job جدید نسازد.
- Worker صف `ai-shopping-list` در سرور اجرا و مانیتور شود.
- timeout، retry، logging امن، migration و rollback بررسی شوند.
- تست انتها‌به‌انتها برای daily و weekly اجرا شود.

## تحویل فاز ۴

جریان کامل زیر آماده انتشار است:

```text
HTTP -> Queue -> OpenAI -> Database -> WebSocket -> API
```

---

# ترتیب اجرای مصوب

```text
فاز ۱: دیتابیس و استخراج دقیق رژیم
فاز ۲: API و Queue غیرهمزمان
فاز ۳: OpenAI و نتیجه واقعی سبد
فاز ۴: WebSocket، کلاینت و انتشار
```

هر فاز همراه تست‌های خودش تکمیل و تأیید می‌شود، سپس فاز بعدی آغاز خواهد شد. تغییر API فعلی رژیم، سیستم غذا، جایگزینی غذا یا بخش Recipe خارج از محدوده است.
