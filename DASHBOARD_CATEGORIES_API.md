# 📊 Dashboard Categories API (Admin Only)

**Base URL:** `/api/dashboard/api/categories`

**Authentication:** Bearer Token (Sanctum) + Admin Role Required

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json (for POST/PUT)
```

---

## 🔗 Endpoints

### 1. List All Categories
**GET** `/api/dashboard/api/categories`

**Query Parameters:**
- `search` (optional) - Search in name or description
- `status` (optional) - Filter by status: `active` or `inactive`
- `parent_id` (optional) - Filter by parent (use `0` for root categories)
- `is_parent` (optional) - Boolean: `true` for parent only, `false` for children only

**Example:**
```
GET /api/dashboard/api/categories?status=active&parent_id=0
```

**Response (200):**
```json
{
    "success": true,
    "message": "Categories retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "Programming",
            "slug": "programming",
            "description": "Programming courses",
            "image": "https://ui-avatars.com/api/?name=Programming&...",
            "parent_id": null,
            "parent": null,
            "children": [],
            "children_count": 0,
            "courses_count": 0,
            "order": 1,
            "status": "active",
            "is_parent": true,
            "has_children": false,
            "created_at": "2026-03-26 15:22:24",
            "updated_at": "2026-03-26 15:22:24"
        }
    ],
    "errors": []
}
```

---

### 2. Get Single Category
**GET** `/api/dashboard/api/categories/{id}`

**Response (200):**
```json
{
    "success": true,
    "message": "Category retrieved successfully",
    "data": {
        "id": 1,
        "name": "Programming",
        ...
    },
    "errors": []
}
```

**Error Responses:**
- **404** - Category not found

---

### 3. Create Category
**POST** `/api/dashboard/api/categories`

**Headers (for file upload):**
```
Content-Type: multipart/form-data
```

**Form Fields:**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| name | string | ✅ | Category name (max 255) |
| slug | string | ❌ | Auto-generated from name if empty (max 255, unique) |
| description | string | ❌ | Category description |
| image | file | ❌ | Image (jpeg, png, jpg, gif, max 2MB) |
| parent_id | integer | ❌ | Parent category ID (null for root) |
| order | integer | ❌ | Display order (default: 0) |
| status | string | ❌ | `active` or `inactive` (default: `active`) |

**Example (multipart/form-data):**
```
POST /api/dashboard/api/categories
name: Programming
description: Programming courses
parent_id: (leave empty for root)
order: 1
status: active
image: [file upload]
```

**Response (201):**
```json
{
    "success": true,
    "message": "Category created successfully",
    "data": {
        "id": 1,
        "name": "Programming",
        ...
    },
    "errors": []
}
```

**Validation Errors (422):**
```json
{
    "success": false,
    "message": "Validation failed",
    "data": null,
    "errors": {
        "name": ["The name field is required."],
        "image": ["The image must be a file of type: jpeg, png, jpg, gif."]
    }
}
```

---

### 4. Update Category
**PUT** `/api/dashboard/api/categories/{id}`

**Headers:**
```
Content-Type: multipart/form-data (if uploading image)
```

**Fields:** Same as create

**Example:**
```bash
curl -X PUT http://localhost/api/dashboard/api/categories/1 \
  -F "name=Updated Name" \
  -F "description=Updated description" \
  -F "status=active"
