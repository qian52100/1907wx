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
