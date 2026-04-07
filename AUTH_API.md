# 🔐 Authentication API Documentation

## Base URL
```
/api/auth
```

---

## 🔗 Endpoints

### 1. Register User
**POST** `/api/auth/register`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Request Body (JSON):**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "type": "student"
}
```

**Field Validation:**
- `name`: required | string | max:255
- `email`: required | email | max:255 | unique:users,email
- `password`: required | string | min:8 | confirmed
- `type` (optional): in:student,instructor,admin (default: student)

**Success Response (201):**
```json
{
    "success": true,
    "message": "Registration successful",
    "data": {
        "user": {
            "id": 1,
            "uuid": "550e8400-e29b-41d4-a716-446655440000",
            "name": "John Doe",
            "email": "john@example.com",
            "email_verified_at": null,
            "is_verified": false,
            "type": "student",
            "avatar_url": null,
            "roles": ["student"],
            "permissions": [],
            "created_at": "2026-03-28 12:00:00",
            "updated_at": "2026-03-28 12:00:00"
        },
        "token": "1|aBcDeFgHiJkLmNoPqRsTuVwXyZ",
        "token_type": "Bearer",
        "requires_verification": true
    },
    "errors": []
}
```

**Error Response (400):**
```json
{
    "success": false,
    "message": "Email already registered",
    "data": null,
    "errors": []
}
```

---

### 2. Verify Email
**POST** `/api/auth/email/verify`

**Request Body (JSON):**
```json
{
    "email": "john@example.com",
    "code": "123456"
}
```

**Field Validation:**
- `email`: required | email
- `code`: required | string | size:6

**Success Response (200):**
```json
{
    "success": true,
    "message": "Email verified successfully",
    "data": {
        "id": 1,
        "uuid": "550e8400-e29b-41d4-a716-446655440000",
        "name": "John Doe",
        "email": "john@example.com",
        "email_verified_at": "2026-03-28 12:15:00",
        "is_verified": true,
        ...
    },
    "errors": []
}
```

**Error Responses:**
- **400** - Invalid or expired verification code
- **404** - User not found

---

### 3. Resend Verification Code
**POST** `/api/auth/email/resend`

**Request Body (JSON):**
```json
{
    "email": "john@example.com"
}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Verification code sent to your email",
    "data": null,
    "errors": []
}
```

**Error Responses:**
- **400** - Email not found or already verified

---

### 4. Check Verification Status
**GET** `/api/auth/email/check/{email}`

**Example:** `/api/auth/email/check/john@example.com`

**Success Response (200):**
```json
{
    "success": true,
    "message": "Verification status retrieved",
    "data": {
        "email": "john@example.com",
        "is_verified": false
    },
    "errors": []
}
```

---

### 5. Login
**POST** `/api/auth/login`

**Request Body (JSON):**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "email_verified_at": "2026-03-28 12:15:00",
            "is_verified": true,
            "type": "student",
            "roles": ["student"],
            "permissions": []
        },
        "token": "1|aBcDeFgHiJkLmNoPqRsTuVwXyZ",
        "token_type": "Bearer"
    },
    "errors": []
}
```

**Error Responses:**
- **401** - Invalid credentials
- **400** - Email not verified

---

### 6. Logout
**POST** `/api/auth/logout`

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Logout successful",
    "data": null,
    "errors": []
}
```

---

### 7. Get Profile
**GET** `/api/auth/me`

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "User retrieved",
    "data": {
        "id": 1,
        "uuid": "550e8400-e29b-41d4-a716-446655440000",
        "name": "John Doe",
        "email": "john@example.com",
        "email_verified_at": "2026-03-28 12:15:00",
        "is_verified": true,
        "type": "student",
        "avatar_url": "https://www.gravatar.com/avatar/...",
        "roles": ["student"],
        "permissions": [],
        "created_at": "2026-03-28 12:00:00",
        "updated_at": "2026-03-28 12:00:00"
    },
    "errors": []
}
```

---

### 8. Forgot Password
**POST** `/api/auth/password/forgot`

**Request Body (JSON):**
```json
{
    "email": "john@example.com"
}
```

**Description:** Sends a 6-digit password reset code to the email.

**Success Response (200):**
```json
{
    "success": true,
    "message": "Password reset code sent to your email",
    "data": null,
    "errors": []
}
```

**Note:** Returns success even if email doesn't exist (security best practice).

---

### 9. Reset Password
**POST** `/api/auth/password/reset`

