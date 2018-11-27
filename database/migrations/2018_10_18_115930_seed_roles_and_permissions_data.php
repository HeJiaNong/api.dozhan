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
        Permission::create(['name' => 'manage_users']);
        Permission::create(['name' => 'manage_resources']);
        Permission::create(['name' => 'manage_comments']);
        Permission::create(['name' => 'manage_categories']);
        Permission::create(['name' => 'manage_works']);
        Permission::create(['name' => 'manage_tags']);
        Permission::create(['name' => 'manage_favours']);
        Permission::create(['name' => 'manage_banners']);
        Permission::create(['name' => 'manage_links']);
        //站点权限
        Permission::create(['name' => 'manage_site']);
        Permission::create(['name' => 'manage_permissions']);
        Permission::create(['name' => 'manage_notifications']);

        //管理员角色,赋予权限
        $maintainer = Role::create(['name' => 'Maintainer']);
        $maintainer->givePermissionTo('manage_users');
        $maintainer->givePermissionTo('manage_resources');
        $maintainer->givePermissionTo('manage_comments');
        $maintainer->givePermissionTo('manage_categories');
        $maintainer->givePermissionTo('manage_works');
        $maintainer->givePermissionTo('manage_tags');
        $maintainer->givePermissionTo('manage_favours');
        $maintainer->givePermissionTo('manage_banners');
        $maintainer->givePermissionTo('manage_links');

        //站长角色
        $founder = Role::create(['name' => 'Founder']);
        $founder->givePermissionTo('manage_favours');
        $founder->givePermissionTo('manage_site');
        $founder->givePermissionTo('manage_permissions');
        $founder->givePermissionTo('manage_notifications');
        $founder->givePermissionTo('manage_users');
        $founder->givePermissionTo('manage_resources');
        $founder->givePermissionTo('manage_comments');
        $founder->givePermissionTo('manage_categories');
        $founder->givePermissionTo('manage_works');
        $founder->givePermissionTo('manage_tags');
        $founder->givePermissionTo('manage_banners');
        $founder->givePermissionTo('manage_links');
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
