<?php
/**
 * Created by PhpStorm.
 * User: TCKJ
 * Date: 2017/8/7
 * Time: 20:37
 */

namespace houdunwang\wechat;

use Curl\Curl;

class Base
{

    private $wxObj;

    public function __construct()
    {
        $this->setWxObj();
    }

    /**
     * 处理微信对象
     */
    private function setWxObj()
    {
        if (isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
            //1.接收微信服务器推送过来的消息(xml格式,字符串类型)
            $wxXML = $GLOBALS['HTTP_RAW_POST_DATA'];
            //2.处理消息类型，把xml格式变成一个对象
            $this->wxObj = simplexml_load_string($wxXML);
        }
    }

    //微信验证
    public function validate()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = c('wechat.token');
//        p($token);
        $tmpArr = array($token, $timestamp, $nonce);
//        p($tmpArr);
//        Array
//        (
//            [0] => xhb
//            [1] =>
//    [2] =>
//)
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
//        p($tmpStr);//xhb
        $tmpStr = sha1($tmpStr);
//        p($tmpStr);//8c4bf66c55cb552ad570c553bb9457a3065eabe2
        if ($tmpStr == $signature && isset($_GET['echostr'])) {
            echo $_GET["echostr"];
            exit;
        }
    }

//是否订阅
    public function subscribe()
    {
        //event事件   $MsgType = 'text';
        //2.处理消息类型，把xml格式变成一个对象
//        $wxObj = simplexml_load_string($wxXML);
        if (strtolower($this->wxObj->MsgType) == 'event') {
            //subscribe订阅
            if (strtolower($this->wxObj->Event) == 'subscribe') {
                return true;
            }
        }

        return false;
    }


    /**
     * 获得用户发送的内容
     * @return string
     */
    public function getContent()
    {
        return strtolower($this->wxObj->Content);
    }


    public function responseMsg($text)
    {
        //刚才是用户发给我们，现在是我们发给用户，所以反一下
        //我们变成发送者FromUserName，用户变为接收者ToUserName
        $FromUserName = $this->wxObj->ToUserName;
        $ToUserName = $this->wxObj->FromUserName;
        $CreateTime = time();
        $MsgType = 'text';
        $Content = $text;
        //组合要回复的模板
        $template = <<<str
				<xml>
				<ToUserName><![CDATA[{$ToUserName}]]></ToUserName>
				<FromUserName><![CDATA[{$FromUserName}]]></FromUserName>
				<CreateTime>{$CreateTime}</CreateTime>
				<MsgType><![CDATA[{$MsgType}]]></MsgType>
				<Content><![CDATA[{$Content}]]></Content>
				</xml>
str;
        echo $template;
        exit;
    }

    /**
     * 回复图文消息
     *
     * @param $data
     */
    public function responseNews($data)
    {
        $toUser = $this->wxObj->FromUserName;
        $fromUser = $this->wxObj->ToUserName;
        $time = time();
        //文章总数
        $total = count($data);

        $str = <<<str
<xml>
<ToUserName><![CDATA[{$toUser}]]></ToUserName>
<FromUserName><![CDATA[{$fromUser}]]></FromUserName>
<CreateTime>{$time}</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>{$total}</ArticleCount>
<Articles>
str;
        //组合文章字符串
        foreach ($data as $v) {
            $str .= <<<str
<item>
<Title><![CDATA[{$v['title']}]]></Title> 
<Description><![CDATA[{$v['description']}]]></Description>
<PicUrl><![CDATA[{$v['picUrl']}]]></PicUrl>
<Url><![CDATA[{$v['url']}]]></Url>
</item>
str;
        }

        $str .= <<<str
</Articles>
</xml>
str;
        echo $str;
        exit;


    }

//获取头通行票据
    public function getAccessToken()
    {
        //请求地址
        $url = "https://api.weixin.qq.com/cgi-bin/token";
        //获取access_token填写client_credential
        $grant_type = 'client_credential';
        //第三方用户唯一凭证
        $appid = c('wechat.appid');
        //第三方用户唯一凭证密钥，即appsecret
        $secret = c('wechat.appsecret');
        //最终地址
        $url .= "?grant_type={$grant_type}&appid={$appid}&secret={$secret}";

        //保存accessToken的文件目录
        $path = './storage/data.php';
        //第一次返回为空数组
        $arrToken = include $path;
        if (!$arrToken || $arrToken['endtime'] <= time()) {
            //请求
            $json = file_get_contents($url);
            //把返回的json转为数组
            $arrToken = json_decode($json, true);
            //计算过期时间
            $arrToken['endtime'] = time() + 7200;
            //写入到文件保存，为了不用重复的获取access_token，因为获取access_token是每天2000次
            file_put_contents($path, "<?php return " . var_export($arrToken, true) . "?>");
        }
        return $arrToken['access_token'];
    }

    //创建菜单

    public function createMenu($data)
    {
//        p($data);//接收是数组格式
        //$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".你的token;
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=" . $this->getAccessToken();
        //p($url);//https://api.weixin.qq.com/cgi-bin/menu/create?access_token=FEvLShlADJsXPni5zZSEcLbQBBbfHJNK1kxm5gfGcEbBWhURQY1gkNXYrFvCsfr4CAY0VrQUruujyB1cFvqQI-ylvJIsobc4uGp-mGgDEfwUOQhAGAXRT
                                                    //不转义斜杠|转换为中文格式
        $json = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
//        p($json);
        //使用curl  Object对象格式
        $curl = new Curl();
//        p($curl);
        //执行post请求
        $data = $curl->post($url, $json);
//        p($data);
        return json_decode($data->response, true);

    }
//获得菜单
    public function getMenu(){
        //发送请求 http请求⽅式：GET
        $url = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token=" . $this->getAccessToken();
//        p($url);
        $curl = new Curl();
        $data = $curl->get($url);
//        p($data);
        return json_decode($data->response,true);
    }
//删除菜单
    public function removeMenu(){
        $url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=" . $this->getAccessToken();
        $data = (new Curl())->get($url);
        return json_decode($data->response,true);
    }

}