**Request Body (JSON):**
```json
{
    "email": "john@example.com",
    "code": "123456",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

**Field Validation:**
- `email`: required | email
- `code`: required | string | size:6
- `password`: required | string | min:8 | confirmed

**Success Response (200):**
```json
{
    "success": true,
    "message": "Password reset successful",
    "data": null,
    "errors": []
}
```

**Error Responses:**
- **400** - Invalid or expired code
- **400** - User not found

---

## 📧 Email Templates

### 1. Email Verification (`resources/views/emails/verify-email.blade.php`)
- Contains 6-digit verification code
- 15-minute expiration
- Professional design

### 2. Password Reset Code (`resources/views/emails/password-reset-code.blade.php`)
- Contains 6-digit reset code
- 15-minute expiration
- Security warning

---

## 🔐 Authentication Flow

### Registration Flow:
1. **POST** `/api/auth/register` with user data
2. System creates user with `email_verified_at = null`
3. System assigns default role (student)
4. System generates 6-digit verification code
5. System sends email with code
6. System returns user data + API token (but user is not verified yet)
7. User calls **POST** `/api/auth/email/verify` with code
8. Email is marked as verified
9. User can now **POST** `/api/auth/login` successfully

### Login Flow:
1. User calls **POST** `/api/auth/login` with email & password
2. System validates credentials
3. System checks if `email_verified_at` is not null
4. If verified: returns user + new API token
5. If not verified: returns error (400)

### Password Reset Flow:
1. User calls **POST** `/api/auth/password/forgot` with email
2. System generates 6-digit reset code (valid 15 min)
3. System sends email with code
4. User calls **POST** `/api/auth/password/reset` with email, code, new password
5. System validates code
6. System updates password
7. System invalidates all existing tokens
8. Returns success

---

## 🔑 Token Usage

All protected endpoints require Sanctum token in Authorization header:

```
Authorization: Bearer {token}
```

---

## 🗄️ Database Tables

### users
- `id` (bigint, PK)
- `uuid` (uuid, unique)
- `name` (varchar 255)
- `email` (varchar 255, unique)
- `password` (varchar 255)
- `type` (enum: student,instructor,admin, default: student)
- `avatar` (varchar 255, nullable)
- `email_verified_at` (timestamp, nullable)
- `status` (enum: active,inactive, default: active)
- `remember_token` (varchar 100, nullable)
- `created_at`, `updated_at`

### email_verification_codes
- `id` (bigint, PK)
- `email` (varchar 255, indexed)
- `code` (varchar 6)
- `expires_at` (timestamp)
- `created_at`, `updated_at`

### password_reset_codes
- `id` (bigint, PK)
- `email` (varchar 255, indexed)
- `code` (varchar 6)
- `expires_at` (timestamp)
- `created_at`, `updated_at`

### roles
- `id` (bigint, PK)
- `name` (varchar 255, unique) - admin, instructor, student
- `description` (text, nullable)
- `created_at`, `updated_at`

### permissions
- `id` (bigint, PK)
- `name` (varchar 255, unique) - e.g., users.view, courses.create
- `description` (text, nullable)
- `created_at`, `updated_at`

### role_user (pivot)
- `role_id` (bigint, FK)
- `user_id` (bigint, FK)

### permission_role (pivot)
- `permission_id` (bigint, FK)
- `role_id` (bigint, FK)

### permission_user (pivot - optional direct permissions)
- `permission_id` (bigint, FK)
- `user_id` (bigint, FK)

---

## 🧪 Testing with cURL

### Register user:
```bash
curl -X POST http://localhost/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### Verify email:
```bash
curl -X POST http://localhost/api/auth/email/verify \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "code": "123456"
  }'
```

### Login:
```bash
curl -X POST http://localhost/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

### Get profile:
```bash
curl -X GET http://localhost/api/auth/me \
  -H "Authorization: Bearer {token}"
```

### Forgot password:
```bash
curl -X POST http://localhost/api/auth/password/forgot \
  -H "Content-Type: application/json" \
  -d '{"email": "john@example.com"}'
```

### Reset password:
```bash
curl -X POST http://localhost/api/auth/password/reset \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "code": "123456",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
  }'
```

---

## 🎯 Default Accounts

After running the database seeder:

### Admin User:
- **Email:** admin@elghad.com
- **Password:** Admin@123
- **Role:** admin (all permissions)

### Student User:
- **Email:** student@elghad.com
- **Password:** Student@123
- **Role:** student (view-only permissions)

---

## 📦 Required Packages

Make sure these are in `composer.json`:
- `laravel/sanctum` (already in your project)
- `laravel/ui` or Laravel Breeze/Fortify (optional)

Mail driver configured in `.env`:
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

---

## ⚠️ Security Notes

1. **Email enumeration protection**: Forgot password returns success even if email doesn't exist
2. **Code expiration**: All codes expire in 15 minutes
3. **Code format**: 6-digit numeric codes
4. **Token invalidation**: Password reset invalidates all existing tokens
5. **UUID**: Users have UUID for public identifier (optional use)
6. **Rate limiting**: Apply Laravel's `throttle` middleware if needed

---

## 📝 Implementation Checklist

- ✅ Models: User (with roles/permissions), EmailVerificationCode, PasswordResetCode
- ✅ Migrations: All tables created
- ✅ Repository: UserRepository with interface
- ✅ Service: AuthService with all business logic
- ✅ Controller: AuthController (API) with 9 endpoints
- ✅ Form Requests: 5 validation classes
- ✅ Resources: UserResource for output transformation
- ✅ Email Templates: 2 responsive templates
- ✅ Language Files: English & Arabic
- ✅ Routes: All API routes defined
- ✅ ServiceProvider: Repository bindings
- ✅ Database Seeder: Default roles, permissions, users
- ✅ Models: Role & Permission with relationships

---

## 🚀 Ready to Use!

Simply run:
```bash
php artisan migrate --seed
php artisan serve
```

And your authentication system will be fully operational! 🎉
