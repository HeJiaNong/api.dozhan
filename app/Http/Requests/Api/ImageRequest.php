<?php

namespace App\Http\Requests\Api;


class ImageRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rule = [
            'scene' => 'required|string|in:avatar,banner,cover',
        ];

        switch ($this->scene){
            case 'avatar':
                $rule['image'] = [
                    'required',
                    'dimensions:width=200,height=200',  //限制图片分辨率
                    'mimetypes:image/*',   //限制图片类型
                ];
                break;
            case 'banner':
                $rule['image'] = [
                    'required',
                    'dimensions:width=1024,height=400',  //限制图片分辨率
                    'mimetypes:image/*',   //限制图片类型
                ];
                break;
            case 'cover':
                $rule['image'] = [
                    'required',
                    'dimensions:width=480,height=300',  //限制图片分辨率
                    'mimetypes:image/*',   //限制图片类型
                ];
                break;
        }

//        dd($this->file('image'));
//        dd($rule);

        return $rule;
    }

    public function attributes()
    {
        return [
            'image' => '图片'
        ];
    }

    public function messages()
    {
        return [
            'image.mimetypes' => '图片分辨率必须为200*200',
        ];
    }
}
