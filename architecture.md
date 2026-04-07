# Project Architecture Documentation

## 🏗️ Architecture Overview

This project follows a **strict layered architecture** to ensure clean, maintainable, and scalable code.

---

## 📊 Layer Structure

```
Controller → Service → Interface → Repository → Model
```

### Flow Explanation:
1. **Controller** receives HTTP requests
2. **Controller** calls **Service** methods
3. **Service** contains business logic and calls **Interface** methods
4. **Interface** defines the contract
5. **Repository** implements the interface and handles database operations
6. **Model** represents database entities

---

## 📁 Folder Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   │   └── [Feature]Controller.php
│   │   └── Dashboard/
│   │       └── [Feature]Controller.php
│   ├── Middleware/
│   ├── Requests/
│   │   ├── Store[Feature]Request.php
│   │   └── Update[Feature]Request.php
│   └── Resources/
│       └── [Feature]Resource.php
├── Services/
│   └── [Feature]Service.php
├── Interfaces/
│   └── [Feature]RepositoryInterface.php
├── Repositories/
│   └── [Feature]Repository.php
├── Models/
│   └── [Feature].php
├── Traits/
│   ├── ApiResponseTrait.php
│   └── [SharedBehavior]Trait.php
├── Helpers/
│   └── [Helper]Helper.php
└── Exceptions/
    └── [Custom]Exception.php

resources/
└── views/
    ├── components/
    │   ├── breadcrumb.blade.php
    │   ├── alert.blade.php
    │   ├── form-input.blade.php
    │   ├── form-select.blade.php
    │   ├── form-textarea.blade.php
    │   └── form-file.blade.php
    └── dashboard/
        └── [feature]/
            ├── index.blade.php
            └── form.blade.php (for create & edit)
```

---

## 🎯 Layer Responsibilities

### 1. Controllers (`app/Http/Controllers/`)
**Purpose:** Handle HTTP requests and responses

**Types:**
- **API Controllers** (`app/Http/Controllers/Api/`) - Return JSON responses using ApiResponseTrait
- **Dashboard Controllers** (`app/Http/Controllers/Dashboard/`) - Return Blade views

**Allowed:**
- Use Form Request classes for validation
- Call service methods
- Return responses using Resources (API) or Views (Dashboard)
- Use ApiResponseTrait for consistent API responses

**NOT Allowed:**
- ❌ Business logic
- ❌ Direct model access
- ❌ Database queries
- ❌ Complex calculations
- ❌ Inline validation (use Form Requests)

**API Controller Example:**
```php
use App\Traits\ApiResponseTrait;
use App\Http\Requests\StoreCategoryRequest;

class CategoryController extends Controller
{
    use ApiResponseTrait;

    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $categories = $this->categoryService->getAllCategories();
        
        return $this->successResponse(
            CategoryResource::collection($categories),
            'Categories retrieved successfully'
        );
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = $this->categoryService->createCategory($request->validated());
        
        return $this->createdResponse(
            new CategoryResource($category),
            'Category created successfully'
        );
    }
}
```

**Dashboard Controller Example:**
```php
use App\Http\Requests\StoreCategoryRequest;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $categories = $this->categoryService->getAllCategories();
        return view('dashboard.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('dashboard.categories.form', ['category' => null]);
    }

    public function store(StoreCategoryRequest $request)
    {
        $this->categoryService->createCategory($request->validated());
        return redirect()->route('dashboard.categories.index')
            ->with('success', 'Category created successfully');
    }
}
```

---

### 2. Services (`app/Services/`)
**Purpose:** Contain all business logic

**Allowed:**
- Business logic and calculations
- Call repository interface methods
- Orchestrate multiple repository calls
- Handle transactions
- Throw exceptions

**NOT Allowed:**
- ❌ Direct model access
- ❌ Direct database queries
- ❌ HTTP response formatting

**Example:**
```php
class UserService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers()
    {
        return $this->userRepository->getAll();
    }

    public function createUser(array $data)
    {
        // Business logic here
        if ($this->userRepository->existsByEmail($data['email'])) {
            throw new Exception('Email already exists');
        }
        
        return $this->userRepository->create($data);
    }
}
```

---

### 3. Interfaces (`app/Interfaces/`)
**Purpose:** Define contracts between services and repositories

**Rules:**
- Define all repository methods
- No implementation
- Use type hints

**Example:**
```php
interface UserRepositoryInterface
{
    public function getAll();
    public function findById(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function existsByEmail(string $email): bool;
}
```

---

### 4. Repositories (`app/Repositories/`)
**Purpose:** Handle all database operations

**Allowed:**
- Database queries
- Use Eloquent models
- Query builder operations
- Relationships

**NOT Allowed:**
- ❌ Business logic
- ❌ Validation logic

**Example:**
```php
class UserRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function findById(int $id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function existsByEmail(string $email): bool
    {
        return $this->model->where('email', $email)->exists();
    }
}
```

---

### 5. Models (`app/Models/`)
**Purpose:** Represent database entities

**Rules:**
- Define fillable/guarded properties
- Define relationships
- Define casts
- Use Accessors (get) and Mutators (set) for data transformation
- Use Scopes for reusable query logic
- No business logic

**Example:**
```php
class User extends Model
{
    protected $fillable = ['name', 'email', 'password'];
    
