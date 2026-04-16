# دليل نظام التحقق من البريد الإلكتروني

## 📧 نظرة عامة
تم تطبيق نظام تحقق من البريد الإلكتروني يتطلب المستخدمين تأكيد بريدهم الإلكتروني بعد التسجيل. عند تأكيد البريد، يتم تسجيل الدخول تلقائياً وإعادة التوجيه إلى الصفحة الرئيسية للتطبيق.

## 🔄 سير العمل

### 1. التسجيل (Registration)
- المستخدم يملأ نموذج التسجيل ويرسله
- يتم إنشاء حساب جديد في قاعدة البيانات
- يتم إنشاء رمز تحقق فريد وحفظه في جدول `email_verification_tokens`
- يتم إرسال بريد إلكتروني للمستخدم يحتوي على رابط التحقق
- يظهر رسالة للمستخدم تطلب منه التحقق من بريده الإلكتروني

### 2. التحقق من البريد (Email Verification)
- المستخدم يضغط على رابط التحقق في البريد الإلكتروني
- يتم التوجيه إلى: `http://localhost:8000/api/verify-email/{token}`
- يتم التحقق من صحة الرمز ومدة صلاحيته
- إذا كان صحيحاً:
  - يتم إنشاء توكن للمستخدم (Sanctum Token)
  - يتم حذف رمز التحقق من قاعدة البيانات
  - يتم إعادة التوجيه إلى: `http://localhost:5173/?token={authToken}&verified=true`

### 3. تسجيل الدخول التلقائي (Auto Login)
- عند فتح الصفحة الرئيسية للـ React، يتم فحص `AuthContext`
- يتم قراءة التوكن من URL إذا كان موجوداً
- يتم حفظ التوكن في `localStorage`
- يتم جلب بيانات المستخدم من الـ API
- يتم تعيين المستخدم كـ "مسجل دخول"

## 📁 الملفات المُنشأة/المُعدلة

### Backend (Laravel)

#### 1. Migration
- **الملف**: `database/migrations/2026_02_07_create_email_verification_tokens.php`
- **الوظيفة**: إنشاء جدول للرموز المؤقتة للتحقق من البريد

#### 2. Model
- **الملف**: `app/Models/EmailVerificationToken.php`
- **العلاقات**:
  - `belongsTo(User::class)` - الارتباط مع جدول المستخدمين

#### 3. Mail Class
- **الملف**: `app/Mail/EmailVerificationMail.php`
- **المتغيرات**:
  - `$token` - رمز التحقق
  - `$user` - بيانات المستخدم
  - `$verificationUrl` - رابط التحقق الكامل

#### 4. View (Email Template)
- **الملف**: `resources/views/emails/email_verification.blade.php`
- **المميزات**:
  - تصميم HTML احترافي
  - دعم اللغة العربية (RTL)
  - رابط تحقق وظيفي مع تعليمات

#### 5. Controller
- **الملف**: `app/Http/Controllers/API/AuthController.php`
- **الدوال المُضافة**:
  - `verifyEmail($token)` - التحقق من البريد وتسجيل الدخول التلقائي
- **التعديلات على `register()`**:
  - إنشاء رمز التحقق
  - إرسال البريد الإلكتروني
  - عدم إنشاء توكن فوراً (ينتظر التحقق)

#### 6. Routes
- **الملف**: `routes/api.php`
- **الرابط المُضاف**: `GET /api/verify-email/{token}`

### Frontend (React)

#### 1. AuthContext
- **الملف**: `src/context/AuthContext.jsx`
- **التعديلات**:
  - قراءة التوكن من URL عند التحميل
  - معالجة التوكن من بريد التحقق
  - حفظ التوكن في `localStorage` تلقائياً
  - تحديث دالة `register()` لعدم تسجيل الدخول فوراً

#### 2. Register Page
- **الملف**: `src/pages/Register.jsx`
- **التعديلات**:
  - إضافة رسالة نجاح عند التسجيل
  - توجيه المستخدم للتحقق من بريده
  - تنظيف النموذج بعد النجاح

