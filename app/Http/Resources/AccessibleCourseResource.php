<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CourseResource;

class AccessibleCourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'course' => new CourseResource($this->course),
            'enrolled_at' => $this->enrolled_at?->format('Y-m-d H:i:s'),
            'expires_at' => $this->expires_at?->format('Y-m-d H:i:s'),
            'source' => $this->source, // 'direct' or 'package'
            'package' => $this->package ? (object)[
                'id' => $this->package->id,
                'title' => $this->package->title,
                'slug' => $this->package->slug,
            ] : null,
        ];
    }
}
