# 📚 Learning Platform - Project Plan

## 🎯 Project Overview
منصة تعليمية تحتوي على:
- دورات تعليمية (Courses)
- أقسام/تصنيفات (Categories)
- اشتراكات للدورات (Subscriptions/Plans)
- نظام شراء وإدارة الاشتراكات (Purchases/Enrollments)
- إدارة المستخدمين والصلاحيات (Users & Roles)

---

## 📋 Development Phases

### Phase 1: Foundation & Core Setup ✅
**الهدف:** تجهيز البنية التحتية الأساسية

#### Tasks:
- [x] Setup Architecture Documentation
- [x] Define Coding Standards
- [x] Setup Error Handling Strategy
- [ ] Create Base Classes (BaseController, BaseService, BaseRepository)
- [ ] Create Helper Functions
- [ ] Setup Global Exception Handler
- [ ] Create Common Traits

**المدة المتوقعة:** 1-2 أيام

---

### Phase 2: User Management & Authentication 🔐
**الهدف:** نظام إدارة المستخدمين والصلاحيات

#### Tasks:
- [ ] User CRUD Operations
  - [ ] Model + Migration
  - [ ] Interface + Repository
  - [ ] Service
  - [ ] Resource
  - [ ] Controller
  - [ ] Routes

- [ ] Role & Permission System
  - [ ] Role Model (already exists)
  - [ ] Permission Model (already exists)
  - [ ] RoleRepository + Interface
  - [ ] PermissionRepository + Interface
  - [ ] RoleService
  - [ ] PermissionService
  - [ ] Assign roles to users

- [ ] Authentication
  - [ ] Login/Logout
  - [ ] Register
  - [ ] Password Reset
  - [ ] Email Verification

**المدة المتوقعة:** 3-4 أيام

---

### Phase 3: Categories Management 📂
**الهدف:** إدارة أقسام وتصنيفات الدورات

#### Tasks:
- [ ] Category Model
  - [ ] Migration (id, name, slug, description, parent_id, status, order)
  - [ ] Model with relationships (parent, children, courses)
  - [ ] Accessors & Mutators (slug auto-generation)
  - [ ] Scopes (active, root, children)

- [ ] Category Repository
  - [ ] Interface
  - [ ] Repository Implementation
  - [ ] Methods: CRUD, getTree, getBySlug, getWithCourses

- [ ] Category Service
  - [ ] Business logic for category management
  - [ ] Nested categories handling
  - [ ] Slug uniqueness validation

- [ ] Category API
  - [ ] CategoryResource
  - [ ] CategoryController
  - [ ] Routes (list, show, create, update, delete)

**المدة المتوقعة:** 2-3 أيام

---

### Phase 4: Courses Management 📖
**الهدف:** إدارة الدورات التعليمية

#### Tasks:
- [ ] Course Model
  - [ ] Migration (id, title, slug, description, instructor_id, category_id, level, duration, thumbnail, status, price, is_free)
  - [ ] Model with relationships (instructor, category, subscriptions, enrollments, lessons)
  - [ ] Accessors & Mutators
  - [ ] Scopes (active, free, paid, byCategory, byInstructor)

- [ ] Course Repository
  - [ ] Interface
  - [ ] Repository Implementation
  - [ ] Methods: CRUD, search, filter, getByCategory, getFeatured

- [ ] Course Service
  - [ ] Business logic for course management
  - [ ] Course publishing workflow
  - [ ] Price validation

- [ ] Course API
  - [ ] CourseResource (with nested category, instructor)
  - [ ] CourseController
  - [ ] Routes (list, show, create, update, delete, publish)

**المدة المتوقعة:** 3-4 أيام

---

### Phase 5: Lessons & Content Management 📝
**الهدف:** إدارة محتوى الدورات (دروس، فيديوهات، ملفات)

#### Tasks:
- [ ] Lesson Model
  - [ ] Migration (id, course_id, title, content, video_url, order, duration, is_free)
  - [ ] Model with relationships (course, attachments)
  - [ ] Scopes (byCourse, free, ordered)

- [ ] Lesson Repository + Service
- [ ] Lesson API (CRUD)
- [ ] File Upload handling for videos/attachments

**المدة المتوقعة:** 2-3 أيام

---

### Phase 6: Subscription Plans 💳
**الهدف:** إدارة خطط الاشتراك للدورات

#### Tasks:
- [ ] Subscription Plan Model
  - [ ] Migration (id, course_id, name, description, price, duration_days, features)
  - [ ] Model with relationships (course, purchases)
  - [ ] Scopes (active, byCourse)

- [ ] Subscription Repository + Interface
- [ ] Subscription Service
  - [ ] Plan creation and management
  - [ ] Price validation
  - [ ] Feature management

- [ ] Subscription API
  - [ ] SubscriptionResource
  - [ ] SubscriptionController
  - [ ] Routes

