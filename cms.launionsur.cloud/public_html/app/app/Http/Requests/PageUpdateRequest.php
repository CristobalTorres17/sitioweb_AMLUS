<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PageUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $page = $this->route('page');
        $id = $page?->id ?? null;

        return [
            'title' => 'required|string|min:3|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug,' . ($id ?? 'NULL'),
            'content' => 'required|string|min:10',
            'description' => 'nullable|string|max:500',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'og_image_media_id' => 'nullable|exists:media,id',
        ];
    }
}
