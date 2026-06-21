<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'recipient_type' => $this->recipient_type,
            'recipient_type_label' => $this->getRecipientTypeLabel(),
            'is_read' => $this->is_read,
            'read_at' => $this->read_at?->toIso8601String(),
            'sent_via_firebase' => $this->sent_via_firebase,
            'sender' => $this->whenLoaded('sender', function () {
                return [
                    'id' => $this->sender->id,
                    'name' => $this->sender->name,
                ];
            }),
            'recipient' => $this->whenLoaded('recipient', function () {
                return [
                    'id' => $this->recipient->id,
                    'name' => $this->recipient->name,
                    'email' => $this->recipient->email,
                ];
            }),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    protected function getRecipientTypeLabel(): string
    {
        return match ($this->recipient_type) {
            'all_students' => 'جميع الطلاب',
            'all_instructors' => 'جميع المحاضرين',
            'single_student' => 'طالب',
            'single_instructor' => 'محاضر',
            default => $this->recipient_type,
        };
    }
}