**المدة المتوقعة:** 2 أيام

---

### Phase 7: Purchase & Enrollment System 🛒
**الهدف:** نظام شراء الاشتراكات والتسجيل في الدورات

#### Tasks:
- [ ] Purchase/Order Model
  - [ ] Migration (id, user_id, subscription_id, amount, status, payment_method, transaction_id)
  - [ ] Model with relationships (user, subscription)
  - [ ] Scopes (completed, pending, byUser)

- [ ] Enrollment Model
  - [ ] Migration (id, user_id, course_id, purchase_id, enrolled_at, expires_at, status)
  - [ ] Model with relationships (user, course, purchase)
  - [ ] Scopes (active, expired, byCourse, byUser)

- [ ] Purchase Repository + Interface
- [ ] Enrollment Repository + Interface

- [ ] Purchase Service
  - [ ] Create purchase
  - [ ] Process payment
  - [ ] Create enrollment after successful payment
  - [ ] Handle expiration

- [ ] Enrollment Service
  - [ ] Check user access to course
  - [ ] Extend enrollment
  - [ ] Cancel enrollment

- [ ] Purchase API
  - [ ] PurchaseResource
  - [ ] PurchaseController
  - [ ] Routes (create, show, list user purchases)

- [ ] Enrollment API
  - [ ] EnrollmentResource
  - [ ] EnrollmentController
  - [ ] Routes (my-courses, check-access)

**المدة المتوقعة:** 4-5 أيام

---

### Phase 8: Payment Integration 💰
**الهدف:** ربط بوابات الدفع (Stripe, PayPal, etc.)

#### Tasks:
- [ ] Payment Gateway Interface
- [ ] Payment Service
- [ ] Webhook handling
- [ ] Payment confirmation
- [ ] Refund handling

**المدة المتوقعة:** 3-4 أيام

---

### Phase 9: Student Progress Tracking 📊
**الهدف:** متابعة تقدم الطالب في الدورة

#### Tasks:
- [ ] Progress Model (user_id, lesson_id, completed, watched_duration)
- [ ] Progress Repository + Service
- [ ] Progress API
- [ ] Certificate generation on completion

**المدة المتوقعة:** 2-3 أيام

---

### Phase 10: Reviews & Ratings ⭐
**الهدف:** تقييمات ومراجعات الدورات

#### Tasks:
- [ ] Review Model
- [ ] Review Repository + Service
- [ ] Review API
- [ ] Calculate average ratings

**المدة المتوقعة:** 2 أيام

---

### Phase 11: Admin Dashboard 👨‍💼
**الهدف:** لوحة تحكم للإدارة

#### Tasks:
- [ ] Statistics & Analytics
- [ ] Reports (sales, enrollments, popular courses)
- [ ] User management
- [ ] Course approval workflow

**المدة المتوقعة:** 3-4 أيام

---

### Phase 12: Notifications & Emails 📧
**الهدف:** نظام الإشعارات والبريد الإلكتروني

#### Tasks:
- [ ] Email notifications (purchase confirmation, enrollment, etc.)
- [ ] In-app notifications
- [ ] Notification preferences

**المدة المتوقعة:** 2-3 أيام

---

## 🚀 Current Phase: Phase 1 - Foundation

### ✅ What's Ready:
- Architecture documentation
- Error handling strategy
- Existing models: User, Role, Permission

### 🔨 What We Need to Build Now:

#### 1. Base Classes
```
app/Http/Controllers/BaseController.php
app/Services/BaseService.php
app/Repositories/BaseRepository.php
```

#### 2. Global Exception Handler
```
app/Exceptions/Handler.php (update existing)
app/Exceptions/CustomExceptions/ (folder for custom exceptions)
```

#### 3. Helper Functions
```
app/Helpers/ResponseHelper.php
app/Helpers/SlugHelper.php
```

#### 4. Common Traits
```
app/Traits/HasSlug.php
app/Traits/Searchable.php
```

---

## 📌 Next Steps

**قولي عايز نبدأ بإيه:**

1. **نكمل Phase 1** (Base Classes + Helpers + Exception Handler)
2. **نبدأ Phase 2** (User Management)
3. **نروح على Phase 3** (Categories)
4. **نبدأ Phase 4** (Courses)
5. **أو حاجة تانية محددة**

---

## 📊 Database Schema Overview

```
users
├── roles (many-to-many)
└── permissions (many-to-many through roles)

categories
├── parent_category (self-referencing)
├── children_categories
└── courses

courses
├── instructor (user)
├── category
├── subscription_plans
├── enrollments
└── lessons

subscription_plans
├── course
└── purchases

purchases
├── user
├── subscription_plan
└── enrollment

enrollments
├── user
├── course
└── purchase

lessons
├── course
└── progress_records

progress
├── user
└── lesson

reviews
├── user
└── course
```

---

*Ready to start building! 🚀*
