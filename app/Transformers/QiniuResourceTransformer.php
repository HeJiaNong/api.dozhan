<?php
namespace App\Transformers;

use App\Models\QiniuResource;
use League\Fractal\TransformerAbstract;

class QiniuResourceTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['user','persistent'];
    protected $defaultIncludes = ['persistent'];

    //限制展示字段
    protected $only = [];

    public function __construct(array $only = [])
    {
        $this->only = $only;
    }

    public function transform(QiniuResource $model){
        $transform = [
            'id' => $model->id,
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

        if ($this->only){
            $transform = array_intersect_key($transform,array_flip($this->only));
        }

        return $transform;
    }

    public function includePersistent(QiniuResource $model){
        if ($model->persistent){
            return $this->item($model->persistent,new QiniuPersistentTransformer(['items']));
        }
    }

}