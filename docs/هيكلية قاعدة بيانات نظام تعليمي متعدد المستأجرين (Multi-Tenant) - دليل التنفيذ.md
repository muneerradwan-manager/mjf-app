# هيكلية قاعدة بيانات نظام تعليمي متعدد المستأجرين (Multi-Tenant) - دليل التنفيذ

---

## حالة التنفيذ (Implementation Status) — آخر تحديث: 2026-04-15

### ✅ المنجز (Completed)

#### البنية التحتية (Infrastructure)
- [x] مشروع Laravel 13 مُنشأ ومُهيأ
- [x] حزمة `stancl/tenancy ^3.10` مثبتة ومُكوَّنة (نموذج "قاعدة بيانات لكل مستأجر")
- [x] بنية وحدات (Modules) تحت `app/Modules/Central/` و `app/Shared/`
- [x] نمط Repository مع `BaseRepository` / `BaseRepositoryInterface`
- [x] مزودا خدمة `RepositoryServiceProvider` و `TenancyServiceProvider` مسجَّلان
- [x] `bootstrap/app.php` مُهيأ مع مسارات API و معالجة JSON للاستثناءات

#### قاعدة البيانات المركزية (Central Database — `mjf_app`)
- [x] جدول `users` — مع `is_super_admin`, `current_tenant_id`
- [x] جدول `subscriptions` — مع جميع الحقول المطلوبة + Seeder (Basic Plan)
- [x] جدول `tenants` — مع `uuid`, `slug`, `code`, `db_name`, `type`, `owner_user_id`, `data` (لـ stancl)
- [x] جدول `domains` — مرتبط بـ `tenants` (مطلوب من stancl/tenancy)
- [x] جدول `tenant_user` — جدول pivot لربط المستخدمين بالمستأجرين
- [x] FK مؤجلة: `users.current_tenant_id → tenants.id` (في migration منفصلة لتجنب الدائرية)

#### توفير المستأجرين (Tenant Provisioning)
- [x] `TenantProvisionService::createTenantWithDatabase()` — يُنشئ سجل المستأجر، قاعدة البيانات، ويشغّل migrations
- [x] `Tenant` model مع تجاوز `getIncrementing()`, `getKeyType()`, `getCustomColumns()`, `getInternal('db_name')` لمتطلبات stancl
- [x] إلغاء auto-jobs `CreateDatabase` / `MigrateDatabase` من event listeners (يتم التنفيذ يدوياً في الـ Service)

#### قاعدة بيانات كل مستأجر (Tenant Database)
- [x] Migration لجدول `users` (tenant) مع `type ENUM('student','teacher','admin')`
- [x] Models + Migrations للجداول: `students`, `teachers`, `courses`, `classes`, `enrollments`, `assignments`, `submissions`, `grades`, `announcements`, `events`
- [x] كل مستأجر يحصل على قاعدة بيانات منفصلة باسم `tenant_xxxxxxxxxx`
- [x] التحقق: تم تشغيل `php artisan tenants:migrate --force` بنجاح على المستأجر الحالي (`Tenant: 1`) وأصبحت قاعدة بياناته تحتوي على الجداول التعليمية كاملة

#### المصادقة والصلاحيات (Authentication & Authorization)
- [x] تسجيل الدخول للمستخدم المركزي (Login API)
- [x] Laravel Sanctum مع جدول `personal_access_tokens`
- [x] `InitializeTenancyByCurrentUser` — Middleware لتهيئة tenancy من `current_tenant_id`
- [x] `EnsureActiveTenantAccess` — Middleware للتحقق من وجود مستأجر نشط وصلاحية المستخدم عليه
- [x] ربط `owner_user_id` تلقائياً من `auth()->id()` (بدل إرساله في الطلب)
- [x] `User`, `Tenant`, `Subscription`, و `PersonalAccessToken` مثبتة على الاتصال المركزي لتعمل المصادقة المركزية داخل Tenant API

#### API (Central)
- [x] `POST /api/login` — تسجيل دخول المستخدم المركزي عبر `Laravel Sanctum`
- [x] `POST /api/logout` — تسجيل خروج المستخدم وحذف الـ token الحالي
- [x] `GET /api/me` — جلب بيانات المستخدم الحالي + المستأجر الحالي + قائمة المستأجرين المتاحين
- [x] `POST /api/central/tenants` — إنشاء مستأجر جديد مع قاعدة بيانات ✅ **مختبر وناجح**
- [x] `POST /api/central/current-tenant` — تعيين `current_tenant_id` للمستخدم المسجل
- [x] `GET /api/health` — فحص حالة النظام
- [x] معالجة صحيحة لـ JSON responses لجميع مسارات `/api/*`

