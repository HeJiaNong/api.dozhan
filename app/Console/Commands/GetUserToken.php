<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class GetUserToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dozhan:get-user-token {user_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '获取用户token';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //获取输入id
        $userId = $this->argument('user_id');

        if (!$userId){
            $userId = $this->ask('请输入用户id');
        }

        $user = User::find($userId);

        if (!$user){
            return $this->error('用户不存在');
        }

        //获取token
        $token = Auth::guard('api')->login($user);

        $this->line('已获取'.$userId.'号用户的token:');
        $this->info($token);
    }
}
