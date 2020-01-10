<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Tools\Wechat;
use App\Model\Channel;
class ChannelController extends Controller
{
    public function list(){
        $data=Channel::get()->toArray();
        $nameStr="";
        $numStr="";
        foreach($data as $key=>$val){
            $nameStr.='"'.$val['channel_name'].'",';
            $numStr.=$val['num'].',';
        }
        //dd($nameStr);
        $numStr=rtrim($numStr,',');
        $nameStr=rtrim($nameStr,',');
        return view('channel.channel_list',['numStr'=>$numStr,'nameStr'=>$nameStr]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        $channel_status='112';
//        echo Wechat::createQrcode($channel_status);die;
        $pageSize=config('app.pageSize');
        $data=Channel::orderBy('channel_id','desc')->paginate($pageSize);
        return view('channel.channel_show',['data'=>$data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('channel.channel_add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //接收值
        $channel_name=$request->channel_name;
        $channel_status=$request->channel_status;
        //渠道标识作为参数 获取ticket生成二维码
        $ticket=Wechat::createQrcode($channel_status);
        //入库
        $data['channel_name']=$channel_name;
        $data['channel_status']=$channel_status;
        $data['ticket']=$ticket;
        $res=Channel::create($data);
        if($res){
            echo "<script>alert('添加成功');location.href='channel_show';</script>";die;
        }else{
            echo "<script>alert('添加失败');location.href='channel_add';</script>";die;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
