<?php

namespace app\api\controller\v1;
use think\Controller;
use app\api\Model\ChatModel;

/**
 * 微信公众号接口
 */
class WeChat extends Controller{
    //订阅号
	public function index(){
        //获得参数 signature nonce token timestamp echostr
        $timestamp = $_GET['timestamp'];
        $nonce = $_GET['nonce'];
        $token = 'linkgeek';
        $echostr = $_GET['echostr'];
        $signature = $_GET['signature'];

        //形成数组，然后按字典序排序
        $arr = array($timestamp,$nonce,$token);
        sort($arr);
        $tmpstr = implode('', $arr);
        
        //拼接成字符串,sha1加密 ，然后与signature进行校验
        $tmpstr = sha1($tmpstr);
        if($tmpstr == $signature && $echostr){
            //第一次接入weixin api接口的时候
            echo $echostr;
            exit;
        }else{
            $this->definedItem();
            $this->reponseMsg();
        }
	}

    //接收事件推送并回复
    public function reponseMsg()
    {
        //1.获取到微信推送过来post数据（xml格式）
        $postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
        
        //2.处理消息类型，并设置回复类型和内容
        $postObj = simplexml_load_string($postArr);

        //3.判断该数据包是否是订阅的事件推送
        if(strtolower($postObj->MsgType) == 'event'){
            //关注事件subscribe
            if(strtolower($postObj->Event) == 'subscribe'){
                
                //用户订阅回复
                $chatModel = new ChatModel();
                $arr = array(
                    array(
                        'title'=>'欢迎关注菜鸟拍照自修室',
                        'desc'=>"一起学习，让拍照成为一件小事儿！",
                        'picUrl'=>'https://mmbiz.qpic.cn/mmbiz_jpg/IXCA35llKEibGSdPcEkcJmBvFJM7QtrNJCqD08qvHibYRsLgptr7CpYCvUQfLiaibhSbYXXmX4JZRrD1PLRIWjMIAQ/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1',
                        'url'=>'https://mp.weixin.qq.com/mp/homepage?__biz=MzUzMjgzODMxNA==&hid=1&sn=bae04d3c6eedda71b396b46ed146f9dc&scene=18#wechat_redirect',
                    ),
                );
                $chatModel->responseNews($postObj,$arr);
            }

            //扫描二维码推送事件
            if(strtolower($postObj->Event) == 'scan'){
                if($postObj->EventKey == 2000){
                    $tmp = "扫描临时二维码推送";
                }
                if($postObj->EventKey == 3000){
                    $tmp = "扫描永久二维码推送";
                }
                $arr = array(
                    array(
                        'title'=> $tmp,
                        'desc'=>"一起学习，让拍照成为一件小事儿！",
                        'picUrl'=>'https://mmbiz.qpic.cn/mmbiz_jpg/IXCA35llKEibGSdPcEkcJmBvFJM7QtrNJCqD08qvHibYRsLgptr7CpYCvUQfLiaibhSbYXXmX4JZRrD1PLRIWjMIAQ/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1',
                        'url'=>'https://mp.weixin.qq.com/mp/homepage?__biz=MzUzMjgzODMxNA==&hid=1&sn=bae04d3c6eedda71b396b46ed146f9dc&scene=18#wechat_redirect',
                    ),
                );
                $chatModel = new ChatModel();
                $chatModel->responseNews($postObj,$arr);
            }
        }

        //自定义菜单事件
        if(strtolower($postObj->Event) == 'click'){
            if(strtolower($postObj->EventKey) == 'pic'){
                $content = "拍照自定义菜单推送";              
            }
            if(strtolower($postObj->EventKey) == 'video'){
                $content = "视频自定义菜单推送";              
            }

            $chatModel = new ChatModel();
            $chatModel->responseText($postObj,$content); 
        }

        //文本回复
        if(strtolower($postObj->MsgType) == 'text'){
            
            //回复图文
            if(trim($postObj->Content) == "教程"){
                $arr = array(
                    array(
                        'title'=>'欢迎关注菜鸟拍照自修室',
                        'desc'=>"一起学习，让拍照成为一件小事儿！",
                        'picUrl'=>'https://mmbiz.qpic.cn/mmbiz_jpg/IXCA35llKEibGSdPcEkcJmBvFJM7QtrNJCqD08qvHibYRsLgptr7CpYCvUQfLiaibhSbYXXmX4JZRrD1PLRIWjMIAQ/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1',
                        'url'=>'https://mp.weixin.qq.com/mp/homepage?__biz=MzUzMjgzODMxNA==&hid=1&sn=bae04d3c6eedda71b396b46ed146f9dc&scene=18#wechat_redirect',
                    ),
                    array(
                        'title'=>'11岁女孩考入“音乐界哈佛”，这条独木桥她是如何走下来的？',
                        'desc'=>"一起学习，让拍照成为一件小事儿！",
                        'picUrl'=>'https://www.baidu.com/img/bd_logo1.png',
                        'url'=>'http://www.baidu.com',
                    ),
                    array(
                        'title'=>'孩子有这4个表现，说明已经被惯坏了！一定好好教育，不然就晚了',
                        'desc'=>"一起学习，让拍照成为一件小事儿！",
                        'picUrl'=>'https://www.baidu.com/img/bd_logo1.png',
                        'url'=>'http://www.baidu.com',
                    ),
                );
                
                $chatModel = new ChatModel();
                $chatModel->responseNews($postObj,$arr);

            }else{  //回复纯文本
                switch (trim($postObj->Content)) {
                    case '官网':
                        $content = "<a href='https://www.tronron.com'>跳转官网</a>";
                        break;
                    case 1:
                        $content = '您输入的数字是1';
                        break;
                    case 2:
                        $content = '您输入的数字是2';
                        break;
                    case '二维码':
                        $content = "<a href='https://www.tronron.com/api/chat/getLongQrCode'>永久二维码</a>";
                        break;
                    default:
                        $content = '你好，请问有什么能帮到你呢？欢迎给我留言哦！';
                        break;
                }
              
                $chatModel = new ChatModel();
                $chatModel->responseText($postObj,$content); 
            }       
        }      
    }

