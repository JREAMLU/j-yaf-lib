<?php

namespace App\Lib;

class Constant {

    const SUCCESS_CODE = 0;
    const SYSTEM_ERROR_CODE = 1000;
    const PARAMS_ERROR_CODE = 1001;
    const NOT_LOGIN = 1002;

    const YAF_ERR_NOTFOUND_MODULE = 'module not found';
    const YAF_ERR_NOTFOUND_CONTROLLER = 'controller not found';
    const YAF_ERR_NOTFOUND_ACTION = 'action not found';
    const YAF_ERR_NOTFOUND_VIEW = 'view not found';

    public static $text = [
        self::SUCCESS_CODE => 'success',
        self::SYSTEM_ERROR_CODE => 'system error',
        self::PARAMS_ERROR_CODE => 'params error',
        self::NOT_LOGIN => 'not login',
    ];
}
