<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ImageRequest extends FormRequest
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

        $rule = [
            'scene' => 'required|string|in:avatar,banner,cover',
        ];

        switch ($this->scene){
            case 'avatar':
                $rule['image'] = [
                    'required',
                    'dimensions:width=200,height=200',  //限制图片分辨率
                    'mimes:psd,jpeg,png,gif,webp,tiff,bmp',   //限制图片类型
                ];
                break;
            case 'banner':
                $rule['image'] = [
                    'required',
                    'dimensions:width=1024,height=400',  //限制图片分辨率
                    'mimes:psd,jpeg,png,gif,webp,tiff,bmp',   //限制图片类型
                ];
                break;
            case 'cover':
                $rule['image'] = [
                    'required',
                    'dimensions:width=480,height=300',  //限制图片分辨率
                    'mimes:psd,jpeg,png,gif,webp,tiff,bmp',   //限制图片类型
                ];
                break;
        }

        return $rule;
    }

    public function messages()
    {
        return [
//            'image.dimensions' => '图片分辨率必须为200*200',
        ];
    }
}
