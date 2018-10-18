<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class SeedRolesAndPermissionsData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 清除缓存
        app()['cache']->forget('spatie.permission.cache');

        //创建权限
        //模型权限
        Permission::create(['name' => 'manage_categories']);
        Permission::create(['name' => 'manage_avs']);
        Permission::create(['name' => 'manage_albums']);
        Permission::create(['name' => 'manage_videos']);
        Permission::create(['name' => 'manage_comments']);
        Permission::create(['name' => 'manage_images']);
        Permission::create(['name' => 'manage_tags']);
        Permission::create(['name' => 'manage_users']);
        //站点权限
        Permission::create(['name' => 'site_setting']);

        //管理员角色,赋予权限
        $maintainer = Role::create(['name' => 'Maintainer']);
        $maintainer->givePermissionTo('manage_categories');
        $maintainer->givePermissionTo('manage_avs');
        $maintainer->givePermissionTo('manage_albums');
        $maintainer->givePermissionTo('manage_videos');
        $maintainer->givePermissionTo('manage_comments');
        $maintainer->givePermissionTo('manage_images');
        $maintainer->givePermissionTo('manage_tags');
        $maintainer->givePermissionTo('manage_users');

        //站长角色
        $founder = Role::create(['name' => 'Founder']);
        $founder->givePermissionTo('site_setting');
        $founder->givePermissionTo('manage_categories');
        $founder->givePermissionTo('manage_avs');
        $founder->givePermissionTo('manage_albums');
        $founder->givePermissionTo('manage_videos');
        $founder->givePermissionTo('manage_comments');
        $founder->givePermissionTo('manage_images');
        $founder->givePermissionTo('manage_tags');
        $founder->givePermissionTo('manage_users');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 清除缓存
        app()['cache']->forget('spatie.permission.cache');

        // 清空所有数据表数据
        $tableNames = config('permission.table_names');

        DB::table($tableNames['role_has_permissions'])->delete();
        DB::table($tableNames['model_has_roles'])->delete();
        DB::table($tableNames['model_has_permissions'])->delete();
        DB::table($tableNames['roles'])->delete();
        DB::table($tableNames['permissions'])->delete();
    }
}
