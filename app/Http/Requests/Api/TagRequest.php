<?php

namespace App\Http\Requests\Api;


class TagRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()){
            case 'POST':
                return [
                    'name' => 'required|string|unique:tags',
                ];
                break;
            case 'PATCH':
                return [
                    'name' => 'string|unique:tags,name,' . $this->tag->id,
                ];
                break;
        }

    }
}
