<?php

namespace App\Http\Requests;

use App\Models\ServiceCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $kind = $this->input('kind', \App\Models\Service::KIND_SERVICE);
        $categoryType = ServiceCategory::typeForKind($kind);

        return [
            'title' => 'required|string|max:255',
            'kind' => 'required|in:service,procedure',
            'category_id' => [
                'nullable',
                Rule::exists('categories', 'id')->where(fn($query) => $query->where('type', $categoryType)),
            ],
            'description' => 'nullable|string',
            'estimated_time' => $kind === \App\Models\Service::KIND_PROCEDURE ? 'required|string|max:50' : 'nullable|string|max:50',
            'cost' => $kind === \App\Models\Service::KIND_PROCEDURE ? 'required|string|max:100' : 'nullable|string|max:100',
            'unit' => 'nullable|string|max:255',
            'schedule' => 'nullable|string|max:255',
            'documentation' => 'nullable|array',
            'documentation.*' => 'string|max:1000',
            'icon' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'featured_order' => 'nullable|integer|min:0|max:10',
            'steps' => $kind === \App\Models\Service::KIND_PROCEDURE ? 'required|array|min:1' : 'nullable|array',
            'steps.*.title' => 'required|string|max:255',
            'steps.*.description' => 'nullable|string',
            'requirements' => $kind === \App\Models\Service::KIND_PROCEDURE ? 'required|array|min:1' : 'nullable|array',
            'requirements.*.title' => 'required|string|max:255',
            'requirements.*.description' => 'nullable|string',
            'requirements.*.is_basic' => 'boolean',
            'media' => 'sometimes|array',
            'media.*.media_id' => 'required_with:media|integer|exists:media,id',
            'media.*.is_primary' => 'boolean',
            'media.*.position' => 'integer|min:0',
            'media.*.caption' => 'nullable|string|max:500',
            'media.*.alt_text' => 'nullable|string|max:500',
        ];
    }
}