```

**Response (200):**
```json
{
    "success": true,
    "message": "Category updated successfully",
    "data": { ... },
    "errors": []
}
```

---

### 5. Delete Category
**DELETE** `/api/dashboard/api/categories/{id}`

**Response (200):**
```json
{
    "success": true,
    "message": "Category deleted successfully",
    "data": null,
    "errors": []
}
```

**Error Responses:**
- **400** - Category has associated courses (must reassign/delete courses first)
- **404** - Category not found

**Note:** Deleting a parent category will cascade delete all its children.

---

### 6. Get Parent Categories
**GET** `/api/dashboard/api/categories/parents/list`

**Response (200):**
```json
{
    "success": true,
    "message": "Parent categories retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "Programming",
            "slug": "programming",
            ...
        }
    ],
    "errors": []
}
```

---

### 7. Get Category Tree
**GET** `/api/dashboard/api/categories/tree`

**Description:** Returns all parent categories with their children nested.

**Response (200):**
```json
{
    "success": true,
    "message": "Category tree retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "Programming",
            "children": [
                {
                    "id": 2,
                    "name": "Web Development",
                    "parent_id": 1,
                    ...
                }
            ]
        }
    ],
    "errors": []
}
```

---

### 8. Reorder Categories
**POST** `/api/dashboard/api/categories/reorder`

**Request Body (JSON):**
```json
{
    "order": [
        {"id": 1, "order": 1},
        {"id": 2, "order": 2},
        {"id": 3, "order": 3}
    ]
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Categories reordered successfully",
    "data": null,
    "errors": []
}
```

---

## 🔐 Authorization

All endpoints require:
1. **Authentication** - Valid Sanctum token in `Authorization: Bearer {token}`
2. **Admin Role** - User must have `admin` role

### Getting Admin Token:

```bash
# Login as admin
curl -X POST http://localhost/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@elghad.com",
    "password": "Admin@123"
  }'

# Response includes token:
# "token": "1|abc123..."
```

Then use:
```bash
curl http://localhost/api/dashboard/api/categories \
  -H "Authorization: Bearer 1|abc123..."
```

---

## 🛢️ Database Schema

**categories table:**
- `id` (bigint, PK)
- `name` (varchar 255)
- `slug` (varchar 255, unique)
- `description` (text, nullable)
- `image` (varchar 255, nullable)
- `parent_id` (bigint, nullable, self-referencing)
- `order` (integer, default 0)
- `status` (enum: active, inactive)
- `created_at`, `updated_at`

---

## 🧪 Testing with cURL

### Get all categories (admin):
```bash
curl -X GET "http://localhost/api/dashboard/api/categories" \
  -H "Authorization: Bearer {admin_token}"
```

### Create category (admin):
```bash
curl -X POST http://localhost/api/dashboard/api/categories \
  -H "Authorization: Bearer {admin_token}" \
  -F "name=New Category" \
  -F "description=Description here" \
  -F "status=active" \
  -F "order=1"
```

### Update category:
```bash
curl -X PUT http://localhost/api/dashboard/api/categories/1 \
  -H "Authorization: Bearer {admin_token}" \
  -F "name=Updated Name" \
  -F "status=active"
```

### Delete category:
```bash
curl -X DELETE "http://localhost/api/dashboard/api/categories/1" \
  -H "Authorization: Bearer {admin_token}"
```

---

## 📝 Notes

- All admin endpoints are **rate-limited** (60 requests per minute)
- CSRF protection is **disabled** for API routes (using Sanctum tokens instead)
- Image uploads are stored in `storage/app/public/categories` (symbolic link: `public/storage/categories`)
- Slug is auto-generated from name if not provided
- Parent-child hierarchy supported (nested categories)
- Cannot delete categories with associated courses (business rule)
- Children are cascade deleted when parent is deleted
- Default ordering by `order` field (ascending)

---

## 🔒 Security

- ✅ **Authentication required** (Sanctum tokens)
- ✅ **Role-based access** (only admin users)
- ✅ **Validation** on all inputs
- ✅ **File type restrictions** for images
- ✅ **Rate limiting** (throttle middleware)
- ✅ **Proper error handling** (global exception handler)

---

## 🎯 Default Admin Credentials

After `php artisan db:seed`:

- **Email:** admin@elghad.com
- **Password:** Admin@123
- **Role:** admin

---

## 📦 Related Files

**Controller:**
- `app/Http/Controllers/Dashboard/Api/CategoryController.php`

**Middleware:**
- `app/Http/Middleware/CheckRole.php`

**Routes:**
- `routes/api.php` (Dashboard API group)

**Service:**
- `app/Services/CategoryService.php`

**Resource:**
- `app/Http/Resources/CategoryResource.php`

---

*Ready for admin use! 🚀*
