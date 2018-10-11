<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as BaseController;
use Dingo\Api\Routing\Helpers;

class Controller extends BaseController
{
   use Helpers;	//增加了 DingoApi 的 helper，这个 trait 可以帮助我们处理接口响应
}
