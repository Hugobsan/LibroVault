<?php

namespace App\Http\Requests;

use App\Enums\Role;
use App\Models\Book;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('create', Book::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'volume' => ['nullable', 'integer'],
            'edition' => ['nullable', 'string', 'max:255'],
            'pages' => ['required', 'integer', 'min:1'],
            'isbn' => ['required', 'string', 'max:255', 'unique:books'],
            'author' => ['required', 'string', 'max:255'],
            'genre' => ['required', 'string', 'max:255'],
            'publisher' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'year' => ['nullable', 'integer', 'min:1'],
            'thumbnail' => ['nullable', 'image', 'max:5242880'],
            'pdf' => ['nullable', 'file', 'mimes:pdf', 'max:104857600'],
        ];
    }
}
