# راهنمای نمایش دکمه خرید یا دریافت رژیم در صفحه نتیجه عضویت

## هدف این تغییر

این راهنما مربوط به صفحه‌ای از اپلیکیشن است که بعد از تکمیل اطلاعات عضویت، نتیجه BMI و پیام «برنامه تخصصی شما ساخته شد» را نمایش می‌دهد. ممکن است این صفحه در پروژه اپلیکیشن با نام‌هایی مانند `package/landing`، Landing یا BMI Result شناخته شود.

در این صفحه دو حالت متفاوت وجود دارد:

1. کاربر از طریق Onboarding یک پکیج رایگان فعال دریافت کرده است؛ در این حالت نباید از او خواسته شود دوباره برنامه بخرد.
2. کاربر پکیج رایگان فعال Onboarding ندارد؛ در این حالت رفتار قبلی صفحه و دکمه «خرید برنامه» باید حفظ شود.

Backend برای تشخیص این دو حالت کلید جدید `onboarding_package` را برمی‌گرداند. تصمیم اپلیکیشن باید فقط براساس مقدار همین کلید گرفته شود.

> نکته مهم: کلید قدیمی `order_diet` برای سازگاری با نسخه‌های قبلی اپلیکیشن از پاسخ API حذف نشده است. بنابراین ممکن است هم‌زمان `order_diet` در پاسخ وجود داشته باشد و `onboarding_package` نیز مقدار داشته باشد. در این وضعیت، `onboarding_package` اولویت دارد و بلوک خرید نباید در رابط کاربری نمایش داده شود.

---

## آدرس API

```http
GET /v1/landing/bmi_result
```

این API نیازمند ورود کاربر است و باید توکن کاربر همراه درخواست ارسال شود:

```http
Authorization: Bearer USER_ACCESS_TOKEN
Accept: application/json
```

دامنه اصلی براساس محیط اپلیکیشن، مانند Production یا Staging، قبل از مسیر بالا قرار می‌گیرد.

---

## کلید اصلی تصمیم‌گیری

نام کلید جدید:

```text
onboarding_package
```

این کلید یکی از دو مقدار زیر را دارد:

- یک Object: کاربر پکیج رایگان فعال Onboarding دارد.
- مقدار `null`: کاربر پکیج رایگان فعال Onboarding ندارد.

در دوره انتشار تدریجی Backend، اگر کلید در پاسخ وجود نداشت نیز اپ باید آن را معادل `null` در نظر بگیرد تا با نسخه‌های قدیمی API سازگار بماند.

---

## حالت اول: پکیج رایگان Onboarding فعال است

نمونه پاسخ:

```json
{
  "title": "برنامه تخصصی شما ساخته شد.",
  "order_diet": {
    "title": "خرید برنامه رژیمی تخصصی",
    "button": "خرید برنامه",
    "description": "همین الان برنامه رژیمی تخصصی خودتون رو بگیرید و بریم برسیم به وزن هدف."
  },
  "onboarding_package": {
    "package_user_id": 10,
    "package_id": 4,
    "title": "پکیج رایگان شما فعال است",
    "button": "ادامه و دریافت رژیم",
    "route": "/new_diet",
    "description": "پکیج رایگان Onboarding برای شما فعال شده است. حالا می‌توانید ادامه دهید و رژیم خود را دریافت کنید."
  },
  "start_weight": 82,
  "target_weight": 70,
  "after_weight_plan": "با شرایطی که داری فقط توی 84 روز میتونی به وزن ایده آلت برسی!",
  "calorie": 2100,
  "units": {
    "protein": 30,
    "carb": 30,
    "fat": 30,
    "fiber": 10
  }
}
```

اگر `onboarding_package` یک Object بود، اپ باید:

1. بلوک خرید مبتنی بر `order_diet` را نمایش ندهد.
2. عنوان را از `onboarding_package.title` بخواند.
3. توضیحات را از `onboarding_package.description` بخواند.
4. متن دکمه را از `onboarding_package.button` بخواند.
5. با لمس دکمه، کاربر را به مسیر `onboarding_package.route` هدایت کند.
6. در وضعیت فعلی مقدار Route برابر `/new_diet` است و کاربر باید وارد فرایند دریافت رژیم شود، نه صفحه خرید پکیج.

### متن‌هایی که باید نمایش داده شوند

| بخش رابط کاربری | کلید API | مقدار فعلی |
|---|---|---|
| عنوان CTA | `onboarding_package.title` | پکیج رایگان شما فعال است |
| توضیح CTA | `onboarding_package.description` | پکیج رایگان Onboarding برای شما فعال شده است... |
| متن دکمه | `onboarding_package.button` | ادامه و دریافت رژیم |
| مسیر دکمه | `onboarding_package.route` | `/new_diet` |

