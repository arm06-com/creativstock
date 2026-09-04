<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ProductUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && Gate::allows('manage-products');
    }

    public function rules(): array
    {
        return [
            'files' => ['required', 'array'],
            'files.*' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,svg,gif', 'max:10240'],
        ];
    }
}