    protected $hidden = ['password', 'remember_token'];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relationships
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    // Accessor: Automatically format data when retrieving
    public function getFullNameAttribute()
    {
        return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name);
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('d M Y');
    }

    // Mutator: Automatically transform data when setting
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower(trim($value));
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    // Scopes: Reusable query filters
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    public function scopeByRole($query, $roleName)
    {
        return $query->whereHas('roles', function($q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%");
        });
    }
}
```

### Using Accessors in Resources:
```php
class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name, // Uses accessor
            'email' => $this->email,
            'created_at' => $this->formatted_created_at // Uses accessor
        ];
    }
}
```

### Using Mutators in Services:
```php
class UserService
{
    public function createUser(array $data)
    {
        // No need to bcrypt password or lowercase email
        // Mutators handle it automatically
        return $this->userRepository->create($data);
    }
}
```

### Using Scopes in Repositories:
```php
class UserRepository implements UserRepositoryInterface
{
    public function getActiveUsers()
    {
        return $this->model->active()->get();
    }

    public function getVerifiedAdmins()
    {
        return $this->model->verified()->byRole('admin')->get();
    }

    public function searchUsers($term)
    {
        return $this->model->search($term)->get();
    }
}
```

---

### 6. Resources (`app/Http/Resources/`)
**Purpose:** Transform model data for API responses

**Rules:**
- Transform all outputs
- Never return raw models
- Format dates, relationships, etc.

**Example:**
```php
class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'roles' => RoleResource::collection($this->whenLoaded('roles'))
        ];
    }
}
```

---

### 7. Form Requests (`app/Http/Requests/`)
**Purpose:** Handle validation logic

**Rules:**
- One request class per action (Store, Update)
- Return validation rules
- Custom error messages
- Authorization logic

**Example:**
```php
class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // or check permissions
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:categories,slug',
            'image' => 'nullable|image|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم القسم مطلوب',
            'slug.unique' => 'هذا الـ slug مستخدم بالفعل',
        ];
    }
}
```

---

### 8. Traits (`app/Traits/`)
**Purpose:** Reusable functionality across classes

**ApiResponseTrait Example:**
```php
trait ApiResponseTrait
{
    protected function successResponse($data, string $message = 'Success', int $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => []
        ], $code);
    }

    protected function errorResponse(string $message, $errors = [], int $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => is_array($errors) ? $errors : [$errors]
        ], $code);
    }

    protected function createdResponse($data, string $message = 'Created')
    {
        return $this->successResponse($data, $message, 201);
    }
}
```

---

### 9. Blade Components (`resources/views/components/`)
**Purpose:** Reusable UI elements

**Available Components:**
- `breadcrumb.blade.php` - Page breadcrumb navigation
- `alert.blade.php` - Success/error alerts
- `form-input.blade.php` - Text input field
- `form-select.blade.php` - Select dropdown
- `form-textarea.blade.php` - Textarea field
- `form-file.blade.php` - File upload field

**Usage Example:**
```blade
<x-breadcrumb 
    title="إدارة الأقسام"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard'],
        ['label' => 'الأقسام']
    ]"
/>

<x-alert type="success" />