## 🔐 إعدادات البريد (Mail Configuration)

### متطلبات .env
```
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@example.com
MAIL_FROM_NAME="My App"
```

### الخدمات المدعومة
- ✅ **Mailtrap** (للتطوير)
- ✅ **SMTP** (أي خادم SMTP)
- ✅ **Gmail**
- ✅ **SendGrid**
- ✅ **Mailgun**

## 🧪 اختبار النظام

### 1. اختبار التسجيل والبريد

#### باستخدام Mailtrap (موصى به للتطوير)
```bash
# 1. انتقل إلى https://mailtrap.io
# 2. تحقق من البريد المرسل في لوحة التحكم
# 3. انسخ رابط التحقق من البريل الموجود
# 4. الصق الرابط في المتصفح
```

#### اختبار محلي (بدون بريل حقيقي)
```bash
# استخدم Mail::log() للتطوير
# سيتم حفظ البريل في logs/laravel.log

# عدل .env
MAIL_MAILER=log
```

### 2. خطوات الاختبار اليدوي

1. **افتح التطبيق**: `http://localhost:5173`
2. **اذهب إلى صفحة التسجيل**: انقر على "تسجيل"
3. **ملء النموذج**:
   ```
   الاسم: Test User
   البريد الإلكتروني: test@example.com
   كلمة المرور: TestPassword123
   تأكيد: TestPassword123
   ```
4. **انقر على "تسجيل"**
5. **تحقق من البريد**:
   - في Mailtrap أو في logs
6. **انقر على رابط التحقق**
7. **يجب أن يتم:
   - إعادة التوجيه إلى `http://localhost:5173/?token=...`
   - تسجيل الدخول تلقائياً
   - عرض بيانات المستخدم

## 🛠️ معالجة الأخطاء

### أخطاء محتملة وحلولها

| الخطأ | السبب | الحل |
|-------|-------|------|
| رابط غير صالح | مدة الصلاحية انتهت | التسجيل مرة أخرى (24 ساعة) |
| البريل لم يصل | خادم SMTP معطل | التحقق من إعدادات `MAIL_*` |
| خطأ 500 | فشل إرسال البريل | تحقق من logs في `storage/logs/` |
| توكن غير صحيح | رمز تحقق فاسد | امسح cookies وحاول مرة أخرى |

## 🔒 نقاط الأمان

1. **رموز التحقق عشوائية**: استخدام `Str::random(64)`
2. **مدة صلاحية محدودة**: 24 ساعة فقط
3. **حذف الرموز بعد الاستخدام**: منع إعادة الاستخدام
4. **التوكن من البريل**: لا يتم حفظه في localStorage مباشرة قبل التحقق

## 📊 جدول البيانات

```sql
CREATE TABLE email_verification_tokens (
  id BIGINT PRIMARY KEY,
  user_id BIGINT NOT NULL,
  token VARCHAR(255) UNIQUE NOT NULL,
  expires_at TIMESTAMP NOT NULL,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX (token)
);
```

## 🚀 التحسينات المستقبلية

1. **إعادة إرسال البريل**: إضافة صفحة لإعادة إرسال رابط التحقق
2. **تأكيد عند تغيير البريل**: التحقق عند تغيير البريل الحالي
3. **قائمة بريد سوداء**: منع مجالات بريل معينة
4. **إرسال بريل ترحيب**: بعد التحقق بنجاح
5. **معدل الإرسال**: تحديد محاولات الإرسال

## 📧 اختبار الرسائل

### في Mailtrap
```
الموقع: https://mailtrap.io/inboxes
البريد الوارد: Demo Inbox
```

### في السجلات
```bash
tail -f storage/logs/laravel.log | grep -i verification
```

---

**ملاحظات مهمة:**
- لا تنسَ تشغيل `php artisan migrate` بعد التحديثات
- تأكد من أن إعدادات `MAIL_*` صحيحة في `.env`
- اختبر البريل في بيئة التطوير قبل الإنتاج