#### API (Tenant)
- [x] `GET /api/tenant/health` — فحص tenant context للمستخدم المسجل
- [x] CRUD كامل للطلاب `students` مع `Requests`, `Resources`, `Controller`
- [x] CRUD كامل للمعلمين `teachers` مع `Requests`, `Resources`, `Controller`
- [x] CRUD كامل للمقررات `courses` مع `Requests`, `Resources`, `Controller`
- [x] CRUD كامل للفصول `classes` مع `Requests`, `Resources`, `Controller`
- [x] CRUD كامل للتسجيلات `enrollments` مع منع تكرار تسجيل نفس الطالب في نفس الفصل
- [x] CRUD كامل للواجبات `assignments` مع ربطها بالفصل والمعلم
- [x] CRUD كامل للتسليمات `submissions` مع التحقق من تسجيل الطالب في الفصل ومن الحد الأعلى للدرجة
- [x] CRUD كامل للدرجات `grades` مع التحقق من الاتساق بين `course` و `assignment`
- [x] CRUD كامل للإعلانات `announcements` مع `Requests`, `Resources`, `Controller`
- [x] CRUD كامل للأحداث `events` مع `Requests`, `Resources`, `Controller`
- [x] اختبارات Feature لجميع مسارات Tenant API: `students`, `teachers`, `courses`, `classes`, `enrollments`, `assignments`, `submissions`, `grades`, `announcements`, `events`

---

### ✅ اكتمل التنفيذ بالكامل — لا يوجد متبقٍ

#### ملاحظات تقنية
- `db_user` و `db_password` في جدول `tenants` فارغان حالياً (يُستخدم مستخدم MySQL الرئيسي)
- التحقق الحالي: `php artisan test` ✅ نجح بالكامل (`33 passed, 167 assertions`)
- `APP_DEBUG=true` في `.env` — يجب تغييره لـ `false` في الإنتاج
- تسجيل دخول مستقل لمستخدمي tenant: غير مطلوب حالياً (مؤجل لقرار مستقبلي)

---

## 1. مقدمة

يقدم هذا المستند تفصيلاً شاملاً لهيكلية قاعدة البيانات لنظام تعليمي يعتمد على مفهوم تعدد المستأجرين (Multi-Tenancy) باستخدام Laravel. الهدف هو توفير دليل تنفيذي واضح ومفصل لـ Agent برمجـي أو مطور، يوضح الجداول، الحقول، أنواع البيانات، القيود، والعلاقات بين الكيانات في كل من قاعدة البيانات المركزية وقواعد بيانات المستأجرين المنفصلة.

## 2. نظرة عامة على هيكلية قاعدة البيانات

يعتمد النظام على نموذج "قاعدة بيانات لكل مستأجر" (Database Per Tenant)، مما يعني وجود:

*   **قاعدة بيانات مركزية (Central Database):** تحتوي على المعلومات الأساسية للنظام مثل المستأجرين، الاشتراكات، والمستخدمين الرئيسيين للنظام (Super Admins) والمستخدمين الذين يسجلون في النظام قبل أن يتم ربطهم بمستأجر معين. هذه القاعدة هي نقطة الدخول وإدارة المستأجرين.
*   **قواعد بيانات المستأجرين (Tenant Databases):** لكل مستأجر قاعدة بيانات منفصلة خاصة به. تحتوي هذه القواعد على جميع البيانات التشغيلية المتعلقة بالمستأجر، مثل الطلاب، المعلمين، المقررات، الفصول، الواجبات، وغيرها. هذا يضمن عزل البيانات، أمانًا أفضل، وقابلية توسع مرنة.

## 3. قاعدة البيانات المركزية (Central Database Schema)

تحتوي قاعدة البيانات المركزية على الجداول التالية:

### 3.1. جدول `users` (المستخدمون الرئيسيون للنظام)

يحتوي هذا الجدول على المستخدمين الذين يمكنهم تسجيل الدخول إلى النظام الرئيسي، بما في ذلك المسؤولون العامون (Super Admins) والمستخدمون الذين يقومون بإنشاء المستأجرين.

| الحقل              | النوع             | القيود                                       | الوصف                                                                |
| :----------------- | :----------------- | :-------------------------------------------- | :------------------------------------------------------------------- |
| `id`               | BIGINT UNSIGNED    | PK, AUTO_INCREMENT                            | معرف فريد للمستخدم                                                   |
| `name`             | VARCHAR(255)       | NOT NULL                                      | اسم المستخدم                                                         |
| `email`            | VARCHAR(255)       | NOT NULL, UNIQUE                              | البريد الإلكتروني للمستخدم (يجب أن يكون فريدًا)                     |
| `email_verified_at`| TIMESTAMP          | NULLABLE                                      | تاريخ ووقت تأكيد البريد الإلكتروني                                   |
| `password`         | VARCHAR(255)       | NOT NULL                                      | كلمة المرور المشفرة للمستخدم                                         |
| `remember_token`   | VARCHAR(100)       | NULLABLE                                      | رمز لتذكر جلسة المستخدم                                              |
| `is_super_admin`   | BOOLEAN            | NOT NULL, DEFAULT 0                           | لتحديد ما إذا كان المستخدم مسؤول نظام عام (Super Admin)             |
| `current_tenant_id`| BIGINT UNSIGNED    | NULLABLE, FK (`tenants.id`)                   | معرف المستأجر الحالي الذي يعمل عليه المستخدم (للتنقل بين المستأجرين) |
| `created_at`       | TIMESTAMP          | NULLABLE                                      | تاريخ ووقت إنشاء السجل                                               |
| `updated_at`       | TIMESTAMP          | NULLABLE                                      | تاريخ ووقت آخر تحديث للسجل                                           |

