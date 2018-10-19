<?php

namespace App\Http\Requests\Api;


class VideoRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'video' => [
                'required',
                'mimetypes:video/avi,video/mp4',
            ],
        ];
    }
}
