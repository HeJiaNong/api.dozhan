<?php
namespace App\Transformers;

use App\Models\Resource;
use League\Fractal\TransformerAbstract;

class ResourceTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['user'];

    protected $defaultIncludes = ['resourceable'];

    public $showPlayUrl = false;

    public function __construct($showPlayUrl = false)
    {
        $this->showPlayUrl = $showPlayUrl;
    }

    public function transform(Resource $model){
        return [
            'driver' => $model->resourceable_type,
        ];
    }

    public function includeUser(Resource $model){
        return $this->item($model->user,new UserTransformer());
    }

    public function includeResourceable(Resource $model){
        if ($model->resourceable){
            //拼接出transformer的名称
            $transformerName = '\App\Transformers\\'.substr(get_class($model->resourceable),strrpos(get_class($model->resourceable),'\\')+1).'Transformer';
            return $this->item($model->resourceable,new $transformerName(true));
        }
    }

}