**العلاقات:**
*   `users` `hasMany` `tenants` (مستخدم واحد يمكن أن ينشئ عدة مستأجرين).
*   `users` `belongsTo` `tenants` (عبر `current_tenant_id` لتحديد المستأجر النشط).

### 3.2. جدول `tenants` (المستأجرون)

يحتوي هذا الجدول على معلومات كل مستأجر في النظام.

| الحقل            | النوع             | القيود                                       | الوصف                                                                |
| :--------------- | :----------------- | :-------------------------------------------- | :------------------------------------------------------------------- |
| `id`             | BIGINT UNSIGNED    | PK, AUTO_INCREMENT                            | معرف فريد للمستأجر                                                   |
| `uuid`           | CHAR(36)           | NOT NULL, UNIQUE                              | معرف فريد عالمي (UUID) للمستأجر لزيادة الأمان والتمييز               |
| `name`           | VARCHAR(255)       | NOT NULL                                      | اسم المستأجر (المسجد، المدرسة، الجامعة)                               |
| `slug`           | VARCHAR(255)       | NOT NULL, UNIQUE                              | معرف نصي فريد للمستأجر، يستخدم في الروابط (مثال: `masjed-al-nour`)   |
| `code`           | VARCHAR(255)       | NOT NULL, UNIQUE                              | رمز فريد يتم إنشاؤه بعد اشتراك المستخدم (يمكن استخدامه للوصول)       |
| `email`          | VARCHAR(255)       | NOT NULL, UNIQUE                              | البريد الإلكتروني للمستأجر                                           |
| `phone`          | VARCHAR(255)       | NULLABLE                                      | رقم هاتف المستأجر                                                    |
| `subscription_id`| BIGINT UNSIGNED    | NOT NULL, FK (`subscriptions.id`)             | يربط المستأجر بخطة الاشتراك الخاصة به                                |
| `type`           | ENUM               | NOT NULL, ("masjed", "school", "university") | نوع المستأجر                                                         |
| `db_name`        | VARCHAR(255)       | NOT NULL, UNIQUE                              | اسم قاعدة البيانات الخاصة بالمستأجر                                  |
| `db_user`        | VARCHAR(255)       | NOT NULL                                      | اسم المستخدم لقاعدة البيانات الخاصة بالمستأجر                       |
| `db_password`    | VARCHAR(255)       | NOT NULL                                      | كلمة المرور لقاعدة البيانات الخاصة بالمستأجر (يجب تشفيرها)          |
| `domain`         | VARCHAR(255)       | NULLABLE, UNIQUE                              | النطاق المخصص للمستأجر (مثال: `myschool.com`)                       |
| `subdomain`      | VARCHAR(255)       | NULLABLE, UNIQUE                              | النطاق الفرعي للمستأجر (مثال: `myschool.myapp.com`)                 |
| `is_active`      | BOOLEAN            | NOT NULL, DEFAULT 1                           | حالة نشاط المستأجر (يمكن تعطيله إداريًا)                             |
| `owner_user_id`  | BIGINT UNSIGNED    | NOT NULL, FK (`users.id`)                     | معرف المستخدم الذي أنشأ المستأجر أو مالكه الرئيسي                   |
| `settings`       | JSON               | NULLABLE                                      | إعدادات مخصصة للمستأجر (شعار، ألوان، لغة، إلخ)                       |
| `created_at`     | TIMESTAMP          | NULLABLE                                      | تاريخ ووقت إنشاء السجل                                               |
| `updated_at`     | TIMESTAMP          | NULLABLE                                      | تاريخ ووقت آخر تحديث للسجل                                           |

**العلاقات:**
*   `tenants` `belongsTo` `subscriptions` (مستأجر واحد ينتمي إلى اشتراك واحد).
*   `tenants` `belongsTo` `users` (عبر `owner_user_id`، المستأجر يمتلكه مستخدم واحد).
*   `tenants` `hasMany` `users` (المستخدمون الذين يمكنهم الوصول إلى هذا المستأجر).

### 3.3. جدول `subscriptions` (الاشتراكات)

يحتوي هذا الجدول على خطط الاشتراك المتاحة في النظام.

