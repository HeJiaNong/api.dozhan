<?php

namespace App\Contracts;

/**
 * Created by PhpStorm.
 * User: JiaNong
 * Date: 2018/12/17
 * Time: 9:23
 */

interface DoResource{

    /**
     * 资源展示
     * @return mixed
     */
    public function show();

    /**
     * 资源下载
     * @return mixed
     */
    public function download();

}