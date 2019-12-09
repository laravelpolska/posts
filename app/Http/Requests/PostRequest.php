<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => [
                'required',
                $this->route('post') ? Rule::unique('posts')->ignoreModel($this->route('post')) : Rule::unique('posts'),
            ],
            'body' => [
                'nullable',
                'min:3',
            ],
            'published_at' => [
                'nullable',
                'date',
            ],
        ];
    }
}
