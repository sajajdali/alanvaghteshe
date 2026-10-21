# راهنمای تغییر دکمه صفحه Landing برای پکیج رایگان Onboarding

این فایل فقط تغییر مربوط به صفحه نتیجه عضویت یا Package Landing را توضیح می‌دهد. ساختار فعلی صفحه، طراحی و سایر جریان‌های اپلیکیشن نباید تغییر کنند.

## هدف تغییر

اگر کاربر از طریق Onboarding پکیج رایگان فعال گرفته است، نباید دکمه «خرید برنامه» ببیند؛ چون پکیج او قبلاً فعال شده است.

در این حالت باید به‌جای خرید، دکمه «ادامه و دریافت رژیم» نمایش داده شود و کاربر وارد جریان دریافت رژیم شود.

اگر کاربر پکیج رایگان Onboarding ندارد، همان رفتار فعلی صفحه و دکمه «خرید برنامه» بدون تغییر باقی بماند.

---

## آدرس API

```http
GET /v1/landing/bmi_result
```

این API نیازمند توکن کاربر است:

```http
Authorization: Bearer USER_ACCESS_TOKEN
Accept: application/json
```

---

## کلید جدید

کلید زیر به پاسخ API اضافه شده است:

```text
onboarding_package
```

این کلید یکی از دو حالت زیر را دارد:

- Object: کاربر پکیج رایگان فعال Onboarding دارد.
- `null`: کاربر پکیج رایگان فعال Onboarding ندارد.

کلید قدیمی `order_diet` حذف نشده است و همچنان در پاسخ وجود دارد. این کار برای سازگاری با نسخه‌های قبلی اپ انجام شده است.

> نکته مهم: اگر `onboarding_package` مقدار داشته باشد، وجود `order_diet` نباید باعث نمایش دکمه خرید شود. اولویت همیشه با `onboarding_package` است.

---

## حالت اول: کاربر پکیج رایگان Onboarding دارد

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
  }
}
```

وقتی `onboarding_package` یک Object است، اپ باید:

1. بلوک خرید `order_diet` را در UI نمایش ندهد.
2. عنوان را از `onboarding_package.title` بخواند.
3. توضیحات را از `onboarding_package.description` بخواند.
4. متن دکمه را از `onboarding_package.button` بخواند.
5. با لمس دکمه، مسیر `onboarding_package.route` را باز کند.
6. کاربر را به صفحه خرید یا انتخاب پکیج هدایت نکند.

مقادیر فعلی:

| بخش | کلید API | مقدار فعلی |
|---|---|---|
| عنوان | `onboarding_package.title` | پکیج رایگان شما فعال است |
| توضیحات | `onboarding_package.description` | پکیج رایگان Onboarding برای شما فعال شده است... |
| متن دکمه | `onboarding_package.button` | ادامه و دریافت رژیم |
| مسیر دکمه | `onboarding_package.route` | `/new_diet` |

تمام این متن‌ها و Route باید از پاسخ API خوانده شوند و داخل اپ Hard-code نشوند.

فیلدهای `package_id` و `package_user_id` برای شناسایی پکیج هستند. برای تصمیم نمایش دکمه نباید مقدار آن‌ها با یک عدد ثابت مقایسه شود.

---

## حالت دوم: کاربر پکیج رایگان Onboarding ندارد

نمونه پاسخ:

```json
{
  "title": "برنامه تخصصی شما ساخته شد.",
  "order_diet": {
    "title": "خرید برنامه رژیمی تخصصی",
    "button": "خرید برنامه",
    "description": "همین الان برنامه رژیمی تخصصی خودتون رو بگیرید و بریم برسیم به وزن هدف."
  },
  "onboarding_package": null
}
```

وقتی `onboarding_package` برابر `null` است، اپ باید رفتار فعلی را حفظ کند:

1. عنوان را از `order_diet.title` بخواند.
2. توضیحات را از `order_diet.description` بخواند.
3. متن دکمه را از `order_diet.button` بخواند.
4. با لمس دکمه، همان جریان خرید پکیج فعلی اپ را اجرا کند.

اگر کلید `onboarding_package` در پاسخ وجود نداشت نیز باید معادل `null` در نظر گرفته شود تا اپ با نسخه‌های قدیمی Backend سازگار بماند.

---

## منطق نهایی اپلیکیشن

تنها شرط معتبر برای انتخاب CTA این است:

```dart
final hasOnboardingPackage = response.onboardingPackage != null;
```

پیاده‌سازی پیشنهادی:

```dart
final onboardingPackage = response.onboardingPackage;

final ctaTitle = onboardingPackage != null
    ? onboardingPackage.title
    : response.orderDiet?.title;

final ctaDescription = onboardingPackage != null
    ? onboardingPackage.description
    : response.orderDiet?.description;

final ctaButtonText = onboardingPackage != null
    ? onboardingPackage.button
    : response.orderDiet?.button;

