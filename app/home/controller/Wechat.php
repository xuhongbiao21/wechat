<?php

namespace app\home\controller;

use houdunwang\wechat\Wechat as Wx;
use Curl\Curl;

class Wechat
{
    public function handle()
    {
        //微信验证
        Wx::validate();

        //如果是订阅
        if (Wx::subscribe()) {
            //回复订阅成功

            Wx::responseMsg('订阅成功3333333');

        }

        //关键词回复
        switch (Wx::getContent()) {
            case "1":
                Wx::responseMsg('https://www.baidu.com');
            case "2":
                Wx::responseMsg('https://www.jingdong.com');
            case "3":
                Wx::responseMsg('https://www.tianmao.com');
            case "4":
                Wx::responseMsg('https://www.mi.com');
            case "5":
                Wx::responseMsg('https://www.apple.com');
            case "安鑫":
                Wx::responseMsg('是狗☺');
            case "许洪标":
                Wx::responseMsg('15247580012');
            case "百度新闻":
                $data = [
                    [
                        'title' => '2017下半年最值得期待的4部手机：每一部都是超级旗舰！',
                        'description' => '腾讯客户端 08-08 00:00',
                        'picUrl' => 'https://ss1.baidu.com/6ONXsjip0QIZ8tyhnq/it/u=974043748,3028528544&fm=173&s=5AA1A9407DA6169AD21A7419030050E0&w=640&h=537&img.JPEG',
                        'url' => 'https://www.baidu.com/home/news/data/newspage?nid=15692360759590093225&n_type=0&p_from=1&dtype=-1'
                    ],
                    [
                        'title' => '10个连科学家无法解释的惊人秘密',
                        'description' => '1、猫发出的咕噜声...',
                        'picUrl' => 'https://ss1.baidu.com/6ONXsjip0QIZ8tyhnq/it/u=2778428899,953251433&fm=173&s=E8144F9C0EC07AC04490DDDC030060B2&w=640&h=370&img.JPEG',
                        'url' => 'https://www.baidu.com/home/news/data/newspage?nid=14878613756406085246&n_type=0&p_from=1&dtype=-1'
                    ],
                    [
                        'title' => 'TCL利润飙涨暗藏玄机：裁员万人 靠面板撑业绩',
                        'description' => '下图为面包财经根据财报绘制的TCL历年上半年总营收与净利润：',
                        'picUrl' => 'https://ss0.baidu.com/6ONWsjip0QIZ8tyhnq/it/u=940784862,3020796760&fm=173&s=9AE0EA0566B5E86F4C14CD1B03005093&w=630&h=370&img.JPEG',
                        'url' => 'https://www.baidu.com/home/news/data/newspage?nid=17566824761682806829&n_type=0&p_from=1&dtype=-1'
                    ]
                ];
                wx::responseNews($data);

        }

        //默认回复
        Wx::responseMsg($this->getTuling(Wx::getContent()));


    }

    //图灵机器人
    public function getTuling($content = '北京天气')
    {
        $url = "http://www.tuling123.com/openapi/api?key=9faefcda0996478ca6194c8bc435f9a0&info=" . $content;
//        p($url);
        //curl方式请求，不要用file_get_contents，比较low
        $curl = new Curl();
        $data = $curl->get($url);
//        p($data);
        $arr = json_decode($data->response, true);
//        p($arr);
        return $arr['text'];
    }

    /**
     * 获取token
     */
    public function handleAccessToken()
    {
        $accessToken = Wx::getAccessToken();
        echo $accessToken;
    }

    /**
     * 获取微信服务器的IP，需要access_token票据
     */
    public function getIp()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=";
        $url .= Wx::getAccessToken();
//        p($url);
        //https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=
        //调取接口
        $data = file_get_contents($url);
//        p($data);
        //{"errcode":41001,"errmsg":"access_token missing hint: [c6KLBA0430vr49!]"}
        $data = json_decode($data);
//p($data);
//stdClass Object
//        (
//        [errcode] => 41001
//    [errmsg] => access_token missing hint: [VJvTxa0471vr47!]
//)
        //输出ip地址，微信服务器很多ip地址
        foreach ($data->ip_list as $ip) {
            echo $ip . '<br/>';
//            p($ip);
        }
    }

    //创建菜单
    public function createMenu()
    {
        $data = [
            'button' => [
                [
                    "name" => "前端项目",
                    "sub_button" => [
                        [
                            "type" => "view",
                            "name" => "小米页面",
                            "url" => "https://github.com/xuhongbiao21/xiaomi"
                        ],
                        [
                            "type" => "view",
                            "name" => "天猫页面",
                            "url" => "https://github.com/xuhongbiao21/tianmao"
                        ],
                        [
                            "type" => "view",
                            "name" => "京东手机端",
                            "url" => "https://github.com/xuhongbiao21/jingdong"
                        ],
                        [
                            "type" => "view",
                            "name" => "微场景",
                            "url" => "https://github.com/xuhongbiao21/wcj"
                        ]
                    ]
                ],
                [
                    "name" => "后台项目",
                    "sub_button" => [
                        [
                            "type" => "view",
                            "name" => "自己的框架",
                            "url" => "https://github.com/xuhongbiao21/xuhongbiao"
                        ],
                        [
                            "type" => "view",
                            "name" => "异步留言板",
                            "url" => "https://github.com/xuhongbiao21/ajax-yb/tree/master/messige"
                        ],
                        [
                            "type" => "view",
                            "name" => "学生管理系统",
                            "url" => "https://github.com/xuhongbiao21/xuhongbiao"
                        ]
                    ]
                ]
            ]
        ];
        $res = Wx::createMenu($data);
        p($res);
    }

    //显示
    public function getMenu()
    {
        $data = Wx::getMenu();
        p($data);
    }

//删除代码
    public function delMenu()
    {
        $res = Wx::removeMenu();
        p($res);
    }

}