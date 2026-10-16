<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'content' => ['sometimes', 'required', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'format' => ['nullable', 'string', 'in:markdown,plain,html,json'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'reprocess' => ['nullable', 'boolean'],
            'scheduled_at' => ['nullable', 'date'],
        ];
    }
}