| الحقل            | النوع             | القيود                                       | الوصف                                                                |
| :--------------- | :----------------- | :-------------------------------------------- | :------------------------------------------------------------------- |
| `id`             | BIGINT UNSIGNED    | PK, AUTO_INCREMENT                            | معرف فريد للاشتراك                                                   |
| `title`          | VARCHAR(255)       | NOT NULL                                      | عنوان خطة الاشتراك (مثال: "الخطة الأساسية للمساجد")                 |
| `description`    | TEXT               | NULLABLE                                      | وصف تفصيلي لخطة الاشتراك                                            |
| `price`          | DECIMAL(8, 2)      | NOT NULL                                      | سعر الاشتراك                                                         |
| `currency`       | VARCHAR(3)         | NOT NULL, DEFAULT 'USD'                       | عملة السعر                                                           |
| `duration_in_days`| SMALLINT UNSIGNED  | NOT NULL                                      | مدة الاشتراك بالأيام (مثال: 30 ليوم شهري، 365 ليوم سنوي)             |
| `billing_period` | ENUM               | NOT NULL, ("monthly", "annually", "lifetime") | دورة الفوترة                                                         |
| `features`       | JSON               | NULLABLE                                      | ميزات الخطة (مثال: عدد الطلاب، مساحة التخزين، ميزات معينة)           |
| `status`         | ENUM               | NOT NULL, ("active", "archived")            | حالة الاشتراك (نشط، مؤرشف)                                         |
| `created_at`     | TIMESTAMP          | NULLABLE                                      | تاريخ ووقت إنشاء السجل                                               |
| `updated_at`     | TIMESTAMP          | NULLABLE                                      | تاريخ ووقت آخر تحديث للسجل                                           |

**العلاقات:**
*   `subscriptions` `hasMany` `tenants` (اشتراك واحد يمكن أن يرتبط بعدة مستأجرين).

### 3.4. جدول `tenant_user` (جدول وسيط لربط المستخدمين بالمستأجرين)

هذا الجدول ضروري لربط المستخدمين (من جدول `users` المركزي) بالمستأجرين الذين يمكنهم الوصول إليهم. يمكن للمستخدم الواحد أن يكون له صلاحية الوصول إلى عدة مستأجرين.

| الحقل            | النوع             | القيود                                       | الوصف                                                                |
| :--------------- | :----------------- | :-------------------------------------------- | :------------------------------------------------------------------- |
| `user_id`        | BIGINT UNSIGNED    | PK, FK (`users.id`)                           | معرف المستخدم من جدول `users` المركزي                                |
| `tenant_id`      | BIGINT UNSIGNED    | PK, FK (`tenants.id`)                         | معرف المستأجر من جدول `tenants`                                      |
| `role`           | VARCHAR(255)       | NOT NULL, DEFAULT 'member'                    | دور المستخدم داخل هذا المستأجر (مثال: 'admin', 'member')             |
| `created_at`     | TIMESTAMP          | NULLABLE                                      | تاريخ ووقت إنشاء السجل                                               |
| `updated_at`     | TIMESTAMP          | NULLABLE                                      | تاريخ ووقت آخر تحديث للسجل                                           |

**العلاقات:**
*   `tenant_user` `belongsTo` `users`.
*   `tenant_user` `belongsTo` `tenants`.

## 4. قواعد بيانات المستأجرين (Tenant Databases Schema)

كل قاعدة بيانات مستأجر ستحتوي على الجداول التالية. لاحظ أن `tenant_id` لن يكون موجودًا في هذه الجداول لأنه ضمنيًا جزء من قاعدة بيانات المستأجر نفسها.

### 4.1. جدول `users` (مستخدمو المستأجر)

يحتوي هذا الجدول على المستخدمين الخاصين بكل مستأجر (مثل الطلاب، المعلمين، الإداريين داخل المدرسة/المسجد/الجامعة).

| الحقل              | النوع             | القيود                                       | الوصف                                                                |
| :----------------- | :----------------- | :-------------------------------------------- | :------------------------------------------------------------------- |
| `id`               | BIGINT UNSIGNED    | PK, AUTO_INCREMENT                            | معرف فريد للمستخدم داخل المستأجر                                     |
| `name`             | VARCHAR(255)       | NOT NULL                                      | اسم المستخدم                                                         |
| `email`            | VARCHAR(255)       | NOT NULL, UNIQUE                              | البريد الإلكتروني للمستخدم (فريد داخل المستأجر)                     |
| `email_verified_at`| TIMESTAMP          | NULLABLE                                      | تاريخ ووقت تأكيد البريد الإلكتروني                                   |
| `password`         | VARCHAR(255)       | NOT NULL                                      | كلمة المرور المشفرة للمستخدم                                         |
| `remember_token`   | VARCHAR(100)       | NULLABLE                                      | رمز لتذكر جلسة المستخدم                                              |
| `type`             | ENUM               | NOT NULL, ("student", "teacher", "admin") | نوع المستخدم داخل المستأجر                                           |
| `created_at`       | TIMESTAMP          | NULLABLE                                      | تاريخ ووقت إنشاء السجل                                               |
| `updated_at`       | TIMESTAMP          | NULLABLE                                      | تاريخ ووقت آخر تحديث للسجل                                           |

