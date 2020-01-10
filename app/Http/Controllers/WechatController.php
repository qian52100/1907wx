<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tools\Wechat;
use App\Model\Media;
class WechatController extends Controller
{
    private $student=[
        '陈香菲',
        '李倩倩',
        '张攀峰',
        '关天龙',
        '刘世坤',
        '赵梦雪',
        '陈晓曼',
        '徐娇',
        '王胜',
        '田珍'
    ];
    public function index(){

    //提交按钮 微信服务器GET请求=》echostr
    //原样输出echostr即可
        echo  $echostr=request()->echostr;die;
        $xml=file_get_contents("php://input"); //接收原始的xml数据或json数据流

        //写文件里
        file_put_contents("log.txt","\n".$xml."\n",FILE_APPEND);
        //xml转成对象
        $xmlObj=simplexml_load_string($xml);
        //如果用户发送的是文本类型
        if($xmlObj->MsgType=="text"){
            $content=trim($xmlObj->Content);
            if($content=='1'){
                //回复所有班级姓名
                $msg=implode(',',$this->student);
                Wechat::reponseText($xmlObj,$msg);
            }elseif($content=='2'){
                //随机回复一个最帅同学
                shuffle($this->student);
                $msg=$this->student[0];
                Wechat::reponseText($xmlObj,$msg);
            }elseif(mb_strpos($content,'天气')!==false){
                //$city=mb_substr($content,0,-2);
                $city=rtrim($content,'天气');
                if(empty($city)){
                    $city='北京';
                }
                //回复天气信息
                $msg=Wechat::reponseWeaid($city);
                Wechat::reponseText($xmlObj,$msg);
            }
        }
    //如果用户发送的是图片  斗图
        if($xmlObj->MsgType=="image"){
//            $media_id=Media::select("wechat_media_id")->where('media_format','image')
//               ->inRandomOrder()->take(1)->get();
            $data=Media::get()->toArray();
            foreach($data as $k=>$v){
                if($data[$k]["media_format"]=='image'){
                    $media_id=array_column($data,'wechat_media_id');
                    shuffle($media_id);
                    $media_ids=$media_id[0];
                }
            }
            echo "<xml>
                  <ToUserName><![CDATA[".$xmlObj->FromUserName."]]></ToUserName>
                  <FromUserName><![CDATA[".$xmlObj->ToUserName."]]></FromUserName>
                  <CreateTime>".time()."</CreateTime>
                  <MsgType><![CDATA[image]]></MsgType>
                  <Image>
                    <MediaId><![CDATA[".$media_ids."]]></MediaId>
                  </Image>
                   </xml>";die;
        }elseif($xmlObj->MsgType=="voice"){
            //斗语音
            $data=Media::get()->toArray();
            foreach($data as $k=>$v){
                if($data[$k]['media_format']=='voice'){
                    $media_id=array_column($data,'wechat_media_id');
                    shuffle($media_id);
                    $media_ids=$media_id[0];
                }
            }
            echo "<xml>
                  <ToUserName><![CDATA[".$xmlObj->FromUserName."]]></ToUserName>
                  <FromUserName><![CDATA[".$xmlObj->ToUserName."]]></FromUserName>
                  <CreateTime>".time()."</CreateTime>
                  <MsgType><![CDATA[voice]]></MsgType>
                  <Voice>
                    <MediaId><![CDATA[".$media_ids."]]></MediaId>
                  </Voice>
                </xml>";die;
        }elseif($xmlObj->MsgType=="video"){
            //斗视频
//            $media_id=Media::select("wechat_media_id")->where('media_format','video')
//                ->inRandomOrder()->take(1)->get();
            $data=Media::get()->toArray();
            foreach($data as $k=>$v){
                if($data[$k]['media_format']=="video"){
                    $media_id=array_column($data,'wechat_media_id');
                    shuffle($media_id);
                    $media_ids=$media_id[0];
                }
            }
            echo "<xml>
                  <ToUserName><![CDATA[".$xmlObj->FromUserName."]]></ToUserName>
                  <FromUserName><![CDATA[".$xmlObj->ToUserName."]]></FromUserName>
                  <CreateTime>".time()."</CreateTime>
                  <MsgType><![CDATA[video]]></MsgType>
                  <Video>
                    <MediaId><![CDATA[".$media_ids."]]></MediaId>
                    <Title><![CDATA[title]]></Title>
                    <Description><![CDATA[description]]></Description>
                  </Video>
                </xml>";die;
        }
    //关注回复  回复欢迎关注
        if($xmlObj->MsgType=="event" && $xmlObj->Event=="subscribe"){
            $res=Wechat::getUserInfoByOpenId($xmlObj->FromUserName);  //获取用户信息  调接口
            $nickname=$res['nickname'];  //用户名字
            Wechat::reponseText($xmlObj,"欢迎".$nickname."关注");
        }
    //回复文本消息
    //输出xml数据  //文档被动回复
    //未关注h回复 收到
        if($xmlObj->MsgType=="event" && $xmlObj->Event=="unsubscribe"){
            Wechat::reponseText($xmlObj,'收到');
        }
    }

}
