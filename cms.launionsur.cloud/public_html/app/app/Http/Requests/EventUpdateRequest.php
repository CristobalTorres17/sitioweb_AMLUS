<?php

namespace App\Http\Requests;

use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $event = $this->route('event');
        $eventId = $event instanceof Event ? $event->id : $event;

        return [
            'title' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('events', 'slug')->ignore($eventId),
            ],
            'description' => 'nullable|string|max:1000',
            'content' => 'nullable|string',
            'event_date' => 'required|date',
            'event_date_end' => 'nullable|date|after_or_equal:event_date',
            'location' => 'nullable|string|max:255',
            'organizer' => 'nullable|string|max:255',
            'featured_image_id' => 'nullable|exists:media,id',
            'status' => 'required|string|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'metadata' => 'nullable|array',
            'category_id' => 'nullable|exists:categories,id',
        ];
    }
}