    //获取accessToken
    function getWxAccessToken(){
        if($_SESSION['access_token'] && $_SESSION['expire_time']>time()){
            return $_SESSION['access_token'];
        }else{
            //订阅号
            //$appid = 'wx03add5947fd59941';
            //$appsecret = '71b680ff23ae098b834ce54e8b50cb14';

            $appid = 'wxd3932d61e4b1f915';
            $appsecret = '0818026fe3d2a9b884213cb7365b4585';
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
            $res = $this->httpGet($url,'get','json');
            $access_token = $res['access_token'];
            $_SESSION['access_token'] = $access_token;
            $_SESSION['expire_time'] = time()+7000;
            return $access_token;
        }
    }

    //获取微信服务器IP地址
    function getWxServerIp(){
        $accessToken = "9_vXowuCGZrnTPwV_lXsgfq9kExyBKsREb5wGZ4MLcY2doAXIC9S-GqxAC70WKFKssINlpH76P5x4VCcJaEuYXqRSh66EICJuh1sSP4orElMJ1at7AfZ3RERsAkEekUJZKrNI-z6VF9CvLNdcJUXFiAAAYBJ";
        $url = "https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=".$accessToken;
        $res = $this->httpGet($url);
        echo "<pre>";
        var_dump($res);
        echo "</pre>";
    }

    //curl
    private function httpGet($url,$type='get',$res='json',$arr='') {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        if($type == 'post'){
            curl_setopt($curl,CURLOPT_POST,1);
            curl_setopt($curl,CURLOPT_POSTFIELDS,$arr);
        }

        $output = curl_exec($curl);
        curl_close($curl);
        if($res == 'json'){
            if(curl_errno($curl)){
                return curl_error($curl);
            }else{
               return json_decode($output,true); 
            }          
        }
    }

    //创建微信自定义菜单
    public function  definedItem(){
        header('content-type:text/html;charset=utf-8');
        echo $access_token=$this ->getWxAccessToken();
        echo $url ='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$access_token;
        $postArr=array(
            'button'=>array(
                array(
                    'name'=>urlencode('往期教程'),
                    'type'=>'view',
                    'url'=>'https://mp.weixin.qq.com/mp/homepage?__biz=MzUzMjgzODMxNA==&hid=1&sn=bae04d3c6eedda71b396b46ed146f9dc&scene=18#wechat_redirect',
                ),
                array(
                    'name'=>urlencode('拍照达人'),
                    'sub_button'=>array(
                        array(
                            'name'=>urlencode('拍照'),
                            'type'=>'click',
                            'key'=>'pic'
                        ),
                        array(
                            'name'=>urlencode('修图'),
                            'type'=>'view',
                            'url'=>'http://wx.tronron.com'
                        ),
                        array(
                            'name'=>urlencode('视频'),
                            'type'=>'click',
                            'key'=>'video'
                        ),
                        array(
                            'name'=>urlencode('旅拍'),
                            'type'=>'click',
                            'key'=>'lvpai'
                        ),
                    )
                ),

                array(
                    'name'=>urlencode('店小二'),
                    'type'=>'view',
                    'url'=>'http://www.qq.com',
                ),//第三个一级菜单
        ));
        echo  $postJson = urldecode(json_encode($postArr));
        $res = $this->httpGet($url,'post','json',$postJson);
        var_dump($res);
    }

