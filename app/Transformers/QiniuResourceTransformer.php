<?php
namespace App\Transformers;

use App\Models\QiniuResource;
use League\Fractal\TransformerAbstract;

class QiniuResourceTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['user','persistents'];
    protected $defaultIncludes = ['persistents'];

    public function transform(QiniuResource $model){
        return [
            'uuid' => $model->uuid,
            'endUser' => $model->endUser,
            'persistentId' => $model->persistentId,
            'bucket' => $model->bucket,
            'key' => $model->key,
            'etag' => $model->etag,
            'fsize' => $model->fsize,
            'mimeType' => $model->mimeType,
            'imageAve' => $model->imageAve,
            'ext' => $model->ext,
            'exif' => $model->exif,
            'imageInfo' => $model->imageInfo,
            'created_at' => $model->created_at->toDateTimeString(),
            'updated_at' => $model->updated_at->toDateTimeString(),
        ];
    }

    public function includePersistents(QiniuResource $model){
        return $this->collection($model->qiniuPersistents,new QiniuPersistentTransformer());
    }

}