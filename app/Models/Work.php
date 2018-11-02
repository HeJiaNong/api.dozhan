<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Work extends Model
{
    use SoftDeletes;    //启用软删除

    protected $fillable = ['name','description','category_id','resource_url','cover_url'];

    //本地可用作用域列表
    public $scopes = ['recent','popular'];
    /**
     * 需要被转换成日期的属性。
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /*
     * 最近发布作品排序
     */
    public function scopeRecent($query){
        return $query->orderBy('created_at','desc');
    }

    /*
     * 浏览量最高作品排序
     */
    public function scopePopular($query){
        return $query->orderBy('page_view','desc');
    }

    /*
     * 获取 resource_url 属性
     */
    public function getResourceUrlAttribute($value){
        return $this->resourceLink($value);
    }

    public function getCoverUrlAttribute($value){
        return $this->resourceLink($value);
    }

    /*
     * 通过资源uuid获取此资源的所有链接
     */
    public function resourceLink($uuid){
        //TODO 根据资源id查询是否有合适的切片 资源或者转码资源，并且分配链接
        $link = '#';

        $domain = config('services.qiniu.domain').'/';

        if ($resource = QiniuResource::find($uuid)){
            $link = $domain.$resource->key;
            //如果该资源有持久化处理
            if (($items = collect($resource->persistent['items'])->pluck('key')->toArray()) && ($wis = json_decode($resource->params,true)['wi'])){
                foreach ($wis as $wi){
                    if (in_array($wi,$items)){
                        $link = $domain.$wi;
                        break;
                    }
                }
            }
        }
        return $link;
    }

    /*
     * 获取此作品的标签
     */
    public function tags(){
        return $this->belongsToMany(Tag::class);
    }

    /*
     * 获取此作品的所属分类
     */
    public function category(){
        return $this->belongsTo(Category::class);
    }

    /*
     * 获取此作品的所有评论
     */
    public function comments(){
        return $this->morphMany(Comment::class,'commentable');
    }

    /*
     * 获取此作品的所属用户
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

    /*
     * 获取此作品的所有点赞
     */
    public function favours(){
        return $this->morphMany(Favour::class,'favourable');
    }
}