    //上传临时图片资源（三天后过期）
    public function uploadImage(){

        //字符集设置为utf8
        header('content-type:text/html;charset=utf8');

        //1.获取全局access_token
        $access_token = $this->getWxAccessToken();

        $url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".$access_token."&type=image";

        // $file=dirname(dirname(dirname(dirname(__FILE__)))).'\Public\Images\1.jpg';
        $file = $_SERVER['DOCUMENT_ROOT'].'\Public\images\qrcode.jpg';
        $postArr = array('media' => "@".$file);

        //4.调用curl
        $res = $this->httpGet($url,'post','json',$postArr);
        var_dump($res['media_id']);
        //return $res['media_id'];
    }

    //群发接口
    public function sendMsgAll(){
        echo $access_token = $this->getWxAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token=".$access_token;

        //文本：
        $sendArr2 = array(
            'touser' => 'oqUaZ1RgQzWs6aS4uckUwlDdgw64',
            'text' => array(
                "content" => urlencode("这是群发消息"),  
            ),
            "msgtype" => "text"
        );

        //图文消息
        $sendArr = array(
            'touser' => 'oqUaZ1RgQzWs6aS4uckUwlDdgw64',
            'mpnews' => array(
                "media_id" => 'vMBvElZFfQqBUw0Z1e4T_idnjqxThnm5USaj1pZBB71TyORc1WymhhrXiSBRHiIu',  
            ),
            "msgtype" => "mpnews"
        );
        echo $postJson = urldecode(json_encode($sendArr));
        echo "<hr />";
        $res = $this->httpGet($url,'post','json',$postJson);
        var_dump($res);
    }

    //获取用户基本信息
    function getBaseInfo(){
        //1.获取code
        $appid = 'wxd3932d61e4b1f915';
        $redirect_uri = urlencode("https://www.tronron.com/api/chat/getUserOpenId");
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
        header('location:'.$url);
    }
    //获取用户openid
    function getUserOpenId(){
        //2.通过code换取网页授权access_token
        $code = $_GET['code'];
        $appid = 'wxd3932d61e4b1f915';
        $secret = '0818026fe3d2a9b884213cb7365b4585';
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$secret."&code=".$code."&grant_type=authorization_code";
        $res = $this->httpGet($url,'get');
        var_dump($res);
    }
    //获取用户详细信息
    function getUserDetail(){
        //1.获取code
        $appid = 'wxd3932d61e4b1f915';
        $redirect_uri = urlencode("https://www.tronron.com/api/chat/getUserInfo");
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect";
        header('location:'.$url);
    }
    function getUserInfo(){
        //2.通过code换取网页授权access_token
        $code = $_GET['code'];
        $appid = 'wxd3932d61e4b1f915';
        $secret = '0818026fe3d2a9b884213cb7365b4585';
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$secret."&code=".$code."&grant_type=authorization_code";
        $res = $this->httpGet($url,'get');
        $access_token = $res['access_token'];
        $openid = $res['openid'];

        //3.拉取用户信息
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
        $res = $this->httpGet($url);
        var_dump($res);
    }

    //模板消息接口
    public function sensTemplateMsg(){
        $access_token = $this->getWxAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
        $postArr = array(
            'touser' => 'oqUaZ1RgQzWs6aS4uckUwlDdgw64',
            'template_id' => 'ZhvpKvbojgYjyh2bVDhQiCxYH6F_5FpZiJvTrk1zJMA',
            'topcolor' => "#FF0000",
            'url' => 'https://mp.weixin.qq.com/mp/homepage?__biz=MzUzMjgzODMxNA==&hid=1&sn=bae04d3c6eedda71b396b46ed146f9dc&scene=18#wechat_redirect',
            'data' => array(
                'name' => array(
                    'value' => urlencode("母亲节"),
                    'color' => "#173177"
                ),
                'date' => array(
                    'value' => '2018-05-13',
                    'color' => "#173177"
                ),
            )
        );
        $postJson = urldecode(json_encode($postArr));
        $res = $this->httpGet($url,'post','json',$postJson);
        var_dump($res);
    }

    /*
    * 二维码
    * 1. 临时二维码
    * 2. 永久二维码
    */

    //临时二维码
    public function getTimeQrCode(){
        $access_token = $this->getWxAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
        $arr = array(
            'expire_seconds'=>604800,
            'action_name'=>'QR_SCENE',
            'action_info'=>array(
                'scene'=>array('scene_id'=>2000)
            ),
        );
        $postJson = json_encode($arr);
        $res = $this->httpGet($url,'post','json',$postJson);
        $ticket = $res['ticket'];
        $url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($ticket);
        echo "<img src='".$url."' />";
    }

    //永久二维码
    public function getLongQrCode(){
        $access_token = $this->getWxAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
        $arr = array(
            'action_name'=>'QR_LIMIT_SCENE',
            'action_info'=>array(
                'scene'=>array('scene_id'=>3000)
            ),
        );
        $postJson = json_encode($arr);
        $res = $this->httpGet($url,'post','json',$postJson);
        $ticket = $res['ticket'];
        $url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($ticket);
        echo "<img src='".$url."' />";
    }

}




?>