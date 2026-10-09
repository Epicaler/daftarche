<div align="center">

<img src="public/favicon.svg" width="72" alt="لوگوی دفترچه">

# دفترچه

**داشبورد حسابداری شخصی برای مدیریت دخل و خرج، طلب‌ها و بدهی‌ها که روی سیستم خودتان اجرا می‌شود**

رابط کاربری فارسی و راست‌به‌چپ · تقویم شمسی · آماده اجرا با Docker

[![Telegram](https://img.shields.io/badge/Telegram-@MREpicaler-26A5E4?logo=telegram&logoColor=white)](https://t.me/MREpicaler)

[English](README.md)

<br>

<img src="docs/screenshots/dashboard.png" alt="داشبورد دفترچه" width="100%">

</div>

---

## امکانات

### داشبورد
- موجودی کل، درآمد و هزینه این ماه (با درصد تغییر نسبت به ماه قبل) و طلب خالص
- نمودار هزینه‌های روزانه هفته جاری، از شنبه تا جمعه
- یادآوری نزدیک‌ترین سررسید طلب یا بدهی، با دکمه ثبت پرداخت
- نمودار وضعیت تسویه: چه مقدار از کل طلب‌ها و بدهی‌ها پرداخت شده است
- دارایی خالص (موجودی + طلب − بدهی)
- نمودار درآمد و هزینه ۱۲ ماه اخیر و تفکیک هزینه‌ها بر اساس دسته

### دخل و خرج
- ثبت، ویرایش و حذف تراکنش با عنوان، دسته‌بندی، تاریخ شمسی و توضیحات
- فیلتر بر اساس ماه شمسی، نوع و دسته‌بندی، به‌همراه جستجو
- دسته‌بندی‌های دلخواه با رنگ‌هایی که برای افراد کوررنگ هم قابل تشخیص‌اند

### طلب و بدهی
- ثبت طلب (پولی که به کسی داده‌اید) و بدهی (پولی که از کسی گرفته‌اید) برای هر شخص
- تاریخ سررسید، پرداخت جزئی، تسویه کامل با یک کلیک و تاریخچه کامل پرداخت‌ها
- با وارد کردن نام جدید، شخص به‌صورت خودکار ساخته می‌شود
- صفحه مخصوص هر شخص با مانده حساب خالص شما با او

### گزارش و خروجی
- گزارش سالانه: جدول ماهانه، نمودار درآمد و هزینه، تفکیک دسته‌ها و بزرگ‌ترین هزینه‌ها
- خروجی CSV سازگار با اکسل برای تراکنش‌ها و طلب و بدهی‌ها

### تجربه کاربری
- فونت وزیرمتن و نمایش اعداد فارسی
- انتخابگر تاریخ شمسی، پیام‌های خطای فارسی و جداسازی سه‌رقمی مبلغ هنگام تایپ
- طراحی واکنش‌گرا برای موبایل، با فرم‌های کشویی و منوی کناری
- ورود با رمز عبور و محدودیت تعداد تلاش؛ مخصوص استفاده تک‌کاربره

## تصاویر

### اجزای داشبورد

| | |
|:---:|:---:|
| <img src="docs/screenshots/component-kpi-cards.png" alt="کارت‌های آمار"> | <img src="docs/screenshots/component-monthly-trend.png" alt="روند ۱۲ ماه"> |
| کارت‌های آمار | روند ۱۲ ماه اخیر |
| <img src="docs/screenshots/component-weekly-spending.png" alt="هزینه‌های هفته"> | <img src="docs/screenshots/component-settlement-gauge.png" alt="وضعیت تسویه"> |
| هزینه‌های این هفته | وضعیت تسویه حساب‌ها |
| <img src="docs/screenshots/component-reminder.png" alt="یادآوری"> | <img src="docs/screenshots/component-people.png" alt="حساب با اشخاص"> |
| یادآوری سررسید | حساب با اشخاص |

<p align="center"><img src="docs/screenshots/component-net-worth.png" alt="دارایی خالص" width="360"></p>

### صفحه‌ها

| | |
|:---:|:---:|
| <img src="docs/screenshots/transactions.png" alt="دخل و خرج"> | <img src="docs/screenshots/debts.png" alt="طلب و بدهی"> |
| دخل و خرج | طلب و بدهی |
| <img src="docs/screenshots/reports.png" alt="گزارش‌ها"> | <img src="docs/screenshots/person.png" alt="جزئیات شخص"> |
| گزارش‌های سالانه | جزئیات شخص |
| <img src="docs/screenshots/modal-transaction.png" alt="فرم تراکنش"> | <img src="docs/screenshots/categories.png" alt="دسته‌بندی‌ها"> |
| فرم تراکنش با تقویم شمسی | دسته‌بندی‌ها |

### موبایل

<p align="center">
  <img src="docs/screenshots/mobile-dashboard.png" alt="داشبورد موبایل" width="240">
  &nbsp;
  <img src="docs/screenshots/mobile-menu.png" alt="منوی موبایل" width="240">
  &nbsp;
  <img src="docs/screenshots/mobile-debt-form.png" alt="فرم طلب و بدهی در موبایل" width="240">
</p>

## فناوری‌ها

| بخش | فناوری |
|---|---|
| بک‌اند | PHP 8.3+ و Laravel 13 |
| رابط کاربری | Livewire 4، Alpine.js، Tailwind CSS 4 و ApexCharts |
| ساخت فایل‌ها | Vite 8 |
| دیتابیس | MariaDB / MySQL یا SQLite |
| تاریخ شمسی | `morilog/jalali` در PHP و `jalaali-js` در مرورگر |
| اجرا | Docker Compose شامل PHP-FPM، Nginx، Node (Vite) و MariaDB |

---

## نصب

یکی از دو روش زیر را انتخاب کنید. **ساده‌ترین روش Docker است** و فقط به نصب بودن Docker نیاز دارد.

### روش ۱: با Docker (پیشنهادی)

**پیش‌نیاز:** [Docker](https://docs.docker.com/get-docker/) همراه با Docker Compose نسخه ۲ (در ویندوز و مک: Docker Desktop، در لینوکس: Docker Engine)

**۱. دریافت پروژه**

```bash
git clone https://github.com/Epicaler/daftarche.git
cd daftarche
```

**۲. ساخت فایل تنظیمات**

```bash
cp .env.example .env
```

سپس فایل `.env` را باز کنید و دست‌کم این موارد را بررسی کنید:

| متغیر | توضیح |
|---|---|
| `ADMIN_EMAIL` و `ADMIN_PASSWORD` | ایمیل و رمز ورود شما؛ کاربر در اولین اجرا ساخته می‌شود |
| `DB_PASSWORD` و `DB_ROOT_PASSWORD` | رمزهای پیش‌فرض دیتابیس را عوض کنید |
| `UID` و `GID` | **فقط در لینوکس:** خروجی دستورهای `id -u` و `id -g` را بگذارید تا فایل‌هایی که کانتینرها می‌سازند متعلق به خودتان باشد |
| `APP_PORT` و `VITE_PORT` | اگر پورت‌های `8080` یا `5180` اشغال‌اند، عوضشان کنید |

**۳. اجرا**

```bash
docker compose up -d
```

اولین اجرا چند دقیقه طول می‌کشد. در این مدت image مربوط به PHP ساخته می‌شود، `composer install` و `npm ci` اجرا می‌شوند، `APP_KEY` به‌صورت خودکار در `.env` ساخته می‌شود و جدول‌های دیتابیس ایجاد می‌شوند. برای دیدن روند کار:

```bash
docker compose logs -f app node
```

**۴. ورود**

آدرس <http://localhost:8080> را باز کنید و با `ADMIN_EMAIL` و `ADMIN_PASSWORD` وارد شوید (پیش‌فرض: `admin@example.com` و `password`). بعد از ورود، رمز عبور را از بخش **تنظیمات** عوض کنید.

#### ساختار Docker

| سرویس | کار |
|---|---|
| `app` | PHP 8.4-FPM؛ در هر بار اجرا جدول‌های دیتابیس را به‌روز می‌کند |
| `web` | Nginx روی پورت `APP_PORT` (پیش‌فرض `8080`) |
| `node` | یک بار فایل‌های CSS و JS را می‌سازد و سپس Vite را روی `VITE_PORT` اجرا می‌کند |
| `db` | MariaDB 10.11 |

- **کد داخل image قرار نمی‌گیرد.** پوشه پروژه مستقیم به کانتینرها وصل است؛ تغییرات PHP و Blade با refresh صفحه و تغییرات CSS و JS بلافاصله اعمال می‌شوند و نیازی به build دوباره نیست.
- **اطلاعات دیتابیس در پوشه `docker/data/mariadb` می‌ماند.** با `docker compose down` و حتی `down -v` پاک نمی‌شود؛ فقط با حذف خود این پوشه از بین می‌رود.

#### دستورهای پرکاربرد

| کار | دستور |
|---|---|
| روشن و خاموش کردن | `docker compose up -d` و `docker compose down` |
| افزودن داده نمونه | `docker compose exec app php artisan db:seed --class=DemoSeeder` |
| پشتیبان‌گیری از دیتابیس | `docker compose exec db sh -c 'mariadb-dump -u"$MARIADB_USER" -p"$MARIADB_PASSWORD" "$MARIADB_DATABASE"' > backup.sql` |
| بازگردانی پشتیبان | `docker compose exec -T db sh -c 'mariadb -u"$MARIADB_USER" -p"$MARIADB_PASSWORD" "$MARIADB_DATABASE"' < backup.sql` |
| دستورهای artisan و composer | `docker compose exec app php artisan …` و `docker compose exec -u www-data app composer …` |
| اجرای تست‌ها | `docker compose exec -u www-data app php artisan test` |
| دیدن لاگ‌ها | `docker compose logs -f app` |

### روش ۲: بدون Docker (با PHP، Composer و Node روی سیستم خودتان)

**پیش‌نیازها**

- PHP نسخه **8.3 یا بالاتر** با افزونه‌های `pdo_mysql` یا `pdo_sqlite`، `mbstring`، `xml` و `zip`
- [Composer](https://getcomposer.org) نسخه ۲
- [Node.js](https://nodejs.org) نسخه **20.19 یا 22.12 به بالا** همراه با npm
- **MySQL 8 یا MariaDB 10.6 به بالا**؛ اگر از **SQLite** استفاده کنید، به چیز دیگری نیاز نیست

> نکته: [Laravel Herd](https://herd.laravel.com) (ویندوز و مک) یا [php.new](https://php.new) همه این‌ها را یک‌جا نصب می‌کنند.

**۱. دریافت پروژه و نصب وابستگی‌ها**

```bash
git clone https://github.com/Epicaler/daftarche.git
cd daftarche
composer install
npm install
```

**۲. ساخت فایل تنظیمات و کلید برنامه**

```bash
cp .env.example .env
php artisan key:generate
```

**۳. تنظیم دیتابیس در `.env`**؛ یکی از دو حالت زیر را انتخاب کنید.

**حالت SQLite** (ساده‌ترین؛ همه اطلاعات در یک فایل):

```dotenv
DB_CONNECTION=sqlite
# DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME and DB_PASSWORD can be removed
```

```bash
touch database/database.sqlite
```

**حالت MySQL یا MariaDB:** ابتدا یک دیتابیس خالی بسازید و سپس:

```dotenv
DB_CONNECTION=mariadb      # or mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=daftarche
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

همچنین `APP_URL=http://localhost:8000` و `ADMIN_EMAIL` و `ADMIN_PASSWORD` خودتان را تنظیم کنید.

**۴. ساخت جدول‌ها و کاربر**

```bash
php artisan migrate --seed
```

**۵. ساخت فایل‌های CSS و JS و اجرای برنامه**

```bash
npm run build
php artisan serve
```

آدرس <http://localhost:8000> را باز کنید. اگر روی کد کار می‌کنید، به‌جای `npm run build` دستور `npm run dev` را در یک ترمینال دیگر اجرا کنید تا تغییرات بلافاصله اعمال شوند.

### نصب روی سرور

پروژه را پشت Nginx یا Apache قرار دهید، به‌طوری که ریشه سایت (document root) پوشه `public/` باشد، و سپس:

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force --seed
php artisan optimize
```

در `.env` مقدارهای `APP_ENV=production` و `APP_DEBUG=false` و یک `APP_URL` با `https://` بگذارید. پوشه‌های `storage/` و `bootstrap/cache/` باید برای وب‌سرور قابل نوشتن باشند.

---

## تنظیمات

| متغیر | پیش‌فرض | توضیح |
|---|---|---|
| `APP_NAME` | `دفترچه` | نامی که در منو و عنوان صفحه‌ها نمایش داده می‌شود |
| `APP_CURRENCY` | `تومان` | واحد پول کنار مبلغ‌ها |
| `APP_TIMEZONE` | `Asia/Tehran` | منطقه زمانی برای «امروز»، هفته و ماه |
| `ADMIN_NAME`، `ADMIN_EMAIL`، `ADMIN_PASSWORD` | `مدیر`، `admin@example.com`، `password` | اولین کاربر؛ فقط وقتی ساخته می‌شود که هیچ کاربری وجود نداشته باشد |
| `APP_PORT` | `8080` | در Docker: پورت وب‌سرور |
| `VITE_PORT` | `5180` | پورت Vite |
| `UID` و `GID` | `1000` | در Docker: کاربر و گروهی که کانتینرها با آن اجرا می‌شوند |

## تست‌ها

```bash
php artisan test
```

تست‌ها همیشه روی یک دیتابیس SQLite موقت در حافظه اجرا می‌شوند. اگر قرار باشد به دیتابیس دیگری دست بزنند، اجرا نمی‌شوند تا اطلاعات واقعی از بین نرود.

## طراح و توسعه‌دهنده

طراحی و توسعه: **[@MREpicaler](https://t.me/MREpicaler)**

- تلگرام: [@MREpicaler](https://t.me/MREpicaler)
- گیت‌هاب: [@Epicaler](https://github.com/Epicaler)

برای پرسش، پیشنهاد یا گزارش مشکل می‌توانید در تلگرام پیام بدهید یا در گیت‌هاب یک issue ثبت کنید.

## مجوز

این پروژه تحت [مجوز MIT](LICENSE) منتشر شده است.

---

<p align="center">© ۱۴۰۵ <a href="https://t.me/MREpicaler">@MREpicaler</a> | تمامی حقوق محفوظ است.</p>
