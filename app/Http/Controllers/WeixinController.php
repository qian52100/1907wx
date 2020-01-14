<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tools\Wechat;
use App\Tools\Curl;
use App\Model\News;
use App\Model\Users;
use App\Model\Channel;
use App\Model\Media;
use App\Model\Message;
use Illuminate\Support\Facades\Redis;
class WeixinController extends Controller
{
    public function index(){
        //echo  $echostr=request()->echostr;
        $xml=file_get_contents("php://input"); //接收原始的xml数据或json数据流

        //写文件里
        file_put_contents("2.txt","\n".$xml."\n",FILE_APPEND);
        //xml转成对象
        $xmlObj=simplexml_load_string($xml);
        //关注回复  回复欢迎xxx/先生/女士关注
        if($xmlObj->MsgType=="event" && $xmlObj->Event=="subscribe"){
            $res=Wechat::getUserInfoByOpenId($xmlObj->FromUserName);  //获取用户信息  调接口
            //xml中获取渠道标识
            $channel_status=$xmlObj->EventKey;
            $channel_status=ltrim($channel_status,'qrscene_');
            //从用户信息获取渠道标识
            //$channel_status=$res['qr_scene_str'];
            //根据渠道标识 关注人数递增
            Channel::where(['channel_status'=>$channel_status])->increment('num');

            //判断用户信息有没有数据 通过openid查询
            $where=[
                ['openid','=',$res['openid']],
            ];
            $res=Users::where($where)->first();
            //有数据 修改删除状态 修改渠道标识
            if($res) {
               Users::where($where)->update(['channel_status' => $channel_status, 'is_del' => 1]);
            }else{
                $res=Wechat::getUserInfoByOpenId($xmlObj->FromUserName);
                //没有数据 入库
                //添加用户信息
                $data['openid']=$res['openid'];
                $data['nickname']=$res['nickname'];
                $data['sex']=$res['sex'];
                $data['city']=$res['city'];
                $data['channel_status']=$channel_status;
                Users::create($data);
            }

            $nickname=$res['nickname'];  //用户名字
            $sex=$res['sex'];  //用户性别
            if($sex==2){
                $sex='女士';
            }else{
                $sex='先生';
            }
            Wechat::reponseText($xmlObj,"欢迎".$nickname.$sex."关注");
        }
        //文本回复的是图片  下载图片
        if($xmlObj->MsgType=='image'){
            $this->downLoadImg($xmlObj->MediaId,$xmlObj->MsgType,$xmlObj);
        }
        //文本回复的是视频 下载视频
        if($xmlObj->MsgType=='video'){
            $this->downLoadImg($xmlObj->MediaId,$xmlObj->MsgType,$xmlObj);
        }
        //文本回复的是语音 下载语音
        if($xmlObj->MsgType=='voice'){
            $this->downLoadImg($xmlObj->MediaId,$xmlObj->MsgType,$xmlObj);
        }
        if($xmlObj->MsgType=='text'){
            $this->downLoadImg($xmlObj->MediaId,$xmlObj->MsgType,$xmlObj);
        }
        //文本回复最新添加的新闻内容
        if($xmlObj->MsgType=='text'){
            $content=trim($xmlObj->Content);
            if($content=='最新新闻'){
                $n_content=News::orderBy('n_time','desc')->get();
                $value="";
                foreach($n_content as $k=>$v){
                    $content=$n_content[$k]['n_content'];
                    $value.="最新新闻内容:".$content;
                    Wechat::reponseText($xmlObj,$value);
                }
            }
        }
        //新闻+新闻关键字 文本回复1条新闻。如未搜索到，则回复：暂无相关新闻
        if($xmlObj->MsgType=='text'){
            $content=trim($xmlObj->Content);
            if(mb_strpos($content,'新闻＋')!==false){
                $news=ltrim($content,'新闻＋');
                $info=News::where([['n_title','like',"%$news%"]])->get();
                if($info){
                    $value = '';
                    foreach($info as $v){
                        News::where('n_id',$v->n_id)->increment('n_sort');
                        $value.="新闻标题:".$v->n_title."新闻作者:".$v->n_people."新闻内容:".$v->n_content."\n";
                    }
                    Wechat::reponseText($xmlObj,$value);
                }
            }else{
                $value="暂无相关新闻";
                Wechat::reponseText($xmlObj,$value);
            }
        }
        //取关回复
        if($xmlObj->MsgType=="event" && $xmlObj->Event=="unsubscribe"){
            $res=Wechat::getUserInfoByOpenId($xmlObj->FromUserName);  //获取用户信息  调接口
            //获取渠道标识 根据openid
            $openid=$res['openid'];
            $channel_status=Users::where(['openid'=>$openid])->get('channel_status')->first()->toArray();

            //根据渠道标识 关注人数递减
            Channel::where(['channel_status'=>$channel_status])->decrement('num');

            //条件没有删除 用户openid查询用户表
            $where=[
                ['is_del','=',1],
                ['openid','=',$openid]
            ];
            $res=Users::where($where)->first()->toArray();
            //关注过将is_del改为2 删除信息
            if($res){
                Users::where(['openid'=>$openid])->update(['is_del'=>2]);
              }
        }

    }
    //创建菜单  一级/二级菜单
    public function createMenu()
    {
    //echo date("Y-m-d H:i:s");echo '</br>';
    //调接口
    $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . Wechat::getAccessToken();
    //传递菜单数据
    $menu=[
       "button"  => [
           [
               "type"  => "view",
               "name"  => "签到",
               "url"  => "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx98fcb5c895c99886&redirect_uri=http%3A%2F%2F1906liqianqian.comcto.com%2Fwx%2Fauth&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect"
           ],
           [
               "name"  => "功能开发",
               "sub_button"=>[
                   [
                       "type"  => "view",
                       "name"  => "百度",
                       "url"   => "http://www.baidu.com/"
                   ],
                   [
                       "type"  => "view",
                       "name"  => "京东",
                       "url"   => "http://www.jd.com/"
                   ],
                   [
                       "type"  => "pic_photo_or_album",
                       "name"  => "拍照或相册发图",
                       "key"   => "photo"
                   ],
                   [
                       "type"  => "scancode_push",
                       "name"  => "扫一扫",
                       "key"  => "scan111"
                   ],
                   [
                       "type"  => "pic_weixin",
                       "name"  => "微信相册发图",
                       "key"  =>  "photo"
                   ]
               ]
           ],
//           [
//               "name"  => "发送位置",
//               "type"  => "location_select",
//               "key"   => "location"
//           ]
       ]
    ];
    //中文菜单名 40033 JSON_UNESCAPED_UNICODE
    $menu=json_encode($menu,JSON_UNESCAPED_UNICODE);
    //发送post请求
    $output=Curl::Post($url,$menu);   //json数据包 返回错误码 错误信息
    //转化成数组
    $arr = json_decode($output,true);
    var_dump($arr);
}
    //下载图片/微信/语音到微信服务器
    protected function downLoadImg($mediaId,$msgType,$xmlObj){
        //获取access_token
        $access_token=Wechat::getAccessToken();
        //调接口
        $url="https://api.weixin.qq.com/cgi-bin/media/get?access_token=".$access_token."&media_id=".$mediaId;
        //发请求
        $query=file_get_contents($url);
        if($msgType=='image'){
            //生成随机图片名字
            $name=date("YmdHis").rand(1111,9999).'.jpg';
            //图片/视频存放路径
            $imgUrl="img/".$name;
            //写入文件
            file_put_contents($imgUrl,$query);
        }else if($msgType=='video'){
            //生成随机视频名字
            $name=date("YmdHis").rand(1111,9999).'.mp4';
            //图片/视频存放路径
            $imgUrl="video/".$name;
            //写入文件
            file_put_contents($imgUrl,$query);
        }else if($msgType=='voice'){
             //生成随机语音名字
             $name=date("YmdHis").rand(1111,9999).'.mp3';
            //图片/视频存放路径
            $imgUrl="voice/".$name;
            //写入文件
            file_put_contents($imgUrl,$query);
        }else if($msgType=='text'){
            $data['openid']=$xmlObj->ToUserName;
            $data['username']=$xmlObj->FromUserName;
            $data['content']=$xmlObj->Content;
            $data['create_time']=$xmlObj->CreateTime;
            $res=Message::insert($data);
        }
    }
    //群发消息 通过 用户表openid
    public function groupSending(){
        //查询用户表的openid实现群发
        $where=[
            ['is_del','=',1]
        ];
        $open_list=Users::where($where)->get('openid')->toArray();
        $open_list=array_column($open_list,'openid');
        //调接口
        $url="https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token=".Wechat::getAccessToken();
        //post数据
        $postData=[
            "touser" => $open_list,
            "msgtype" => "text",
            "text" => [
                "content" =>date('Y-m-d H:i:s'). "谢谢您关注"
            ]
        ];
        //转为字符串 第二个参数为中文格式
        $postData=json_encode($postData,JSON_UNESCAPED_UNICODE);
        //发送post请求
        $output=Curl::Post($url,$postData);   //json数据包 返回错误码 错误信息
        //转化为数组
        $output=json_decode($output,true);
        //错误码为0 成功
       if($output['errcode']==0){
           echo "发送成功";
       }else{
           //返回错误信息
           echo '错误信息： ' . $output['errmsg'];
       }
    }
    public function test(){
        //获取code  需scope为 snsapi_userinfo
        $appid=env('WC_APPID');
        $redirect_uri=urlencode(env('WC_AUTH_REDIRECT_URI'));
        //请求接口
        $url='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
        echo $url;
    }
    /**
     *接收网页授权code
    */
    public function auth(){
        //接收code
        $code=$_GET['code'];
        //换取access_token 请求接口
        $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('WC_APPID').'&secret='.env('WC_APPSEC').'&code='.$code.'&grant_type=authorization_code';
        //请求方式
        $json_data=file_get_contents($url);
        //转为数组
        $arr=json_decode($json_data,true);
        echo '<pre>';print_r($arr);echo '</pre>';

        /*
         * Array
            (
                [access_token] => 29_xG8A1usJaZvkL3HtDOqdOTlHSk3HyeegjWGsMY7MSrOcL2Kkjq1jkFNc2ON43rO33XUzRo_SgR4lH58JnF2fvg
                [expires_in] => 7200
                [refresh_token] => 29_1R7R_MwyFIR3OReUbqAevPodEQvtv_HvLgsju2vU3Ypu-9gUdaQn-KJNqbNOnBudExWnAjKHmRmNgoMrFbHqrw
                [openid] => oc_ZXv_Sb5N2seYTwQTOeylWHUxw
                [scope] => snsapi_userinfo
            )
         */


        //获取用户信息 根据openid和access_token 请求接口
        $url='https://api.weixin.qq.com/sns/userinfo?access_token='.$arr['access_token'].'&openid='.$arr['openid'].'&lang=zh_CN';
        //请求方式
        $json_user_info=file_get_contents($url);
        //转为数组
        $user_info_arr=json_decode($json_user_info,true);
        echo '<pre>';print_r($user_info_arr);echo '</pre>';


        //将用户信息保存至Redis Hash
        $key='h:user_info:'.$user_info_arr['openid'];
        Redis::Hmset($key,$user_info_arr);  //设置哈希表
        echo "<prev>";print_r($user_info_arr);echo "</prev>";


        //实现签到功能 记录用户签到
        //Redis 有序集合
        $redis_key='checkin:'.date('Y-m-d');
        //将openid加入有序集合
        Redis::Zadd($redis_key,time(),$user_info_arr['openid']);
        echo $user_info_arr['nickname'].'签到成功'.'签到时间:'.date('Y-m-d H:i:s');echo '<hr>';
        //返回所有openid
        $user_list=Redis::zrange($redis_key,0,-1);
        //echo "<hr>";
        //echo "<prev>";print_r($user_list);echo "</prev>";

        //显示所有用户头像
        foreach($user_list as $k=>$v){
            $key='h:user_info:'.$v;  //取出openid
            $u=Redis::hGetAll($key);  //取所有键 值
            if(empty($u)){
                continue;
            }
            //显示头像
            echo "<img src='".$u['headimgurl']."'>";
        }
    }

    public function gitpull(){
        $git="cd /data/wwwroot/default/1907wx && git pull ";
        shell_exec($git);
    }

}
