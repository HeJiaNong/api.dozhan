<?php
namespace App\Transformers;

use App\Models\ResourceQiniu;
use League\Fractal\TransformerAbstract;

class ResourceQiniuTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['user','persistent'];

    //限制展示字段
    protected $showPlayUrl = false;

    public function __construct($showPlayUrl = false)
    {
        $this->showPlayUrl = $showPlayUrl;
    }

    public function transform(ResourceQiniu $model){
        return [
            'url' => $model->showUrl($model),
        ];
        if ($this->showPlayUrl){
            return $model->showUrl($model);
        }
        $this->setDefaultIncludes(['persistent']);
        return $model->toArray();
    }

    public function includePersistent(ResourceQiniu $model){
        if ($model->persistent){
            return $this->item($model->persistent,new ResourceQiniuPersistentTransformer(['items']));
        }
    }

}