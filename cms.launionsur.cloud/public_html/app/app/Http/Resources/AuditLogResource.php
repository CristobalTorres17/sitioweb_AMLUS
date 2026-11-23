<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\AuditService;

class AuditLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'model_type' => $this->model_type,
            'model_id' => $this->model_id,
            'action' => $this->action,
            'action_label' => AuditService::getActionLabel($this->action, $this->model_type),
            'old_values' => $this->old_values ?? null,
            'new_values' => $this->new_values ?? null,
            'is_sensitive' => (bool) $this->is_sensitive,
            'is_restoreable' => (bool) $this->is_restoreable,
            'user' => $this->whenLoaded('user', function () {
                if (!$this->user) {
                    return null;
                }
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            'ip_address' => $this->ip_address ?? null,
            'reason' => $this->reason ?? null,
            'created_at' => $this->created_at ? $this->created_at->toIsoString() : null,
        ];
    }

    /**
     * Return the diff array for detail views.
     */
    public function diff(): array
    {
        return $this->resource->getDiff();
    }
}
