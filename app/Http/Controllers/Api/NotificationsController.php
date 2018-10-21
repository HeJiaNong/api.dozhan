<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Transformers\NotificationTransformer;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    //获取当前用户的所有通知
    public function index(){
        return $this->response->paginator($this->user()->notifications()->paginate(20),new NotificationTransformer());
    }
}
