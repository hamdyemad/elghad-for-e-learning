<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CourseResource;

class LessonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'topic' => $this->topic,
            'title' => $this->title,
            'outsource_link' => $this->outsource_link,
            'outsource_type' => $this->outsource_type,
            'is_free' => (bool) $this->is_free,
            'duration' => $this->duration,
            'file_pdf' => $this->file_pdf ? \Storage::disk('public')->url($this->file_pdf) : null,
            'course_id' => $this->course_id,
            'order' => (int) $this->order,
            'course' => new CourseResource($this->whenLoaded('course')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
        ];
    }
}
