<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Login;
use App\Tools\Wechat;
use App\Tools\Curl;

class LoginController extends Controller
{
    public function login(){
        return view('wechat.login');
    }

    public function dologin(){
        $data=request()->except('_token');
        $info=Login::where('account',$data['account'])->first();
        if($info){
            $error_num=$info['error_num'];
            $last_error_time=$info['last_error_time'];
            $time=time();
            if($info['pwd']==$data['pwd']){
                    //验证验证码
                    $codeSession=session('code');
                    if($codeSession!=$data['code']){
                        echo "<script>alert('验证码错误！！');location.href='/';</script>";die;
                    }

                    //密码正确
                    if($error_num>=3&&$last_error_time<3600){
                        $min=60-ceil(($time-$last_error_time)/60);
                        echo "<script>alert('账号已锁定,请于".$min."分钟后登陆');location.href='/';</script>";die;
                    }
                    //错误次数清零 时间改为null
                    $res=Login::where('id',$info['id'])->update(['error_num'=>0,'last_error_time'=>null]);
                    echo "<script>alert('登陆成功');location.href='/wechat';</script>";

            }else{
                //密码错误超过1小时
                if(($time-$last_error_time)>=3600){
                    $res=Login::where('id',$info['id'])->update(['error_num'=>1,'last_error_time'=>$time]);
                    if($res){
                        echo "<script>alert('密码错误,还有两次机会');location.href='/';</script>";die;
                    }
                }
                //密码错误3次
                if($error_num>=3){
                    echo "<script>alert('账号已锁定请于1小时登陆');location.href='/';</script>";die;
                }else{
                    $res=Login::where('id',$info['id'])->update(['error_num'=>$error_num+1,'last_error_time'=>$time]);
                    if($res){
                        if($error_num==2){
                            echo "<script>alert('账号已锁定请于1小时登陆');location.href='/';</script>";die;
                        }
                        echo "<script>alert('密码错误,还有".(3-($error_num+1))."次机会');location.href='/';</script>";die;
                    }
                }
            }

        }else{
            echo "<script>alert('账户或密码错误');location.href='/';</script>";die;
        }

    }

    //模板消息
    public function test(){
        $account=request()->account;
        $code=rand(1000,9999);
        //存验证码
        session(['code'=>$code]);
        $access_token=Wechat::getAccessToken();
        //请求接口
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
        //请求参数
        $args=[
            'touser'=>'oc_ZXv_Sb5N2seYTwQTOeylWHUxw',
            'template_id'=>'iB53OFkD3YN8uJ3Z5LBSl9cHwrWG_Il243WhMz5s3k8',
            'data'=>[
                'name'=>[
                    'value'=>$account,
                    'color'=>'#173177',
                ],
                'code'=>[
                    'value'=>$code,
                    'color'=>'#173177',
                ],
                'time'=>[
                    'value'=>'5分钟内有效,请尽快输入',
                    'color'=>'#173177',
                ],
            ],
        ];
        $args=json_encode($args,JSON_UNESCAPED_UNICODE);
        //发送请求
        $res=Curl::Post($url,$args);
        $res=json_decode($res,true);
        if($res['errcode']=='0'){
            echo '1';
        }else{
            echo '2';
        }
    }
    //绑定账号
    public function user_login(){
        //接收code
        $code=$_GET['code'];
        //换取access_token 请求接口
        $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('WC_APPID').'&secret='.env('WC_APPSEC').'&code='.$code.'&grant_type=authorization_code';
        //请求方式
        $json_data=file_get_contents($url);
        //转为数组
        $arr=json_decode($json_data,true);

        //获取用户信息 根据openid和access_token 请求接口
        $url='https://api.weixin.qq.com/sns/userinfo?access_token='.$arr['access_token'].'&openid='.$arr['openid'].'&lang=zh_CN';
        //请求方式
        $json_user_info=file_get_contents($url);
        //转为数组
        $user_info_arr=json_decode($json_user_info,true);
        $openid=$user_info_arr['openid'];
        session(['openid'=>$openid]);
        return view('login');
    }
    //根据openid入库
    public function user_index(){
        $account=request()->account;
        $pwd=request()->pwd;
        $openid=session('openid');
        $where=[
          ['account','=',$account],
          ['pwd','=',$pwd]
        ];
        $res=Login::where($where)->first();
        if($res){
            //密码相等
            if($res['pwd']==$pwd){
                Login::where($where)->update(['openid'=>$openid]);
                echo "<script>alert('绑定成功');location.href='';</script>";die;
            }else{
                //密码不相等
                echo "<script>alert('绑定失败');location.href='/user_login';</script>";die;
            }
        }
    }
}