**العلاقات:**
*   `users` `hasOne` `student` (إذا كان نوع المستخدم طالبًا).
*   `users` `hasOne` `teacher` (إذا كان نوع المستخدم معلمًا).

### 4.2. جدول `students` (الطلاب)

| الحقل              | النوع             | القيود                                       | الوصف                                                                |
| :----------------- | :----------------- | :-------------------------------------------- | :------------------------------------------------------------------- |
| `id`               | BIGINT UNSIGNED    | PK, AUTO_INCREMENT                            | معرف فريد للطالب                                                     |
| `user_id`          | BIGINT UNSIGNED    | NOT NULL, UNIQUE, FK (`users.id`)             | معرف المستخدم المرتبط بهذا الطالب                                   |
| `student_id_number`| VARCHAR(255)       | NULLABLE, UNIQUE                              | رقم تعريف الطالب (خاص بالمستأجر)                                     |
| `date_of_birth`    | DATE               | NULLABLE                                      | تاريخ ميلاد الطالب                                                   |
| `address`          | TEXT               | NULLABLE                                      | عنوان الطالب                                                         |
| `phone`            | VARCHAR(255)       | NULLABLE                                      | رقم هاتف الطالب                                                      |
| `parent_name`      | VARCHAR(255)       | NULLABLE                                      | اسم ولي الأمر                                                        |
| `parent_phone`     | VARCHAR(255)       | NULLABLE                                      | رقم هاتف ولي الأمر                                                   |
| `created_at`       | TIMESTAMP          | NULLABLE                                      | تاريخ ووقت إنشاء السجل                                               |
| `updated_at`       | TIMESTAMP          | NULLABLE                                      | تاريخ ووقت آخر تحديث للسجل                                           |

**العلاقات:**
*   `students` `belongsTo` `users`.
*   `students` `hasMany` `enrollments`.
*   `students` `hasMany` `submissions`.
*   `students` `hasMany` `grades`.

### 4.3. جدول `teachers` (المعلمون)

| الحقل              | النوع             | القيود                                       | الوصف                                                                |
| :----------------- | :----------------- | :-------------------------------------------- | :------------------------------------------------------------------- |
| `id`               | BIGINT UNSIGNED    | PK, AUTO_INCREMENT                            | معرف فريد للمعلم                                                     |
| `user_id`          | BIGINT UNSIGNED    | NOT NULL, UNIQUE, FK (`users.id`)             | معرف المستخدم المرتبط بهذا المعلم                                   |
| `employee_id_number`| VARCHAR(255)       | NULLABLE, UNIQUE                              | رقم تعريف الموظف/المعلم (خاص بالمستأجر)                             |
| `specialization`   | VARCHAR(255)       | NULLABLE                                      | تخصص المعلم                                                          |
| `bio`              | TEXT               | NULLABLE                                      | نبذة عن المعلم                                                       |
| `created_at`       | TIMESTAMP          | NULLABLE                                      | تاريخ ووقت إنشاء السجل                                               |
| `updated_at`       | TIMESTAMP          | NULLABLE                                      | تاريخ ووقت آخر تحديث للسجل                                           |

**العلاقات:**
*   `teachers` `belongsTo` `users`.
*   `teachers` `hasMany` `courses`.
*   `teachers` `hasMany` `classes`.
*   `teachers` `hasMany` `assignments`.

### 4.4. جدول `courses` (المقررات/المواد)

| الحقل              | النوع             | القيود                                       | الوصف                                                                |
| :----------------- | :----------------- | :-------------------------------------------- | :------------------------------------------------------------------- |
| `id`               | BIGINT UNSIGNED    | PK, AUTO_INCREMENT                            | معرف فريد للمقرر                                                     |
| `name`             | VARCHAR(255)       | NOT NULL                                      | اسم المقرر                                                           |
| `code`             | VARCHAR(255)       | NOT NULL, UNIQUE                              | رمز المقرر (مثال: MATH101)                                           |
| `description`      | TEXT               | NULLABLE                                      | وصف المقرر                                                           |
| `teacher_id`       | BIGINT UNSIGNED    | NULLABLE, FK (`teachers.id`)                  | المعلم المسؤول عن المقرر (يمكن أن يكون هناك عدة معلمين، ولكن هذا هو الرئيسي) |
| `status`           | ENUM               | NOT NULL, ("active", "inactive", "archived") | حالة المقرر                                                         |
| `created_at`       | TIMESTAMP          | NULLABLE                                      | تاريخ ووقت إنشاء السجل                                               |
| `updated_at`       | TIMESTAMP          | NULLABLE                                      | تاريخ ووقت آخر تحديث للسجل                                           |

**العلاقات:**
*   `courses` `belongsTo` `teachers`.
*   `courses` `hasMany` `classes`.
*   `courses` `hasMany` `grades`.

### 4.5. جدول `classes` (الفصول/المجموعات)