<x-form-input 
    name="name" 
    label="اسم القسم" 
    :value="$category->name ?? ''"
    :required="true"
/>

<x-form-select 
    name="status" 
    label="الحالة"
    :value="$category->status ?? 'active'"
    :options="['active' => 'نشط', 'inactive' => 'غير نشط']"
/>
```

**Benefits:**
- No code duplication in views
- Consistent UI across pages
- Easy to maintain and update
- Single form for create & edit

---

## 🔧 Dependency Injection
**Purpose:** Transform model data for API responses

**Rules:**
- Transform all outputs
- Never return raw models
- Format dates, relationships, etc.

**Example:**
```php
class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'roles' => RoleResource::collection($this->whenLoaded('roles'))
        ];
    }
}
```

---

## 🔧 Dependency Injection

### Service Provider Binding (`app/Providers/AppServiceProvider.php`)

```php
public function register()
{
    $this->app->bind(
        \App\Interfaces\UserRepositoryInterface::class,
        \App\Repositories\UserRepository::class
    );
}
```

### Usage in Services:
```php
class UserService
{
    protected $userRepository;

    // Inject interface, not implementation
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }
}
```

---

## 📤 Unified Response Format

All API responses must follow this structure:

```json
{
    "success": true,
    "message": "Operation completed successfully",
    "data": {},
    "errors": []
}
```

### Success Response:
```php
return response()->json([
    'success' => true,
    'message' => 'User created successfully',
    'data' => new UserResource($user)
], 201);
```

### Error Response:
```php
return response()->json([
    'success' => false,
    'message' => 'Validation failed',
    'data' => null,
    'errors' => $validator->errors()
], 422);
```

---

## 🎨 Blade Components

### Purpose
Reusable UI components to eliminate view duplication

### Available Components

#### 1. Breadcrumb Component
**File:** `resources/views/components/breadcrumb.blade.php`

**Usage:**
```blade
<x-breadcrumb 
    title="إدارة الأقسام"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard'],
        ['label' => 'الأقسام']
    ]"
/>
```

#### 2. Alert Component
**File:** `resources/views/components/alert.blade.php`

**Usage:**
```blade
<x-alert type="success" />
<x-alert type="error" />
```

#### 3. Form Input Component
**File:** `resources/views/components/form-input.blade.php`

**Usage:**
```blade
<x-form-input 
    name="name" 
    label="اسم القسم" 
    :value="$category->name ?? ''"
    :required="true"
    placeholder="أدخل الاسم"
    hint="نص مساعد"
/>
```

#### 4. Form Select Component
**File:** `resources/views/components/form-select.blade.php`

**Usage:**
```blade
<x-form-select 
    name="status" 
    label="الحالة"
    :value="$category->status ?? 'active'"
    :options="['active' => 'نشط', 'inactive' => 'غير نشط']"
    placeholder="-- اختر --"
/>
```

#### 5. Form Textarea Component
**File:** `resources/views/components/form-textarea.blade.php`

**Usage:**
```blade
<x-form-textarea 
    name="description" 
    label="الوصف"
    :value="$category->description ?? ''"
    :rows="4"
/>
```

#### 6. Form File Component
**File:** `resources/views/components/form-file.blade.php`

**Usage:**
```blade
<x-form-file 
    name="image" 
    label="الصورة"
    :currentFile="$category->image_url ?? null"
    accept="image/*"
/>
```

### Single Form Pattern
Use one form view for both create and edit:

```blade
<!-- form.blade.php -->
<form action="{{ $category ? route('update', $category->id) : route('store') }}" method="POST">
    @csrf
    @if($category) @method('PUT') @endif
    
    <x-form-input name="name" :value="$category->name ?? ''" />
    <!-- More fields... -->
</form>
```

**Controller:**
```php
public function create()
{
    return view('dashboard.categories.form', ['category' => null]);
}

