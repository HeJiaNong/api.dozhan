<?php
namespace App\Observers;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored
use App\Models\Album;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class AlbumObserver
{
    public function saved(Album $album){
        //保存时，同步该专辑下的所有视频的分类id
        DB::table('avs')->where('album_id',$album->id)->update(['category_id' => $album->category_id]);
    }
}