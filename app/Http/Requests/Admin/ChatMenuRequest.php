<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ChatMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:100'],
            'action' => ['required', 'in:answer,agents,link'],
            'answer' => ['nullable', 'string', 'max:5000', 'required_if:action,answer'],
            'keywords' => ['nullable', 'string', 'max:500'],
            'url' => ['nullable', 'url', 'max:255', 'required_if:action,link'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'answer.required_if' => 'Jawaban bot wajib diisi untuk menu bertipe Jawaban teks.',
            'url.required_if' => 'URL wajib diisi untuk menu bertipe Buka tautan.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);
    }
}