متن‌های جدول بالا نباید داخل اپ Hard-code شوند. Backend ممکن است در آینده متن‌ها یا Route را تغییر دهد؛ اپ باید همیشه مقدار پاسخ API را نمایش دهد.

`package_id` شناسه نوع پکیج و `package_user_id` شناسه پکیج تخصیص‌یافته به همین کاربر است. این دو مقدار برای نمایش متن لازم نیستند، اما باید در مدل پاسخ نگهداری شوند تا در صورت نیاز به گزارش‌گیری، Analytics یا درخواست‌های بعدی قابل استفاده باشند.

---

## حالت دوم: پکیج رایگان فعال Onboarding وجود ندارد

نمونه پاسخ:

```json
{
  "title": "برنامه تخصصی شما ساخته شد.",
  "order_diet": {
    "title": "خرید برنامه رژیمی تخصصی",
    "button": "خرید برنامه",
    "description": "همین الان برنامه رژیمی تخصصی خودتون رو بگیرید و بریم برسیم به وزن هدف."
  },
  "onboarding_package": null,
  "start_weight": 82,
  "target_weight": 70,
  "after_weight_plan": "با شرایطی که داری فقط توی 84 روز میتونی به وزن ایده آلت برسی!",
  "calorie": 2100,
  "units": {
    "protein": 30,
    "carb": 30,
    "fat": 30,
    "fiber": 10
  }
}
```

اگر `onboarding_package` برابر `null` بود یا اصلاً در پاسخ وجود نداشت، اپ باید رفتار قبلی را اجرا کند:

1. عنوان خرید را از `order_diet.title` بخواند.
2. توضیحات خرید را از `order_diet.description` بخواند.
3. متن دکمه خرید را از `order_diet.button` بخواند.
4. با لمس دکمه، همان مسیر خرید پکیج که قبل از این تغییر در اپ وجود داشته است باز شود.

در این حالت هیچ متن یا دکمه‌ای از `onboarding_package` نباید ساخته یا نمایش داده شود.

---

## قانون نهایی و اولویت نمایش

منطق صفحه باید دقیقاً به شکل زیر باشد:

```text
API را دریافت کن
│
├── onboarding_package یک Object معتبر است
│   ├── اطلاعات CTA را از onboarding_package نمایش بده
│   ├── دکمه «ادامه و دریافت رژیم» را نمایش بده
│   ├── با کلیک، route برگشتی API را باز کن
│   └── order_diet را در UI نادیده بگیر
│
└── onboarding_package برابر null است یا وجود ندارد
    ├── اطلاعات CTA را از order_diet نمایش بده
    ├── دکمه «خرید برنامه» را نمایش بده
    └── مسیر خرید قبلی اپ را اجرا کن
```

شرط درست:

```dart
final hasOnboardingPackage = response.onboardingPackage != null;
```

شرط‌های زیر اشتباه هستند و نباید جایگزین شرط بالا شوند:

```dart
// اشتباه: وجود order_diet به معنی نمایش خرید نیست، چون این کلید برای سازگاری همیشه وجود دارد.
if (response.orderDiet != null) { ... }

// اشتباه: اپ نباید از package_id ثابت برای تشخیص استفاده کند.
if (response.onboardingPackage?.packageId == 4) { ... }

// اشتباه: متن دکمه نباید مبنای منطق برنامه باشد.
if (response.onboardingPackage?.button == 'ادامه و دریافت رژیم') { ... }
```

---

## پیشنهاد مدل‌های Flutter/Dart

نام کلاس‌ها اختیاری است، اما nullable بودن `onboardingPackage` ضروری است.

