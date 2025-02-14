<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'volume' => 'nullable|integer',
            'edition' => 'nullable|string|max:255',
            'pages' => 'nullable|integer|min:1',
            'isbn' => 'nullable|string|max:13',
            'author' => 'required|string|max:255',
            'genre' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'year' => 'nullable|integer|min:1000|max:' . date('Y'),
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'pdf' => 'nullable|mimes:pdf|max:10240',
        ];
    }
}
