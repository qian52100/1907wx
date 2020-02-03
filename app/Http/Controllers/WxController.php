<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tools\Wechat;
use App\Model\Media;
class WxController extends Controller
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
        //echo $echostr=request()->echostr;die;

        //接收原始xml数据或原始json数据流
        $xml=file_get_contents("php://input");
        //写文件里
        file_put_contents("wx.txt","\n".$xml."\n",FILE_APPEND);
        //xml转成对象
        $xmlObj=simplexml_load_string($xml);
        //关注回复 回复xx先生/女士关注
        if($xmlObj->MsgType=='event' && $xmlObj->Event=='subscribe'){
            //获取用户基本信息 调接口
            $res=Wechat::getUserInfoByOpenId($xmlObj->FromUserName);
            var_dump($res);die;
            //用户名字
            $nickname=$res['nickname'];
            $sex=$res['sex'];
            if($sex==2){
                $sex='女士';
            }else{
                $sex='先生';
            }
            Wechat::reponseText($xmlObj,"欢迎".$nickname.$sex."关注");
        }
    }
    //自动上线
    public function gitpull(){
        $git="cd /data/wwwroot/default/1907wx && git pull";
        shell_exec($git);
    }
    //获取access_token
    public function access_token(){
        $access_token=Wechat::getAccessToken();
        echo $access_token;
    }
}
