<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'course_id' => $this->course_id,
            'title' => $this->title,
            'description' => $this->description,
            'duration_minutes' => $this->duration_minutes,
            'passing_score' => $this->passing_score,
            'is_active' => $this->is_active,
            'questions_count' => $this->when($this->relationLoaded('questions'), fn() => $this->questions->count()),
            'questions' => ExamQuestionResource::collection($this->whenLoaded('questions')),
            'course' => new CourseResource($this->whenLoaded('course')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
