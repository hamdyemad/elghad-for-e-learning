# 📚 Categories API Documentation

## Base URL
```
/api/categories
```

---

## 🔗 Endpoints

### 1. Get All Categories
**GET** `/api/categories`

**Query Parameters:**
- `search` (optional) - البحث في الأقسام
- `parent_id` (optional) - جلب الأقسام الفرعية لقسم معين
- `tree` (optional) - جلب الأقسام في شكل شجري (parents with children)

**Response:**
```json
{
    "success": true,
    "message": "Categories retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "البرمجة",
            "slug": "programming",
            "description": "دورات البرمجة وتطوير البرمجيات",
            "image": "http://localhost/storage/categories/image.jpg",
            "parent_id": null,
            "parent": null,
            "children": [...],
            "children_count": 3,
            "order": 1,
            "status": "active",
            "is_parent": true,
            "has_children": true,
            "created_at": "2026-03-26 15:22:24",
            "updated_at": "2026-03-26 15:22:24"
        }
    ],
    "errors": []
}
```

---

### 2. Get Parent Categories Only
**GET** `/api/categories/parents`

**Response:**
```json
{
    "success": true,
    "message": "Parent categories retrieved successfully",
    "data": [...]
}
```

---

### 3. Get Single Category
**GET** `/api/categories/{id}`

**Response:**
```json
{
    "success": true,
    "message": "Category retrieved successfully",
    "data": {
        "id": 1,
        "name": "البرمجة",
        "slug": "programming",
        ...
    },
    "errors": []
}
```

---

### 4. Create Category
**POST** `/api/categories`

**Request Body:**
```json
{
    "name": "اسم القسم",
    "slug": "category-slug",
    "description": "وصف القسم",
    "parent_id": 1,
    "order": 0,
    "status": "active"
}
```

**Form Data (with image):**
```
name: اسم القسم
slug: category-slug (optional)
description: وصف القسم (optional)
image: [file] (optional)
parent_id: 1 (optional)
order: 0 (optional)
status: active (optional)
```

**Validation Rules:**
- `name`: required, string, max:255
- `slug`: nullable, string, max:255, unique
- `description`: nullable, string
- `image`: nullable, image, mimes:jpeg,png,jpg,gif, max:2048
- `parent_id`: nullable, exists:categories,id
- `order`: nullable, integer
- `status`: nullable, in:active,inactive

**Response:**
```json
{
    "success": true,
    "message": "Category created successfully",
    "data": {...},
    "errors": []
}
```

---

### 5. Update Category
**PUT** `/api/categories/{id}`

**Request Body:** Same as create

**Response:**
```json
{
    "success": true,
    "message": "Category updated successfully",
    "data": {...},
    "errors": []
}
```

---

### 6. Delete Category
**DELETE** `/api/categories/{id}`

**Response:**
```json
{
    "success": true,
    "message": "Category deleted successfully",
    "data": null,
    "errors": []
}
```

**Note:** Deleting a parent category will cascade delete all its children.

---

## 🔍 Search Examples

### Search by name or description:
```
GET /api/categories?search=برمجة
```

### Get subcategories of a parent:
```
GET /api/categories?parent_id=1
```

### Get category tree (parents with children):
```
GET /api/categories?tree=1
```

---

## ❌ Error Responses

### 404 - Not Found
```json
{
    "success": false,
    "message": "Resource not found",
    "data": null,
    "errors": ["The requested resource does not exist"]
}
```

### 422 - Validation Error
```json
{
    "success": false,
    "message": "Validation failed",
    "data": null,
    "errors": {
        "name": ["The name field is required."],
        "slug": ["The slug has already been taken."]
    }
}
```

### 409 - Conflict (Slug exists)
```json
{
    "success": false,
    "message": "Category with this slug already exists",
    "data": null,
    "errors": []
}
```

---

## 🧪 Testing with cURL

### Get all categories:
```bash
curl -X GET http://localhost/api/categories
```

### Create category:
```bash
curl -X POST http://localhost/api/categories \
  -H "Content-Type: application/json" \
  -d '{
    "name": "قسم جديد",
    "description": "وصف القسم",
    "status": "active"
  }'
```

### Create subcategory with image:
```bash
curl -X POST http://localhost/api/categories \
  -F "name=قسم فرعي" \
  -F "parent_id=1" \
  -F "image=@/path/to/image.jpg"
```

---

## 📊 Database Schema

```sql
categories
├── id (bigint, primary key)
├── name (varchar 255)
├── slug (varchar 255, unique)
├── description (text, nullable)
├── image (varchar 255, nullable)
├── parent_id (bigint, nullable, foreign key → categories.id)
├── order (integer, default 0)
├── status (enum: active, inactive, default active)
├── created_at (timestamp)
└── updated_at (timestamp)
```

---

## 🎯 Model Features

### Accessors:
- `image_url` - Returns full URL or default image
- `is_parent` - Boolean if category is parent
- `has_children` - Boolean if category has children

### Mutators:
- `name` - Auto capitalizes and trims
- `slug` - Auto generates from name if empty

### Scopes:
- `active()` - Get active categories only
- `inactive()` - Get inactive categories
- `parents()` - Get parent categories only
- `children()` - Get subcategories only
- `byParent($id)` - Get children of specific parent
- `search($term)` - Search in name and description
- `ordered()` - Order by 'order' field

---

## 🌐 Dashboard Routes

```
GET  /dashboard/categories          - List all categories
GET  /dashboard/categories/create   - Show create form
POST /dashboard/categories          - Store new category
GET  /dashboard/categories/{id}/edit - Show edit form
PUT  /dashboard/categories/{id}     - Update category
DELETE /dashboard/categories/{id}   - Delete category
```

---

*API Ready to Use! 🚀*