| الحقل              | النوع             | القيود                                       | الوصف                                                                |
| :----------------- | :----------------- | :-------------------------------------------- | :------------------------------------------------------------------- |
| `id`               | BIGINT UNSIGNED    | PK, AUTO_INCREMENT                            | معرف فريد للفصل                                                      |
| `name`             | VARCHAR(255)       | NOT NULL                                      | اسم الفصل (مثال: "الصف الأول أ")                                   |
| `description`      | TEXT               | NULLABLE                                      | وصف الفصل                                                           |
| `course_id`        | BIGINT UNSIGNED    | NOT NULL, FK (`courses.id`)                   | المقرر الذي ينتمي إليه هذا الفصل                                    |
| `teacher_id`       | BIGINT UNSIGNED    | NOT NULL, FK (`teachers.id`)                  | المعلم المسؤول عن هذا الفصل                                         |
| `start_date`       | DATE               | NULLABLE                                      | تاريخ بدء الفصل                                                      |
| `end_date`         | DATE               | NULLABLE                                      | تاريخ انتهاء الفصل                                                   |
| `schedule`         | JSON               | NULLABLE                                      | جدول الحصص/المواعيد (مثال: أيام الأسبوع، الأوقات)                   |
| `created_at`       | TIMESTAMP          | NULLABLE                                      | تاريخ ووقت إنشاء السجل                                               |
| `updated_at`       | TIMESTAMP          | NULLABLE                                      | تاريخ ووقت آخر تحديث للسجل                                           |

**العلاقات:**
*   `classes` `belongsTo` `courses`.
*   `classes` `belongsTo` `teachers`.
*   `classes` `hasMany` `enrollments`.
*   `classes` `hasMany` `assignments`.

### 4.6. جدول `enrollments` (التسجيلات)

| الحقل              | النوع             | القيود                                       | الوصف                                                                |
| :----------------- | :----------------- | :-------------------------------------------- | :------------------------------------------------------------------- |
| `id`               | BIGINT UNSIGNED    | PK, AUTO_INCREMENT                            | معرف فريد للتسجيل                                                    |
| `student_id`       | BIGINT UNSIGNED    | NOT NULL, FK (`students.id`)                  | معرف الطالب المسجل                                                   |
| `class_id`         | BIGINT UNSIGNED    | NOT NULL, FK (`classes.id`)                   | معرف الفصل الذي تم التسجيل فيه                                      |
| `enrollment_date`  | DATE               | NOT NULL                                      | تاريخ التسجيل                                                        |
| `status`           | ENUM               | NOT NULL, ("active", "completed", "dropped") | حالة التسجيل                                                         |
| `created_at`       | TIMESTAMP          | NULLABLE                                      | تاريخ ووقت إنشاء السجل                                               |
| `updated_at`       | TIMESTAMP          | NULLABLE                                      | تاريخ ووقت آخر تحديث للسجل                                           |

**العلاقات:**
*   `enrollments` `belongsTo` `students`.
*   `enrollments` `belongsTo` `classes`.

### 4.7. جدول `assignments` (الواجبات)

| الحقل              | النوع             | القيود                                       | الوصف                                                                |
| :----------------- | :----------------- | :-------------------------------------------- | :------------------------------------------------------------------- |
| `id`               | BIGINT UNSIGNED    | PK, AUTO_INCREMENT                            | معرف فريد للواجب                                                     |
| `title`            | VARCHAR(255)       | NOT NULL                                      | عنوان الواجب                                                         |
| `description`      | TEXT               | NULLABLE                                      | وصف الواجب                                                           |
| `due_date`         | DATETIME           | NOT NULL                                      | تاريخ ووقت استحقاق الواجب                                           |
| `class_id`         | BIGINT UNSIGNED    | NOT NULL, FK (`classes.id`)                   | الفصل الذي ينتمي إليه الواجب                                        |
| `teacher_id`       | BIGINT UNSIGNED    | NOT NULL, FK (`teachers.id`)                  | المعلم الذي قام بتعيين الواجب                                       |
| `max_grade`        | DECIMAL(5, 2)      | NOT NULL, DEFAULT 100.00                      | أقصى درجة يمكن الحصول عليها في الواجب                               |
| `created_at`       | TIMESTAMP          | NULLABLE                                      | تاريخ ووقت إنشاء السجل                                               |
| `updated_at`       | TIMESTAMP          | NULLABLE                                      | تاريخ ووقت آخر تحديث للسجل                                           |

**العلاقات:**
*   `assignments` `belongsTo` `classes`.
*   `assignments` `belongsTo` `teachers`.
*   `assignments` `hasMany` `submissions`.
*   `assignments` `hasMany` `grades`.

### 4.8. جدول `submissions` (تسليمات الواجبات)