public function edit($id)
{
    $category = $this->service->find($id);
    return view('dashboard.categories.form', ['category' => $category]);
}
```

---

## 🎨 Naming Conventions

| Component | Pattern | Example |
|-----------|---------|---------|
| API Controller | `[Feature]Controller` | `CategoryController` (in Api folder) |
| Dashboard Controller | `[Feature]Controller` | `CategoryController` (in Dashboard folder) |
| Service | `[Feature]Service` | `CategoryService` |
| Interface | `[Feature]RepositoryInterface` | `CategoryRepositoryInterface` |
| Repository | `[Feature]Repository` | `CategoryRepository` |
| Model | `[Feature]` | `Category` |
| Resource | `[Feature]Resource` | `CategoryResource` |
| Form Request | `Store[Feature]Request` / `Update[Feature]Request` | `StoreCategoryRequest` |
| Trait | `[Behavior]Trait` | `ApiResponseTrait` |
| Exception | `[Feature][Error]Exception` | `CategoryAlreadyExistsException` |
| Blade Component | `[component-name].blade.php` | `form-input.blade.php` |

---

## ♻️ Code Reusability

### Base Classes
Create base classes for shared functionality:
- `BaseController`
- `BaseService`
- `BaseRepository`

### Traits
Use traits for shared behaviors:
- `ApiResponseTrait` - Unified API responses
- `HasRoles` - Role management
- `HasPermissions` - Permission management
- `Searchable` - Search functionality
- `Filterable` - Filter functionality

### Helpers
Create helper functions for common operations:
- `responseHelper.php`
- `dateHelper.php`
- `stringHelper.php`

---

## ⚠️ Error Handling

### Global Exception Handler
**Use Laravel's built-in exception handler** - NO try/catch in controllers or services unless specific handling is needed.

**Location:** `app/Exceptions/Handler.php`

**How it works:**
1. Services throw exceptions when errors occur
2. Laravel catches them automatically
3. Handler formats them into unified response
4. Controllers stay clean

**Handler Example:**
```php
class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        // Handle specific exceptions
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Resource not found',
                'data' => null,
                'errors' => [$exception->getMessage()]
            ], 404);
        }

        if ($exception instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'data' => null,
                'errors' => $exception->errors()
            ], 422);
        }

        // Handle custom exceptions
        if ($exception instanceof CustomBusinessException) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'data' => null,
                'errors' => []
            ], $exception->getCode() ?: 400);
        }

        // Default error response
        return response()->json([
            'success' => false,
            'message' => 'An error occurred',
            'data' => null,
            'errors' => [config('app.debug') ? $exception->getMessage() : 'Internal server error']
        ], 500);
    }
}
```

### Custom Exceptions (`app/Exceptions/`)
Create specific exceptions for business logic:
```php
class UserAlreadyExistsException extends Exception
{
    protected $code = 409;
    protected $message = 'User with this email already exists';
}
```

### Service Implementation (Clean - No try/catch):
```php
class UserService
{
    public function createUser(array $data)
    {
        if ($this->userRepository->existsByEmail($data['email'])) {
            throw new UserAlreadyExistsException();
        }
        
        return $this->userRepository->create($data);
    }
}
```

### Controller Implementation (Clean - No try/catch):
```php
class UserController extends Controller
{
    public function store(Request $request)
    {
        $user = $this->userService->createUser($request->validated());
        
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => new UserResource($user),
            'errors' => []
        ], 201);
    }
}
```

**Rule:** Only use try/catch when you need specific error recovery logic, otherwise let the global handler manage it.

---

## 🚫 Strict Restrictions

### ❌ NEVER DO:
1. Access models directly in controllers
2. Write business logic in controllers
3. Write database queries outside repositories
4. Return raw models from controllers
5. Duplicate code across files
6. Use concrete classes instead of interfaces in services
7. Skip dependency injection
8. Break the layer flow
9. Use try/catch everywhere (use global exception handler)
10. Use inline validation in controllers (use Form Requests)
11. Duplicate form views (use single form for create & edit)
12. Repeat response formatting (use ApiResponseTrait)

### ✅ ALWAYS DO:
1. Follow the layer structure
2. Use dependency injection
3. Return unified responses
4. Use resources for output
5. Keep controllers thin
6. Keep services focused
7. Keep repositories simple
8. Reuse existing code
9. Throw exceptions in services, let handler catch them
10. Create custom exceptions for business logic errors
11. Use Form Request classes for validation
12. Use ApiResponseTrait for API responses
13. Use Blade components for reusable UI elements
14. Use single form view for create & edit operations

---

## 📝 Implementation Checklist

When creating a new feature:

- [ ] Create Model with relationships, accessors, mutators, and scopes
- [ ] Create Migration
- [ ] Create Interface with method signatures
- [ ] Create Repository implementing interface (use scopes)
- [ ] Create Service with business logic (throw exceptions, no try/catch)
- [ ] Create Custom Exceptions if needed
- [ ] Create Form Request classes (Store & Update)
- [ ] Create Resource for output transformation (use accessors)
- [ ] Create API Controller with ApiResponseTrait (no inline validation, no try/catch)
- [ ] Create Dashboard Controller (use Form Requests)
- [ ] Create Blade views using components (single form for create & edit)
- [ ] Update Global Exception Handler if needed
- [ ] Bind Interface to Repository in ServiceProvider
- [ ] Define routes (API + Dashboard)
- [ ] Create Seeder
- [ ] Test the flow

---

## 🔍 Code Review Checklist

Before committing code:

- [ ] No business logic in controllers?
- [ ] No model access in controllers?
- [ ] Services use interfaces only?
- [ ] All DB operations in repositories?
- [ ] Using unified response format?
- [ ] Using resources for output?
- [ ] Dependency injection used?
- [ ] No code duplication?
- [ ] Following naming conventions?
- [ ] Using accessors/mutators/scopes in models?
- [ ] No try/catch blocks (unless specific handling needed)?
- [ ] Exceptions handled globally?
- [ ] Using Form Request classes for validation?
- [ ] Using ApiResponseTrait for API responses?
- [ ] Using Blade components for UI elements?
- [ ] Single form view for create & edit?

---

## 🎓 Example: Complete Feature Implementation

### Feature: User Management

**1. Model** (`app/Models/User.php`)
```php
class User extends Model
{
    protected $fillable = ['name', 'email', 'password'];
    
