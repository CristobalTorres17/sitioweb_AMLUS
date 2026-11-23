<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MediaUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'is_active' => 'boolean',
            'collection_id' => ['nullable', 'integer', 'exists:media_collections,id'],
            'tags' => 'array',
            'tags.*' => 'exists:media_tags,id',
            'title' => 'nullable|string|max:255',
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:2000',
        ];
    }
}