| الحقل              | النوع             | القيود                                       | الوصف                                                                |
| :----------------- | :----------------- | :-------------------------------------------- | :------------------------------------------------------------------- |
| `id`               | BIGINT UNSIGNED    | PK, AUTO_INCREMENT                            | معرف فريد لتسليم الواجب                                              |
| `assignment_id`    | BIGINT UNSIGNED    | NOT NULL, FK (`assignments.id`)               | الواجب الذي تم تسليمه                                               |
| `student_id`       | BIGINT UNSIGNED    | NOT NULL, FK (`students.id`)                  | الطالب الذي قام بالتسليم                                            |
| `submission_date`  | DATETIME           | NOT NULL                                      | تاريخ ووقت التسليم                                                  |
| `file_path`        | VARCHAR(255)       | NULLABLE                                      | مسار الملف الذي تم تسليمه (إذا كان هناك ملف)                        |
| `content`          | TEXT               | NULLABLE                                      | محتوى التسليم (إذا كان نصيًا)                                       |
| `grade`            | DECIMAL(5, 2)      | NULLABLE                                      | الدرجة التي حصل عليها الطالب في هذا التسليم                         |
| `feedback`         | TEXT               | NULLABLE                                      | ملاحظات المعلم على التسليم                                          |
| `created_at`       | TIMESTAMP          | NULLABLE                                      | تاريخ ووقت إنشاء السجل                                               |
| `updated_at`       | TIMESTAMP          | NULLABLE                                      | تاريخ ووقت آخر تحديث للسجل                                           |

**العلاقات:**
*   `submissions` `belongsTo` `assignments`.
*   `submissions` `belongsTo` `students`.

### 4.9. جدول `grades` (الدرجات)

| الحقل              | النوع             | القيود                                       | الوصف                                                                |
| :----------------- | :----------------- | :-------------------------------------------- | :------------------------------------------------------------------- |
| `id`               | BIGINT UNSIGNED    | PK, AUTO_INCREMENT                            | معرف فريد للدرجة                                                     |
| `student_id`       | BIGINT UNSIGNED    | NOT NULL, FK (`students.id`)                  | الطالب الذي حصل على الدرجة                                          |
| `course_id`        | BIGINT UNSIGNED    | NULLABLE, FK (`courses.id`)                   | المقرر الذي تتعلق به الدرجة (إذا كانت درجة مقرر)                    |
| `assignment_id`    | BIGINT UNSIGNED    | NULLABLE, FK (`assignments.id`)               | الواجب الذي تتعلق به الدرجة (إذا كانت درجة واجب)                    |
| `grade`            | DECIMAL(5, 2)      | NOT NULL                                      | الدرجة المحصل عليها                                                  |
| `comments`         | TEXT               | NULLABLE                                      | تعليقات إضافية على الدرجة                                           |
| `graded_by`        | BIGINT UNSIGNED    | NULLABLE, FK (`teachers.id`)                  | المعلم الذي قام برصد الدرجة                                         |
| `created_at`       | TIMESTAMP          | NULLABLE                                      | تاريخ ووقت إنشاء السجل                                               |
| `updated_at`       | TIMESTAMP          | NULLABLE                                      | تاريخ ووقت آخر تحديث للسجل                                           |

**العلاقات:**
*   `grades` `belongsTo` `students`.
*   `grades` `belongsTo` `courses`.
*   `grades` `belongsTo` `assignments`.
*   `grades` `belongsTo` `teachers` (عبر `graded_by`).

### 4.10. جدول `announcements` (الإعلانات)

| الحقل              | النوع             | القيود                                       | الوصف                                                                |
| :----------------- | :----------------- | :-------------------------------------------- | :------------------------------------------------------------------- |
| `id`               | BIGINT UNSIGNED    | PK, AUTO_INCREMENT                            | معرف فريد للإعلان                                                    |
| `title`            | VARCHAR(255)       | NOT NULL                                      | عنوان الإعلان                                                        |
| `content`          | TEXT               | NOT NULL                                      | محتوى الإعلان                                                        |
| `created_by`       | BIGINT UNSIGNED    | NOT NULL, FK (`users.id`)                     | المستخدم الذي أنشأ الإعلان (يمكن أن يكون معلمًا أو إداريًا)         |
| `audience_type`    | ENUM               | NULLABLE, ("all", "students", "teachers", "class") | نوع الجمهور المستهدف للإعلان                                        |
| `audience_id`      | BIGINT UNSIGNED    | NULLABLE                                      | معرف الجمهور المستهدف (مثال: `class_id` إذا كان `audience_type` هو 'class') |
| `published_at`     | DATETIME           | NULLABLE                                      | تاريخ ووقت نشر الإعلان                                              |
| `created_at`       | TIMESTAMP          | NULLABLE                                      | تاريخ ووقت إنشاء السجل                                               |
| `updated_at`       | TIMESTAMP          | NULLABLE                                      | تاريخ ووقت آخر تحديث للسجل                                           |

**العلاقات:**
*   `announcements` `belongsTo` `users` (عبر `created_by`).

### 4.11. جدول `events` (الأحداث/الفعاليات)

