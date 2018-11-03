<?php
namespace App\Transformers;

use App\Models\QiniuPersistent;
use League\Fractal\TransformerAbstract;

class QiniuPersistentTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['qiniuResource'];

    //限制展示字段
    protected $only = [];

    public function __construct(array $only = [])
    {
        $this->only = $only;
    }

    public function transform(QiniuPersistent $model){

        $transform = [
            'id' => $model->id,
            'pipeline' => $model->pipeline,
            'code' => $model->code,
            'desc' => $model->desc,
            'reqid' => $model->reqid,
            'inputBucket' => $model->inputBucket,
            'inputKey' => $model->inputKey,
            'items' => $model->items,
            'created_at' => $model->created_at->toDateTimeString(),
            'updated_at' => $model->updated_at->toDateTimeString(),
        ];

        if ($this->only){
            $transform = array_intersect_key($transform,array_flip($this->only));
        }

        return $transform;
    }


}