```dart
class BmiResultResponse {
  final String? title;
  final DietOrderContent? orderDiet;
  final OnboardingPackageContent? onboardingPackage;
  final double? startWeight;
  final double? targetWeight;
  final String? afterWeightPlan;
  final num? calorie;

  BmiResultResponse({
    this.title,
    this.orderDiet,
    this.onboardingPackage,
    this.startWeight,
    this.targetWeight,
    this.afterWeightPlan,
    this.calorie,
  });

  factory BmiResultResponse.fromJson(Map<String, dynamic> json) {
    return BmiResultResponse(
      title: json['title'] as String?,
      orderDiet: json['order_diet'] is Map<String, dynamic>
          ? DietOrderContent.fromJson(
              json['order_diet'] as Map<String, dynamic>,
            )
          : null,
      onboardingPackage: json['onboarding_package'] is Map<String, dynamic>
          ? OnboardingPackageContent.fromJson(
              json['onboarding_package'] as Map<String, dynamic>,
            )
          : null,
      startWeight: (json['start_weight'] as num?)?.toDouble(),
      targetWeight: (json['target_weight'] as num?)?.toDouble(),
      afterWeightPlan: json['after_weight_plan'] as String?,
      calorie: json['calorie'] as num?,
    );
  }
}

class DietOrderContent {
  final String? title;
  final String? button;
  final String? description;

  DietOrderContent({this.title, this.button, this.description});

  factory DietOrderContent.fromJson(Map<String, dynamic> json) {
    return DietOrderContent(
      title: json['title'] as String?,
      button: json['button'] as String?,
      description: json['description'] as String?,
    );
  }
}

class OnboardingPackageContent {
  final int? packageUserId;
  final int? packageId;
  final String? title;
  final String? button;
  final String? route;
  final String? description;

  OnboardingPackageContent({
    this.packageUserId,
    this.packageId,
    this.title,
    this.button,
    this.route,
    this.description,
  });

  factory OnboardingPackageContent.fromJson(Map<String, dynamic> json) {
    return OnboardingPackageContent(
      packageUserId: json['package_user_id'] as int?,
      packageId: json['package_id'] as int?,
      title: json['title'] as String?,
      button: json['button'] as String?,
      route: json['route'] as String?,
      description: json['description'] as String?,
    );
  }
}
```

اگر ابزار تولید مدل مانند `json_serializable` یا Freezed استفاده می‌شود، نگاشت نام فیلدها باید حفظ شود:

```dart
@JsonKey(name: 'order_diet')
final DietOrderContent? orderDiet;

@JsonKey(name: 'onboarding_package')
final OnboardingPackageContent? onboardingPackage;

@JsonKey(name: 'package_user_id')
final int? packageUserId;

@JsonKey(name: 'package_id')
final int? packageId;
```

---

## پیشنهاد پیاده‌سازی CTA داینامیک

یک CTA واحد در صفحه نگه دارید و Source آن را براساس `onboarding_package` انتخاب کنید. نیازی به ساخت دو صفحه یا تغییر Structure اصلی صفحه نیست.

```dart
final onboardingPackage = response.onboardingPackage;
final hasOnboardingPackage = onboardingPackage != null;

final ctaTitle = hasOnboardingPackage
    ? onboardingPackage.title
    : response.orderDiet?.title;

final ctaDescription = hasOnboardingPackage
    ? onboardingPackage.description
    : response.orderDiet?.description;

final ctaButtonText = hasOnboardingPackage
    ? onboardingPackage.button
    : response.orderDiet?.button;

void onCtaPressed() {
  if (hasOnboardingPackage) {
    final route = onboardingPackage.route;
    if (route != null && route.isNotEmpty) {
      appRouter.navigateByApiRoute(route);
    }
    return;
  }

  openExistingPackagePurchaseFlow();
}
```

در Widget نیز فقط CTA انتخاب‌شده نمایش داده شود:

```dart
if (ctaTitle != null &&
    ctaDescription != null &&
    ctaButtonText != null) ...[
  Text(ctaTitle),
  Text(ctaDescription),
  PrimaryButton(
    title: ctaButtonText,
    onPressed: onCtaPressed,
  ),
]
```

نام `appRouter.navigateByApiRoute` و `openExistingPackagePurchaseFlow` در مثال فرضی است و باید با Router و معماری فعلی اپ جایگزین شود.

---

## متن‌های ثابت و متن‌های داینامیک صفحه

قاعده کلی این است که هر متنی که API در پاسخ می‌دهد، از API خوانده شود و داخل اپ تکرار نشود.

| محتوای صفحه | منبع |
|---|---|
| تیتر اصلی صفحه | `title` |
| CTA کاربر دارای پکیج Onboarding | `onboarding_package.title`، `description` و `button` |
| CTA خرید کاربر بدون پکیج Onboarding | `order_diet.title`، `description` و `button` |
| وزن شروع | `start_weight` |
| وزن هدف | `target_weight` |
| متن زمان رسیدن به هدف | `after_weight_plan` |
| کالری | `calorie` |
| درصد مواد غذایی | `units` |

اپ نباید متن «خرید برنامه» را به‌صورت ثابت روی دکمه قرار دهد؛ چون در حالت پکیج رایگان باید متن برگشتی `onboarding_package.button` نمایش داده شود.

---

