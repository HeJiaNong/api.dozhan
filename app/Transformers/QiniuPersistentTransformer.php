<?php
namespace App\Transformers;

use App\Models\QiniuPersistent;
use League\Fractal\TransformerAbstract;

class QiniuPersistentTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['qiniuResource'];

    public function transform(QiniuPersistent $model){
        return [
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
    }


}