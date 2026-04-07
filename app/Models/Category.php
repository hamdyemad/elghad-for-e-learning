<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'parent_id',
        'order',
        'status'
    ];

    protected $casts = [
        'order' => 'integer',
        'parent_id' => 'integer'
    ];

    // Relationships
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('order');
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    // Mutators
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucfirst(trim($value));
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug) && !empty($category->name)) {
                $category->slug = self::generateUniqueSlug($category->name);
            }
        });

        static::updating(function ($category) {
            if (!empty($category->name) && (empty($category->slug) || $category->slug !== Str::slug($category->name))) {
                $category->slug = self::generateUniqueSlug($category->name, $category->id);
            }
        });
    }

    protected static function generateUniqueSlug($name, $ignoreId = null)
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        $query = static::where('slug', $slug);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        while ($query->count() > 0) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
            $query = static::where('slug', $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
        }

        return $slug;
    }

    // Accessors
    public function getImageUrlAttribute()
    {
        if ($this->image && file_exists(public_path('storage/' . $this->image))) {
            return asset('storage/' . $this->image);
        }
        
        // Generate color based on ID for consistent colors
        $colors = ['5b73e8', '34c38f', 'f46a6a', 'f1b44c', '50a5f1', '556ee6', 'f672a7', '2ab57d'];
        $color = $colors[$this->id % count($colors)];
        
        // Return placeholder image using UI Avatars
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&size=200&background=' . $color . '&color=fff&bold=true';
    }

    public function getIsParentAttribute()
    {
        return is_null($this->parent_id);
    }

    public function getHasChildrenAttribute()
    {
        return $this->children()->count() > 0;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeChildren($query)
    {
        return $query->whereNotNull('parent_id');
    }

    public function scopeByParent($query, $parentId)
    {
        return $query->where('parent_id', $parentId);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
}
