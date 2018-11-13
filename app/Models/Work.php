<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Work extends Model
{
    use SoftDeletes;    //启用软删除

    protected $fillable = ['name','description','category_id','video_id','cover_id'];

    //todo 本地可用作用域列表,此方案有待优化
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
     * 获取作品的视频信息
     */
    public function video(){
        return $this->belongsTo(Resource::class,'video_id');
    }

    /*
     * 获取作品的封面信息
     */
    public function cover(){
        return $this->belongsTo(Resource::class,'cover_id');
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
