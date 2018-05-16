<?php

namespace app\admin\model;
use think\Model;

class WxChat extends Model{
	//Protected $autoCheckFields = false;

	//回复多图文类型的微信消息
	public function responseNews($postObj,$arr){
		$fromUser = $postObj->ToUserName;
        $toUser   = $postObj->FromUserName; 

		$template = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[%s]]></MsgType>
                    <ArticleCount>".count($arr)."</ArticleCount>
                    <Articles>";
        foreach($arr as $k=>$v){
            $template .="<item>
                        <Title><![CDATA[".$v['title']."]]></Title> 
                        <Description><![CDATA[".$v['desc']."]]></Description>
                        <PicUrl><![CDATA[".$v['picUrl']."]]></PicUrl>
                        <Url><![CDATA[".$v['url']."]]></Url>
                        </item>";
        }
        $template .="</Articles>
                </xml>";
        echo sprintf($template, $toUser, $fromUser, time(), 'news');
	}

	//回复纯文本
	public function responseText($postObj,$content){
		$toUser = $postObj->FromUserName;
        $fromUser = $postObj->ToUserName;
        $msgType = 'text';

        $template = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[%s]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    </xml>";              
        echo sprintf($template, $toUser, $fromUser, time(), $msgType, $content); 
	}

	public function responseSubscribe($postObj,$arr){
		$this->responseNews($postObj,$arr);
	}

	public function printText(){
		echo 'this is model responseNews';
	}
}