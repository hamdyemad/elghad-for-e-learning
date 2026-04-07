<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'image' => $this->image_url,
            'parent_id' => $this->parent_id,
            'parent' => $this->when($this->parent, function() {
                return [
                    'id' => $this->parent->id,
                    'name' => $this->parent->name,
                    'slug' => $this->parent->slug,
                ];
            }),
            'children' => CategoryResource::collection($this->whenLoaded('children')),
            'children_count' => $this->children()->count(),
            'courses_count' => $this->when($this->relationLoaded('courses'), function() {
                return $this->courses->count();
            }),
            'order' => $this->order,
            'status' => $this->status,
            'is_parent' => $this->is_parent,
            'has_children' => $this->has_children,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
