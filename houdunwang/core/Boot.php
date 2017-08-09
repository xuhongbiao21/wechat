<?php
/**
 * Created by PhpStorm.
 * User: TCKJ
 * Date: 2017/8/7
 * Time: 20:19
 */

namespace houdunwang\core;
class Boot
{
    public static function run()
    {
        self::init();
        self::appRun();
    }


    private static function appRun(){
        $s = isset($_GET['s']) ? strtolower($_GET['s']) : 'home/entry/index';
        $info = explode('/',$s);

        //定义组合模板的常量
        define('APP',$info[0]);
        define('CONTROLLER',$info[1]);
        define('ACTION',$info[2]);

        $className = "\app\\{$info[0]}\controller\\" . ucfirst($info[1]);
        echo call_user_func_array([new $className,$info[2]],[]);
    }

    //初始化框架
    public static function init()
    {
        session_id() || session_start();
        date_default_timezone_set('PRC');
        define('IS_POST', $_SERVER['REQUEST_METHOD'] == 'POST' ? true : false);
    }

}