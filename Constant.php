<?php

namespace App\Lib;

class Constant
{
    public const SUCCESS_CODE = 0;
    public const SYSTEM_ERROR_CODE = 1000;
    public const PARAMS_ERROR_CODE = 1001;
    public const NOT_LOGIN = 1002;

    public const YAF_ERR_NOTFOUND_MODULE = 'module not found';
    public const YAF_ERR_NOTFOUND_CONTROLLER = 'controller not found';
    public const YAF_ERR_NOTFOUND_ACTION = 'action not found';
    public const YAF_ERR_NOTFOUND_VIEW = 'view not found';

    public static $text = [
        self::SUCCESS_CODE => 'success',
        self::SYSTEM_ERROR_CODE => 'system error',
        self::PARAMS_ERROR_CODE => 'params error',
        self::NOT_LOGIN => 'not login',
    ];
}
