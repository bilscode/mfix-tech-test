<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // @TODO implement
       return [
            'isbn' => ['required', 'numeric', 'unique:books,isbn', 'regex:'.$this->ISBNRule()],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'max:255'],
            'published_year' => ['required', 'integer', 'between:1900,2020'],
            'authors' => ['required', 'array', 'min:1'],
            'authors.*' => ['required', 'numeric', 'distinct', 'exists:authors,id'],
        ];
    }

    public function ISBNRule()
    {
        return '/^(?:ISBN(?:-1[03])?:? )?(?=[0-9X]{10}$|(?=(?:[0-9]+[- ]){3})[- 0-9X]{13}$|97[89][0-9]{10}$|(?=(?:[0-9]+[- ]){4})[- 0-9]{17}$)(?:97[89][- ]?)?[0-9]{1,5}[- ]?[0-9]+[- ]?[0-9]+[- ]?[0-9X]$/';
    }
}