## چه زمانی Backend مقدار `onboarding_package` می‌دهد؟

Backend فقط وقتی Object برمی‌گرداند که هر دو شرط زیر برقرار باشند:

1. پکیج واقعاً از طریق عملیات فعال‌سازی پکیج رایگان Onboarding به همین کاربر تخصیص داده شده باشد.
2. همان پکیج در زمان درخواست API همچنان فعال باشد.

بنابراین در شرایط زیر مقدار این کلید `null` خواهد بود:

- کاربر پیشنهاد Onboarding را رد کرده یا پکیج را فعال نکرده باشد.
- Onboarding برای کاربر پکیجی تخصیص نداده باشد.
- پکیج رایگان قبلاً منقضی، لغو یا غیرفعال شده باشد.
- کاربر صرفاً یک پکیج معمولی را از مسیر دیگری خریده باشد؛ این کلید مشخصاً نشان‌دهنده پکیج تخصیص‌یافته توسط Onboarding است، نه هر نوع عضویت.
- پاسخ از نسخه قدیمی Backend دریافت شود و کلید هنوز وجود نداشته باشد؛ اپ باید این وضعیت را مانند `null` مدیریت کند.

اپ نباید این شرایط را با محاسبه تاریخ، شناسه ثابت پکیج یا اطلاعات ذخیره‌شده محلی بازسازی کند. پاسخ جدید API مرجع نهایی تصمیم‌گیری است.

---

## مدیریت Loading، خطا و کلیک چندباره

- تا قبل از دریافت پاسخ API، CTA خرید یا دریافت رژیم نمایش داده نشود یا Skeleton فعلی صفحه نمایش داده شود. نمایش موقت «خرید برنامه» قبل از کامل‌شدن درخواست باعث چشمک‌زدن UI و سردرگمی کاربر می‌شود.
- هنگام خطای API، رفتار خطای فعلی صفحه حفظ شود و براساس Cache قدیمی درباره خرید یا دریافت رژیم تصمیم‌گیری نشود.
- هنگام لمس دکمه، تا پایان Navigation یا عملیات جاری دکمه موقتاً غیرفعال شود تا با Double Tap دو مسیر هم‌زمان باز نشود.
- اگر `onboarding_package` موجود بود اما `route` خالی یا `null` بود، اپ نباید کاربر را به صفحه خرید بفرستد. این حالت خطای داده است؛ باید خطای قابل Retry نمایش داده و برای بررسی ثبت شود.
- Route برگشتی Backend باید فقط از Routeهای پشتیبانی‌شده اپ عبور کند؛ Route ناشناخته باید با Fallback کنترل‌شده مدیریت شود، نه صفحه سیاه یا Crash.

---

## حفظ Navigation صحیح

وقتی دکمه «ادامه و دریافت رژیم» نمایش داده می‌شود:

- صفحه خرید پکیج نباید باز شود.
- Route فعلی `/new_diet` باید کاربر را وارد اولین فرایند دریافت رژیم کند.
- بازگشت کاربر نباید او را وارد Onboarding تمام‌شده یا صفحه پرداخت کند.

وقتی دکمه «خرید برنامه» نمایش داده می‌شود:

- Navigation قبلی اپ برای انتخاب و خرید پکیج بدون تغییر استفاده شود.
- نبودن `route` در `order_diet` طبیعی است؛ این بخش در قرارداد قدیمی Route نداشته و اپ باید همان مسیر خرید موجود خودش را اجرا کند.

---

## سازگاری با نسخه‌های قبلی و Cache

- هیچ‌کدام از کلیدهای قدیمی پاسخ حذف نشده‌اند.
- `onboarding_package` باید در مدل اپ Optional/Nullable تعریف شود.
- نبودن کلید و مقدار `null` باید یک رفتار داشته باشند.
- اگر پاسخ این API Cache می‌شود، Cache باید پس از موفقیت API فعال‌سازی پکیج رایگان زیر پاک یا Refresh شود:

```http
POST /v1/onboarding/free-package/activate
```

در غیر این صورت ممکن است اپ پس از فعال‌شدن پکیج همچنان پاسخ قدیمی `onboarding_package: null` را نمایش دهد و کاربر را اشتباه به خرید هدایت کند.

بهترین رفتار بعد از فعال‌سازی موفق:

1. پاسخ فعال‌سازی دریافت شود.
2. وضعیت قبلی Landing از Cache حذف شود.
3. در زمان ورود به صفحه نتیجه، `GET /v1/landing/bmi_result` دوباره فراخوانی شود.
4. CTA براساس پاسخ جدید ساخته شود.

