<?php

namespace App\Http\Controllers\Api;

use App\Models\Banner;
use App\Transformers\BannerTransformer;
use Illuminate\Http\Request;

class BannersController extends Controller
{
    public function __construct()
    {
        $this->middleware('api.auth')->except(['index']);
    }

    public function index(Banner $banner){
        return $this->response->collection(Banner::all(),new BannerTransformer());
    }

    public function store(Banner $banner,Request $request){
        $this->authorize('create',$banner);

        $banner->fill($request->all())->save();

        return $this->response->item($banner,new BannerTransformer());
    }

    public function update(Banner $banner,Request $request){
        $this->authorize('update',$banner);

        $banner->update($request->all());

        return $this->response->item($banner,new BannerTransformer());
    }

    public function destroy(Banner $banner){
        $this->authorize('destroy',$banner);

        $banner->delete();

        return $this->response->noContent();
    }
}
