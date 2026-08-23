<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreFavoritePokemonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pokemon_name' => ['required', 'string', 'max:100', 'alpha_dash:ascii'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('pokemon_name')) {
            $this->merge([
                'pokemon_name' => Str::lower($this->string('pokemon_name')->trim()->toString()),
            ]);
        }
    }
}
