<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Tools\Curl;
use App\Tools\Wechat;
use Illuminate\Support\Facades\Cache;
class IndexController extends Controller
{
    public function index(){
        return view('wechat.index');
    }
    public function list(){
        return view('wechat.list');
    }
    public function weather(){
        //如果是ajax请求
        if(request()->ajax()){
            $city=request()->city;
            //调用天气接口 并且缓存到当天凌晨
            $cache_name='weatherDate_'.$city;
            $data=Cache::get($cache_name);
            if(empty($data)){
                echo "走接口";
                $url="http://api.k780.com/?app=weather.future&weaid=".$city."&&appkey=47851&sign=e128338f63dcb12ec4f05aad281b3a0f&format=json";
                //$data=file_get_contents($url);
                //发请求
                $data=Curl::Get($url);
                //缓存数据 只到当天24点 (获取24点时间-当前时间)
                $time24=strtotime(date("Y-m-d"))+86400;
                $second=$time24-time();
                Cache::put($cache_name,$data,$second);
            }
            //把调接口得到的json格式天气数据返回
            return $data;
        }
    }
    //获取最新assess_token
    public function haha(){
        echo Wechat::freshToken();
    }
}
