<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
 * HTTP 设计了很多动词，来表示不同的操作，RESTful 很好的利用的这一点，我们需要正确的使用 HTTP 动词，来表明我们要如何操作资源。
 * 先来解释一个概念，幂等性，指一次和多次请求某一个资源应该具有同样的副作用，也就是一次访问与多次访问，对这个资源带来的变化是相同的。
 */

/*
 *   动词	    描述	                                是否幂等
 *
 *   GET	    获取资源，单个或多个	                是
 *   POST	    创建资源	                            否
 *   PUT	    更新资源，客户端提供完整的资源数据	    是
 *   PATCH	    更新资源，客户端提供部分的资源数据	    否
 *   DELETE	    删除资源                             是
 */

//200 OK - 对成功的 GET、PUT、PATCH 或 DELETE 操作进行响应。也可以被用在不创建新资源的 POST 操作上
//201 Created - 对创建新资源的 POST 操作进行响应。应该带着指向新资源地址的 Location 头
//202 Accepted - 服务器接受了请求，但是还未处理，响应中应该包含相应的指示信息，告诉客户端该去哪里查询关于本次请求的信息
//204 No Content - 对不会返回响应体的成功请求进行响应（比如 DELETE 请求）
//304 Not Modified - HTTP缓存header生效的时候用
//400 Bad Request - 请求异常，比如请求中的body无法解析
//401 Unauthorized - 没有进行认证或者认证非法
//403 Forbidden - 服务器已经理解请求，但是拒绝执行它
//404 Not Found - 请求一个不存在的资源
//405 Method Not Allowed - 所请求的 HTTP 方法不允许当前认证用户访问
//410 Gone - 表示当前请求的资源不再可用。当调用老版本 API 的时候很有用
//415 Unsupported Media Type - 如果请求中的内容类型是错误的
//422 Unprocessable Entity - 用来表示校验错误
//429 Too Many Requests - 由于请求频次达到上限而被拒绝访问

// Dingo/api 路由 获得一个路由实例
$api = app(\Dingo\Api\Routing\Router::class);

$api->version('v1',[
    'namespace' => 'App\Http\Controllers\Api',
    'middleware' => [
        'serializer',
        'bindings', //路由模型绑定
    ],

],function ($api){
    //游客可以访问的接口
    $api->group([
        'middleware' => 'api.throttle', //DingoApi 已经为我们提供了调用频率限制的中间件 api.throttle
        'limit' => config('api.rate_limits.sign.limit'),    //接口频率限制
        'expires' => config('api.rate_limits.sign.expires'),
    ], function ($api) {
        //测试接口
        $api->get('test',function (){
            dd([
                'APP_ENV' => env('APP_ENV'),
                'APP_DEBUG' => env('APP_DEBUG'),
                'API_DEBUG' => env('API_DEBUG'),
                'Token过期时间:' => Auth::guard('api')->factory()->getTTL().'分钟',
                '网站URL' => env('APP_URL'),
            ]);
        });
        //==============================================================================================================
        //邮箱验证码
        $api->post('verificationCodes/email','VerificationCodesController@email')->name('api.verificationCodes.email');
        //用户注册
        $api->post('user','UsersController@store')->name('api.users.store');
        //用户登陆
        $api->post('authorizations','AuthorizationsController@store')->name('api.authorizations.store');
        //获取指定用户发布的专辑
        $api->get('users/{user}/albums','AlbumsController@userIndex')->name('api.users.albums.index');
        //==============================================================================================================
        //获取专辑列表
        $api->get('albums','AlbumsController@index')->name('api.albums.index');
        //获取专辑所属分类
        $api->get('albums/{album}/category','AlbumsController@CategoryIndex')->name('api.albums.category.index');
        //获取专辑下的所有视频
        $api->get('albums/{album}/avs','AlbumsController@AvsIndex')->name('api.albums.avs.index');
        //==============================================================================================================
        //获取分类列表
        $api->get('categories','CategoriesController@index')->name('api.categories.index');
        //获取某分类下的所有专辑
        $api->get('categories/{category}/albums','CategoriesController@albumsIndex')->name('api.categories.albums.index');
        //获取某分类下的所有视频
        $api->get('categories/{category}/avs','CategoriesController@avsIndex')->name('api.categories.avs.index');
        //==============================================================================================================
        //视频列表
        $api->get('avs','AvsController@index')->name('api.avs.index');
        //==============================================================================================================
        //获取所有标签
        $api->get('tags','TagsController@index')->name('api.tags.index');
        //获取某标签下的所有视频
        $api->get('tags/{tag}/avs','TagsController@avsIndex')->name('api.tags.avs.index');
        //==============================================================================================================


    });

    //需要token验证的接口
    $api->group(['middleware' => 'api.auth'],function ($api){
        //==============================================================================================================
        //刷新token
        $api->put('authorizations/current','AuthorizationsController@update')->name('api.authorizations.update');
        //删除token
        $api->delete('authorizations/current','AuthorizationsController@destroy')->name('api.authorizations.destroy');
        //当前登陆用户信息
        $api->get('user','UsersController@me')->name('api.user.show');
        //编辑登陆用户信息 patch 部分修改资源，提供部分资源信息 注意，PATCH 请求方式只能接收 application/x-www-form-urlencoded 的 [Content-type] 的表单信息
        $api->patch('user','UsersController@update')->name('api.user.update');
        //==============================================================================================================
        //资源api
        $api->group(['prefix' => 'resource'],function ($api){
            //上传图片
            $api->post('image','ResourcesController@image')->name('api.resource.image');
            //上传视频
            $api->post('video','ResourcesController@video')->name('api.resource.video');
        });
        //==============================================================================================================
        //发布专辑
        $api->post('albums','AlbumsController@store')->name('api.albums.store');
        //修改专辑
        $api->patch('albums/{album}','AlbumsController@update')->name('api.albums.update');
        //删除专辑
        $api->delete('albums/{album}','AlbumsController@destroy')->name('api.albums.destroy');
        //==============================================================================================================
        //新建分类
        $api->post('categories','CategoriesController@store')->name('api.categories.store');
        //修改分类
        $api->patch('categories/{category}','CategoriesController@update')->name('api.categories.update');
        //删除分类
        $api->delete('categories/{category}','CategoriesController@destroy')->name('api.categories.destroy');
        //==============================================================================================================
        //发布视频
        $api->post('avs','AvsController@store')->name('api.avs.store');
        //编辑视频
        $api->patch('avs/{av}','AvsController@update')->name('api.av.update');
        //删除视频
        $api->delete('avs/{av}','AvsController@destroy')->name('api.av.destroy');
        //==============================================================================================================
        //新增标签
        $api->post('tags','TagsController@store')->name('api.tags.store');
        //修改标签
        $api->patch('tags/{tag}','TagsController@update')->name('api.tags.update');
        //删除标签
        $api->delete('tags/{tag}','TagsController@destroy')->name('api.tags.destroy');
        //==============================================================================================================
    });

});