---

## سناریوهای تست الزامی اپلیکیشن

### سناریو ۱: فعال‌سازی موفق پکیج Onboarding

1. Onboarding در پنل فعال باشد و حالت پکیج رایگان داشته باشد.
2. کاربر جدید وارد Onboarding شود.
3. پکیج رایگان را فعال کند.
4. مراحل عضویت را تکمیل کند.
5. وارد صفحه نتیجه شود.
6. `onboarding_package` باید Object باشد.
7. عنوان «پکیج رایگان شما فعال است» نمایش داده شود.
8. دکمه «ادامه و دریافت رژیم» نمایش داده شود.
9. هیچ دکمه یا متن «خرید برنامه» در UI دیده نشود.
10. لمس دکمه باید فرایند دریافت رژیم را باز کند.

### سناریو ۲: ردکردن پیشنهاد پکیج رایگان

1. کاربر پیشنهاد رایگان را فعال نکند.
2. مراحل عضویت را ادامه دهد.
3. در پاسخ Landing، مقدار `onboarding_package` باید `null` باشد.
4. عنوان و متن `order_diet` نمایش داده شود.
5. دکمه «خرید برنامه» نمایش داده شود.
6. لمس دکمه باید مسیر خرید قبلی را باز کند.

### سناریو ۳: Onboarding غیرفعال

1. Onboarding از پنل غیرفعال باشد.
2. کاربر مراحل عضویت را تکمیل کند.
3. مقدار `onboarding_package` باید `null` باشد.
4. رفتار قدیمی خرید برنامه نمایش داده شود.

### سناریو ۴: پکیج هدیه منقضی یا لغو شده

1. کاربر سابقه دریافت پکیج رایگان Onboarding داشته باشد.
2. پکیج دیگر فعال نباشد.
3. مقدار `onboarding_package` باید `null` باشد.
4. اپ نباید صرفاً به دلیل وجود سابقه محلی Onboarding، دکمه دریافت رژیم را نمایش دهد.

### سناریو ۵: پاسخ Backend قدیمی

1. پاسخ تستی بدون کلید `onboarding_package` به اپ داده شود.
2. Parsing نباید Crash کند.
3. اپ باید آن را معادل `null` در نظر بگیرد.
4. رفتار قبلی `order_diet` نمایش داده شود.

### سناریو ۶: وجود هم‌زمان دو کلید

1. `order_diet` موجود باشد.
2. `onboarding_package` نیز Object باشد.
3. فقط CTA مربوط به `onboarding_package` نمایش داده شود.
4. وجود `order_diet` نباید باعث نمایش خرید شود.

### سناریو ۷: Refresh و بازکردن مجدد اپ

1. کاربر پکیج Onboarding فعال داشته باشد.
2. صفحه را Refresh کند یا اپ را ببندد و دوباره باز کند.
3. API مجدداً فراخوانی شود.
4. دکمه دریافت رژیم همچنان نمایش داده شود و به خرید برنگردد.

---

## معیار پذیرش نهایی

پیاده‌سازی زمانی کامل است که تمام موارد زیر برقرار باشند:

- مدل پاسخ، کلید nullable به نام `onboarding_package` را می‌شناسد.
- تصمیم نمایش CTA فقط با `onboarding_package != null` گرفته می‌شود.
- در حالت پکیج Onboarding، تمام متن‌ها از `onboarding_package` خوانده می‌شوند.
- در حالت پکیج Onboarding، هیچ متن یا دکمه خریدی نمایش داده نمی‌شود.
- دکمه حالت Onboarding از `route` برگشتی API استفاده می‌کند.
- در حالت `null` یا نبودن کلید، رفتار فعلی `order_diet` بدون تغییر اجرا می‌شود.
- متن‌ها Hard-code نشده‌اند.
- صفحه قبل از کامل‌شدن API دکمه اشتباه را لحظه‌ای نمایش نمی‌دهد.
- Cache بعد از فعال‌سازی پکیج رایگان Refresh می‌شود.
- پاسخ قدیمی، مقدار `null` و Route نامعتبر بدون Crash مدیریت می‌شوند.
- ساختار سایر بخش‌های صفحه و کلیدهای قبلی API تغییر نمی‌کند.

خلاصه قانون برای برنامه‌نویس اپ:

```dart
if (response.onboardingPackage != null) {
  // نمایش متن و دکمه از onboarding_package
  // هدایت به onboarding_package.route
  // عدم نمایش خرید
} else {
  // نمایش متن و دکمه از order_diet
  // اجرای مسیر خرید فعلی اپ
}
```
