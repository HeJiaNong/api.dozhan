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
    'namespace' => 'App\Http\Controllers\Api',  //命名空间设置
    'middleware' => [
        'serializer',
        'bindings', //路由模型绑定
    ],

],function ($api){
    //游客可以访问的接口
    //==============================================================================================================
    //测试接口
    $api->get('test',function (){return $_SERVER;});
    //==============================================================================================================
    //七牛作品消息回调地址
    $api->post('resource/notification','ResourcesController@notification')->name('api.resource.notification');
    //七牛callbackUrl地址
    $api->any('resources/qiniu/callback','ResourcesController@qiniuCallbak')->name('api.resources.qiniu.callback');
    //==============================================================================================================
    $api->group([
        'middleware' => 'api.throttle', //DingoApi 已经为我们提供了调用频率限制的中间件 api.throttle
        'limit' => config('api.rate_limits.sign.limit'),    //接口频率限制次数
        'expires' => config('api.rate_limits.sign.expires'),    //接口频率限制过期时间
    ],function ($api){
        //邮箱验证码
        $api->post('verificationCodes/email','VerificationCodesController@email')->name('api.verificationCodes.email');
        //用户注册
        $api->post('user','UsersController@store')->name('api.users.store');
        //用户登陆
        $api->post('authorizations','AuthorizationsController@store')->name('api.authorizations.store');
    });
    //==============================================================================================================
    //获取Banner
    $api->get('sites/banners','SitesController@banners')->name('api.sites.banners');
    //==============================================================================================================
    //获取某用户的信息
    $api->get('users/{user}','UsersController@show')->name('api.users.show');
    //==============================================================================================================
    //获取分类信息
    $api->get('categories/{category}','CategoriesController@show')->name('api.categories.show');
    //获取分类列表
    $api->get('categories','CategoriesController@index')->name('api.categories.index');
    //获取某分类下的所有作品
    $api->get('categories/{category}/works','CategoriesController@worksIndex')->name('api.categories.works.index');
    //==============================================================================================================
    //作品列表
    $api->get('works','WorksController@index')->name('api.works.index');
    //获取某用户的作品列表
    $api->get('users/{user}/works','WorksController@userIndex')->name('api.user.woks.index');
    //作品信息
    $api->get('works/{work}','WorksController@show')->name('api.work.show');
    //==============================================================================================================
    //获取所有标签
    $api->get('tags','TagsController@index')->name('api.tags.index');
    //标签信息
    $api->get('tags/{tag}','TagsController@show')->name('api.tags.show');
    //获取某标签下的所有作品
    $api->get('tags/{tag}/works','TagsController@worksIndex')->name('api.tags.works.index');
    //==============================================================================================================
    //获取评论列表
    $api->get('comments','CommentsController@index')->name('api.comments.index');
    //获取某作品下的评论列表
    $api->get('works/{work}/comments','CommentsController@worksIndex')->name('api.works.comments.index');
    //获取评论信息
    $api->get('comments/{comment}','CommentsController@show')->name('api.comment.show');


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
        $api->patch('user','UsersController@updateMe')->name('api.user.update.me');
        //编辑某用户信息
        $api->patch('users/{user}','UsersController@update')->name('api.users.update');
        //注销某用户
        $api->delete('users/{user}','UsersController@destroy')->name('api.users.destroy');
        //恢复注销某用户
        $api->put('users/{user}','UsersController@restore')->name('api.users.restore');
        //==============================================================================================================
        //资源api
        $api->group(['prefix' => 'resource'],function ($api){
            //获取作品上传凭证
            $api->get('videos/token','ResourcesController@videoToken')->name('api.resource.videos.token');
            //获取图片上传凭证
            $api->get('images/token/{scene}','ResourcesController@imageToken')->name('api.resource.videos.token');
            //上传作品
            $api->post('video','ResourcesController@video');
            //上传图片
            $api->post('image','ResourcesController@image');
        });

        //==============================================================================================================
        //新建分类
        $api->post('categories','CategoriesController@store')->name('api.categories.store');
        //修改分类
        $api->patch('categories/{category}','CategoriesController@update')->name('api.categories.update');
        //删除分类
        $api->delete('categories/{category}','CategoriesController@destroy')->name('api.categories.destroy');
        //==============================================================================================================
        //发布作品
        $api->post('works','WorksController@store')->name('api.work.store');
        //编辑作品
        $api->patch('works/{work}','WorksController@update')->name('api.work.update');
        //软删除作品
        $api->delete('works/{work}','WorksController@destroy')->name('api.work.destroy');
        //获取被软删除的作品
        $api->get('destroys/works','WorksController@destroys')->name('api.work.destroys');
        //恢复当前用户已软删除的作品
        $api->put('works/{id}','WorksController@restore')->name('api.work.restore');
        //彻底删除作品
        $api->delete('works/{id}/force','WorksController@forceDestroy')->name('api.work.destroy.force');
        //==============================================================================================================
        //新增标签
        $api->post('tags','TagsController@store')->name('api.tags.store');
        //修改标签
        $api->patch('tags/{tag}','TagsController@update')->name('api.tags.update');
        //删除标签
        $api->delete('tags/{tag}','TagsController@destroy')->name('api.tags.destroy');
        //==============================================================================================================
        //新增评论
        $api->post('comments','CommentsController@store')->name('api.comments.store');
        //修改评论
        $api->patch('comments/{comment}','CommentsController@update')->name('api.comments.update');
        //删除评论
        $api->delete('comments/{comment}','CommentsController@destroy')->name('api.comments.destroy');
        //==============================================================================================================
        //获取当前用户的所有通知
        $api->get('user/notifications','NotificationsController@index')->name('api.me.notifications.index');
        //获取当前用户的未读通知
        $api->get('user/unread/notifications','NotificationsController@unreadNotifications')->name('api.me.notifications.unread');
        //获取当前用户的已读通知
        $api->get('user/markread/notifications','NotificationsController@markReadNotifications')->name('api.me.notifications.markread');
        //获取当前用户的未读通知统计
        $api->get('user/notifications/stats','NotificationsController@stats')->name('api.me.notifications.stats');
        //将当前用户所有通知设置为已读
        $api->patch('user/notifications','NotificationsController@readAll')->name('api.me.notifications.read');
        //将当前用户的某条通知设置为已读
        $api->put('user/notifications/{notification}','NotificationsController@readSingle')->name('api.me.notification.read');
        //删除当前用户的某条通知
        $api->delete('user/notifications/{notification}','NotificationsController@destroySingle')->name('api.me.notification.destroy');
        //删除当前用户所有通知
        $api->delete('user/notifications/','NotificationsController@destroyAll')->name('api.me.notifications.destroy');
        //==============================================================================================================
        //获取所有角色列表
        $api->get('roles','RolesController@index')->name('api.roles.index');
        //获取当前用户的角色
        $api->get('user/roles','RolesController@me')->name('api.me.roles');

        //获取所有权限列表
        $api->get('permissions','PermissionsController@index')->name('api.permissions.index');
        //获取当前用户的权限
        $api->get('user/permissions','PermissionsController@me')->name('api.me.permissions');

    });

});
