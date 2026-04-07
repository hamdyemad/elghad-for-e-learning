<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\LessonResource;

class CourseResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'professor_profit' => $this->professor_profit,
            'status' => $this->status,
            'is_free' => $this->is_free,
            'level' => $this->level,
            'duration' => $this->duration,
            'thumbnail' => $this->thumbnail ? asset('storage/' . $this->thumbnail) : null,
            'category' => $this->when($this->category, function() {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                ];
            }),
            'category_id' => $this->category_id,
            'instructor' => $this->when($this->instructor, function() {
                return [
                    'id' => $this->instructor->id,
                    'name' => $this->instructor->name,
                    'email' => $this->instructor->email,
                ];
            }),
            'instructor_id' => $this->instructor_id,
            'lessons' => LessonResource::collection($this->whenLoaded('lessons')),
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
            'deleted_at' => $this->deleted_at ? $this->deleted_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