| الحقل              | النوع             | القيود                                       | الوصف                                                                |
| :----------------- | :----------------- | :-------------------------------------------- | :------------------------------------------------------------------- |
| `id`               | BIGINT UNSIGNED    | PK, AUTO_INCREMENT                            | معرف فريد للحدث                                                      |
| `title`            | VARCHAR(255)       | NOT NULL                                      | عنوان الحدث                                                          |
| `description`      | TEXT               | NULLABLE                                      | وصف الحدث                                                           |
| `start_date`       | DATETIME           | NOT NULL                                      | تاريخ ووقت بدء الحدث                                                |
| `end_date`         | DATETIME           | NULLABLE                                      | تاريخ ووقت انتهاء الحدث                                             |
| `location`         | VARCHAR(255)       | NULLABLE                                      | مكان الحدث                                                           |
| `created_by`       | BIGINT UNSIGNED    | NOT NULL, FK (`users.id`)                     | المستخدم الذي أنشأ الحدث                                            |
| `created_at`       | TIMESTAMP          | NULLABLE                                      | تاريخ ووقت إنشاء السجل                                               |
| `updated_at`       | TIMESTAMP          | NULLABLE                                      | تاريخ ووقت آخر تحديث للسجل                                           |

**العلاقات:**
*   `events` `belongsTo` `users` (عبر `created_by`).

## 5. اعتبارات التنفيذ في Laravel

لتحقيق هيكلية قاعدة البيانات هذه في Laravel، يجب مراعاة ما يلي:

1.  **حزم Multi-Tenancy:** استخدام حزم مثل `spatie/laravel-multitenancy` أو `stancl/tenancy` سيسهل بشكل كبير إدارة تبديل اتصالات قواعد البيانات، وتوفير قواعد بيانات جديدة للمستأجرين، وتشغيل الهجرات (migrations) الخاصة بالمستأجرين.
2.  **الهجرات (Migrations):**
    *   يجب أن تكون هناك هجرات منفصلة لقاعدة البيانات المركزية.
    *   يجب أن تكون هناك هجرات خاصة بقواعد بيانات المستأجرين، والتي سيتم تشغيلها تلقائيًا عند إنشاء مستأجر جديد.
3.  **الموديلات (Models):** تعريف الموديلات والعلاقات (Eloquent Relationships) لكل جدول في كلتا القاعدتين.
4.  **المتحكمات (Controllers) والـ Middleware:** استخدام Middleware لتبديل اتصال قاعدة البيانات إلى قاعدة بيانات المستأجر الصحيحة بناءً على النطاق الفرعي (subdomain) أو `current_tenant_id` للمستخدم المسجل أو أي معرف آخر للمستأجر في الطلب (request).
5.  **الأمان:** تشفير كلمات مرور قواعد البيانات الخاصة بالمستأجرين في جدول `tenants`، وتطبيق أفضل ممارسات الأمان في Laravel.
6.  **توفير قواعد البيانات:** يجب أن تكون هناك آلية لإنشاء قواعد بيانات MySQL/PostgreSQL جديدة ديناميكيًا عند تسجيل مستأجر جديد، وتعيين المستخدمين وكلمات المرور لها.

## 6. ترتيب التنفيذ المقترح (Roadmap)

لضمان تنفيذ سلس، يوصى بالترتيب التالي:

1.  **إعداد بيئة Laravel:** إنشاء مشروع Laravel جديد.
2.  **تصميم قاعدة البيانات المركزية:**
    *   إنشاء هجرات (migrations) لجداول `users`, `subscriptions`, `tenants`, `tenant_user` في قاعدة البيانات المركزية.
    *   تشغيل الهجرات.
    *   تعريف الموديلات والعلاقات لهذه الجداول.
3.  **تكامل حزمة Multi-Tenancy:** تثبيت وتكوين حزمة Multi-Tenancy (مثل `stancl/tenancy`).
4.  **تكوين توفير المستأجرين:** إعداد المنطق لإنشاء قاعدة بيانات جديدة للمستأجر، وإنشاء مستخدم قاعدة بيانات، وتشغيل هجرات المستأجر عند تسجيل مستأجر جديد.
5.  **تصميم قواعد بيانات المستأجرين:**
    *   إنشاء هجرات (migrations) لجميع جداول المستأجرين (`users`, `students`, `teachers`, `courses`, `classes`, `enrollments`, `assignments`, `submissions`, `grades`, `announcements`, `events`).
    *   تعريف الموديلات والعلاقات لهذه الجداول.
6.  **تطبيق الـ Middleware:** تطوير Middleware لتبديل اتصال قاعدة البيانات بناءً على المستأجر النشط.
7.  **تطوير واجهات المستخدم (UI) والـ API:** البدء في بناء واجهات المستخدم ولوحات التحكم للمسؤولين العامين والمستأجرين، بالإضافة إلى واجهات برمجة التطبيقات اللازمة.
8.  **اختبار شامل:** اختبار جميع جوانب النظام، بما في ذلك إنشاء المستأجرين، تسجيل الدخول، عزل البيانات، والأداء.

---
