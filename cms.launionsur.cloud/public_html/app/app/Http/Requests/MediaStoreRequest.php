<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MediaStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'image' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:10240', 'required_without:images'],
            'images' => ['nullable', 'array', 'min:1', 'required_without:image'],
            'images.*' => ['image', 'mimes:jpeg,png,webp', 'max:10240'],
            'title' => ['nullable', 'string', 'max:255'],
            'titles' => ['nullable', 'array'],
            'titles.*' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:2000'],
            'descriptions' => ['nullable', 'array'],
            'descriptions.*' => ['nullable', 'string', 'max:2000'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'alt_texts' => ['nullable', 'array'],
            'alt_texts.*' => ['nullable', 'string', 'max:255'],
            'collection_id' => ['nullable', 'integer', 'exists:media_collections,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:media_tags,id'],
        ];
    }
}
