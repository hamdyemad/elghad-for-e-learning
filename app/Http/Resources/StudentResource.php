<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'date_of_birth' => $this->date_of_birth ? $this->date_of_birth->format('Y-m-d') : null,
            'enrollment_date' => $this->enrollment_date ? $this->enrollment_date->format('Y-m-d') : null,
            'status' => $this->status,
            'notes' => $this->notes,
            'avatar_url' => $this->avatar_url ?? null,
            'is_verified' => $this->is_verified ?? false,
            'type' => $this->type ?? 'student',
            'balance' => $this->balance ?? 0,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
            'deleted_at' => $this->deleted_at ? $this->deleted_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
