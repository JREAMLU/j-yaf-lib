<?php

namespace App\Lib;

class Constant {

    const SUCCESS_CODE = 0;
    const SYSTEM_ERROR_CODE = 1000;
    const PARAMS_ERROR_CODE = 1001;

    const YAF_ERR_NOTFOUND_MODULE = '找不到指定的模块';
    const YAF_ERR_NOTFOUND_CONTROLLER = '找不到指定的Controller';
    const YAF_ERR_NOTFOUND_ACTION = '找不到指定的Action';
    const YAF_ERR_NOTFOUND_VIEW = '找不到指定的视图文件';

    public static $text = [
        self::SUCCESS_CODE => '成功',
        self::SYSTEM_ERROR_CODE => '系统错误',
        self::PARAMS_ERROR_CODE => '参数错误',
    ];
}