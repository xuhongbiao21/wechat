<?php
/**
 * Created by PhpStorm.
 * User: TCKJ
 * Date: 2017/8/7
 * Time: 20:37
 */

namespace houdunwang\wechat;
class Wechat
{
    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([new Base(), $name], $arguments);
    }
}