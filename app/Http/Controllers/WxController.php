<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tools\Wechat;
class WxController extends Controller
{
    public function index(){
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
