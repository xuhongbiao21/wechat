<?php

namespace houdunwang\view;

class View
{
    public static function __callStatic($name, $arguments)
    {
//        p($name);
        return call_user_func_array([new Base(), $name], $arguments);
    }
}