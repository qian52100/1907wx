<?php

namespace App\Tools;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Tools\Curl;
class Wechat
{
    const appId='wx98fcb5c895c99886';
    const secret='b8778c49c374e82c90a9a55ce96c1fd2';
    //回复文本
    public static function reponseText($xmlObj,$msg){
        echo "<xml>
      <ToUserName><![CDATA[".$xmlObj->FromUserName."]]></ToUserName>
      <FromUserName><![CDATA[".$xmlObj->ToUserName."]]></FromUserName>
      <CreateTime>".time()."</CreateTime>
      <MsgType><![CDATA[text]]></MsgType>
      <Content><![CDATA[".$msg."]]></Content>
    </xml>";die;
    }
    //回复城市天气
    public static function reponseWeaid($city){
        //调用天气接口 获取数据
        $url="http://api.k780.com/?app=weather.future&weaid=".$city."&&appkey=47851&sign=e128338f63dcb12ec4f05aad281b3a0f&format=json";
        //参数传递好
        //调用接口 (GET POST)
        //发送请求 打开文件 接收xml数据
        $data=file_get_contents($url);
        $data=json_decode($data,true);    //转为数组
        $msg="";
        foreach($data['result'] as $key=>$value){
            $msg.=$value['days']." ".$value['week']." ".$value['citynm']." ".$value['temperature']."\n";
        }
        return $msg;
    }
    //获取最新assess_token 并换缓存
    public static function freshToken(){
        $redis_weixin_token_key="weixin_access_token";
        $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".Self::appId."&secret=".Self::secret;
        //请求接口
        $data=file_get_contents($url);
        $data=json_decode($data,true);
        $access_token=$data['access_token']; 
        //缓存access_token
        Redis::set($redis_weixin_token_key,$access_token);
        //Redis::expire($redis_weixin_token_key,3600);
       // echo "token已刷新".date("Y-m-d H:i:s");echo "<br>";
        echo $access_token;
    }
    // 获取access_token微信接口凭证
    public static function getAccessToken(){
        //先判断缓存是否有数据
        $access_token=Cache::get('access_token');
        //有数据直接返回
        //没有数据调微信接口 获取=》存入缓存
        if(empty($access_token)){
            $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".Self::appId."&secret=".Self::secret;
            $data=file_get_contents($url);
            $data=json_decode($data,true);
            $access_token=$data['access_token'];  //access_token如何存储两小时
            Cache::put('access_token',$access_token,7200); //120分钟
        }
        return $access_token;
    }
    //获取用户信息
    public static function getUserInfoByOpenId($openid){
        $access_token=Self::getAccessToken();
        $url1="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
        $res=file_get_contents($url1);
        $res=json_decode($res,true);
        return $res;
    }
    //上传素材
    public static function uploadMedia($path,$data){
        //调用微信接口 把图片传到服务器上
        //获取access_token
        $access_token=Self::getAccessToken();
        $type=$data['media_format'];
        $url="https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".$access_token."&type=".$type;
        //curl发送文件需要先通过CURLFile类处理
        $paths= new \CURLFile(public_path()."/".$path);
        //使用post请求传送数据
        //media  form-data中媒体文件标识，有filename、filelength、content-type等信息
        $postData=['media'=>$paths];
        //调post请求方法
        $res=Curl::Post($url,$postData);
        //json字符串转化为数组
        $res=json_decode($res);
        return $res;
    }
    //获取ticket生成二维码
    public static function createQrcode($channel_status){
        //获取access_token
        $access_token=Self::getAccessToken();
        //url地址
        $url="https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
        //传递参数 7天
        //$postData='{"expire_seconds": 259200, "action_name": "QR_STR_SCENE", "action_info": {"scene": {"scene_str": "'.$channel_status.'"}}}';
        $postdata=[
            "expire_seconds"=>'604800',
            "action_name"=>"QR_STR_SCENE",
            "action_info"=>[
                "scene"=>[
                    "scene_str"=> $channel_status
                ]
            ]
        ];
        $postdata=json_encode($postdata,JSON_UNESCAPED_UNICODE);
        //请求方式post请求
        $res=Curl::Post($url,$postdata);
        $res=json_decode($res,true);
        $ticket=$res['ticket'];
        return $ticket;
    }


}