    protected $hidden = ['password', 'remember_token'];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relationships
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    // Mutators: Auto-transform on save
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower(trim($value));
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    // Accessors: Auto-format on retrieve
    public function getFullNameAttribute()
    {
        return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name);
    }

    // Scopes: Reusable queries
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }
}
```

**2. Interface** (`app/Interfaces/UserRepositoryInterface.php`)
```php
interface UserRepositoryInterface
{
    public function getAll();
    public function findById(int $id);
    public function create(array $data);
}
```

**3. Repository** (`app/Repositories/UserRepository.php`)
```php
class UserRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function findById(int $id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        // Mutators will handle email lowercase and password hashing
        return $this->model->create($data);
    }

    public function existsByEmail(string $email): bool
    {
        return $this->model->where('email', $email)->exists();
    }

    // Using scopes for cleaner queries
    public function getActiveUsers()
    {
        return $this->model->active()->get();
    }

    public function getVerifiedUsers()
    {
        return $this->model->verified()->get();
    }
}
```

**4. Service** (`app/Services/UserService.php`)
```php
class UserService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers()
    {
        return $this->userRepository->getAll();
    }

    public function createUser(array $data)
    {
        // Check business rule
        if ($this->userRepository->existsByEmail($data['email'])) {
            throw new UserAlreadyExistsException();
        }
        
        // No need to bcrypt password - mutator handles it
        return $this->userRepository->create($data);
    }
}
```

**5. Resource** (`app/Http/Resources/UserResource.php`)
```php
class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at->format('Y-m-d H:i:s')
        ];
    }
}
```

**6. Controller** (`app/Http/Controllers/UserController.php`)
```php
class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $users = $this->userService->getAllUsers();
        
        return response()->json([
            'success' => true,
            'message' => 'Users retrieved successfully',
            'data' => UserResource::collection($users),
            'errors' => []
        ]);
    }

    public function store(Request $request)
    {
        // No try/catch - let global handler manage exceptions
        $user = $this->userService->createUser($request->validated());
        
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => new UserResource($user),
            'errors' => []
        ], 201);
    }
}
```

**7. Service Provider Binding** (`app/Providers/AppServiceProvider.php`)
```php
public function register()
{
    $this->app->bind(
        \App\Interfaces\UserRepositoryInterface::class,
        \App\Repositories\UserRepository::class
    );
}
```

---

## 🎯 Remember

**Breaking this architecture = Failure**

Always follow: **Controller → Service → Interface → Repository → Model**

---

*Last Updated: 2026-03-26*
