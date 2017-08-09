<?php
function p($var)
{
    echo '<pre style="background:#ccc;padding: 5px;">';
    print_r($var);
    echo '</pre>';
}

//echo 1;
//c('wechat.token');
function c($path)
{
    $info = explode('.', $path);
//    p($info);
//    Array
//    (
//        [0] => wechat
//        [1] => token
//)
    //include './system/config/wechat.php';
    $config = include './system/config/' . $info[0] . '.php';
//    p($config);//./system/config/wechat.php
//    wechat.php token?wechat.php token:null
    return isset($config[$info[1]]) ? $config[$info[1]] : NULL;
}