void onCtaPressed() {
  if (onboardingPackage != null) {
    final route = onboardingPackage.route;

    if (route != null && route.isNotEmpty) {
      appRouter.navigateByApiRoute(route);
    }

    return;
  }

  openExistingPackagePurchaseFlow();
}
```

نام متدهای Navigation در مثال فرضی است و باید با Router فعلی اپ جایگزین شود.

منطق خلاصه:

```text
onboarding_package != null
    → نمایش اطلاعات onboarding_package
    → نمایش دکمه «ادامه و دریافت رژیم»
    → هدایت به route برگشتی API
    → عدم نمایش خرید برنامه

onboarding_package == null
    → نمایش اطلاعات order_diet
    → نمایش دکمه «خرید برنامه»
    → اجرای جریان خرید فعلی اپ
```

---

## شرط‌هایی که نباید استفاده شوند

وجود `order_diet` نباید شرط نمایش خرید باشد؛ چون این کلید برای سازگاری همیشه در پاسخ باقی مانده است:

```dart
// اشتباه
if (response.orderDiet != null) {
  showPurchaseButton();
}
```

شناسه پکیج نیز نباید Hard-code شود:

```dart
// اشتباه
if (response.onboardingPackage?.packageId == 4) {
  showContinueButton();
}
```

متن دکمه هم نباید مبنای منطق باشد:

```dart
// اشتباه
if (response.onboardingPackage?.button == 'ادامه و دریافت رژیم') {
  showContinueButton();
}
```

تنها شرط درست:

```dart
response.onboardingPackage != null
```

---

## مدل پیشنهادی برای کلید جدید

اگر مدل پاسخ از قبل وجود دارد، فقط فیلد nullable زیر به آن اضافه شود:

```dart
@JsonKey(name: 'onboarding_package')
final OnboardingPackageContent? onboardingPackage;
```

مدل محتوای جدید:

```dart
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

---

## Cache و Refresh

کاربر پکیج رایگان را با API زیر فعال می‌کند:

```http
POST /v1/onboarding/free-package/activate
```

بعد از موفقیت این درخواست:

1. Cache پاسخ Landing پاک یا invalidate شود.
2. هنگام ورود به صفحه نتیجه، `GET /v1/landing/bmi_result` دوباره فراخوانی شود.
3. CTA براساس مقدار جدید `onboarding_package` ساخته شود.

در غیر این صورت ممکن است اپ پاسخ قبلی `onboarding_package: null` را نگه دارد و اشتباهاً دکمه خرید نمایش دهد.

تا زمانی که پاسخ API دریافت نشده است، دکمه خرید نباید به‌صورت پیش‌فرض یا لحظه‌ای نمایش داده شود. در حالت Loading از Loading فعلی صفحه یا Skeleton استفاده شود.

---

## مدیریت خطا

- اگر `onboarding_package` مقدار داشت ولی `route` خالی بود، کاربر نباید به صفحه خرید هدایت شود.
- Route خالی در این حالت خطای داده محسوب می‌شود و باید امکان Retry فراهم شود.
- Route ناشناخته نباید باعث Crash یا صفحه سیاه شود.
- در صورت Double Tap، فقط یک Navigation انجام شود.

---

## تست‌های تحویل

### کاربر دارای پکیج رایگان Onboarding

1. کاربر پکیج رایگان Onboarding را فعال کند.
2. وارد صفحه Landing شود.
3. `onboarding_package` در پاسخ Object باشد.
4. عنوان و توضیحات از `onboarding_package` نمایش داده شوند.
5. دکمه «ادامه و دریافت رژیم» دیده شود.
6. هیچ متن یا دکمه «خرید برنامه» دیده نشود.
7. لمس دکمه، Route `/new_diet` را باز کند.

### کاربر بدون پکیج رایگان Onboarding

1. مقدار `onboarding_package` برابر `null` باشد.
2. اطلاعات `order_diet` نمایش داده شوند.
3. دکمه «خرید برنامه» دیده شود.
4. دکمه، جریان خرید فعلی اپ را باز کند.

### سازگاری با پاسخ قدیمی

1. پاسخ تستی بدون کلید `onboarding_package` به اپ داده شود.
2. Parsing نباید Crash کند.
3. اپ باید نبودن کلید را معادل `null` بداند.
4. جریان خرید فعلی نمایش داده شود.

### وجود هم‌زمان دو کلید

1. `order_diet` و `onboarding_package` هر دو در پاسخ وجود داشته باشند.
2. فقط اطلاعات `onboarding_package` در UI نمایش داده شوند.
3. وجود `order_diet` باعث نمایش دکمه خرید نشود.

---

## معیار پذیرش نهایی

- فقط یک فیلد nullable به مدل موجود اضافه شده باشد: `onboarding_package`.
- Structure صفحه موجود تغییر نکرده باشد.
- اگر `onboarding_package` مقدار دارد، CTA از همان Object ساخته شود.
- اگر `onboarding_package` مقدار دارد، خرید برنامه در UI دیده نشود.
- اگر `onboarding_package` مقدار ندارد، رفتار خرید فعلی بدون تغییر اجرا شود.
- تمام عنوان‌ها، توضیحات، متن دکمه و Route از API خوانده شوند.
- بعد از فعال‌سازی پکیج رایگان، Landing Refresh شود.
- پاسخ قدیمی یا مقدار `null` بدون Crash مدیریت شود.
