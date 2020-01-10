<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Tools\Wechat;
use App\Tools\Curl;
use App\Model\Media;
class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pageSize=config('app.pageSize');
        $data=Media::orderBy('media_id','desc')->paginate($pageSize);
        return view('media.media_show',['data'=>$data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('media.media_add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data=$request->all();
        $file=request()->file;
        if (!$request->hasFile('file')) {
            echo "上传有误";die;
        }
        $ext=$file->getClientOriginalExtension(); //后缀名
        $filename=md5(uniqid()).".".$ext;
        //文件上传的路径
        $path = $file->storeAs('images',$filename);
        //上传临时素材 调接口
        $res=Wechat::uploadMedia($path,$data);
        if(isset($res->media_id)){
            $media_id=$res->media_id;
            $data['wechat_media_id']=$media_id;
        }else{
            echo "<script>alert('上传素材错误');location.href='media_add';</script>";die;
        }
        //返回media_id媒体文件上传后，获取标识
        $data['media_url']=$path;
        $data['add_time']=time();
        unset($data['s']);
        unset($data['file']);
        if($data){
            //入库
            $res=Media::create($data);
            echo "<script>alert('添加成功');location.href='media_show';</script>";die;
        }else{
            echo "<script>alert('添加失败');location.href='media_add';</script>";die;
        }

    }

    /**
     * Display the specified resource.
     *
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
