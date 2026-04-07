<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array|Arrayable|\JsonSerializable
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at?->format('Y-m-d H:i:s'),
            'is_verified' => $this->is_verified ?? false,
            'type' => $this->type ?? 'student',
            'avatar_url' => $this->avatar_url ?? null,
            'roles' => $this->whenLoaded('roles', function() {
                return $this->roles->pluck('name');
            }),
            'permissions' => $this->whenLoaded('permissions', function() {
                return $this->permissions->pluck('name');
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
