<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\News;
use App\Tools\Wechat;
class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       // echo Wechat::createQrcode();die;
        //echo Wechat::getAccessToken();die;
        $pageSize=config('app.pageSize');
        $n_title=request()->n_title;
        $n_people=request()->n_people;
        $where=[];
        if($n_title!==false){
            $where[]=['n_title','like',"%$n_title%"];
        }
        if($n_people||$n_people=='0'){
            $where[]=['n_people','=',$n_people];
        }
        $data=News::orderBy('n_id','desc')->where($where)->paginate($pageSize);
        $query=request()->all();
        return view('news.index',['data'=>$data,'query'=>$query]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('news.create');
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
        $data['n_time']=time();
        $res=News::insert($data);
        if($res){
            echo "<script>alert('添加成功');location.href='news_show';</script>";die;
        }else{
            echo "<script>alert('添加失败');location.href='news_add';</script>";die;
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
        $data=News::find($id);
//        if($data){
//            $data=News::where('n_id',$id)->increment('n_sort');
//        }
        return view('news.edit',['data'=>$data]);
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
        $data=$request->all();
        $res=News::where('n_id',$id)->update($data);
        if($res!==false){
            echo "<script>alert('编辑成功');location.href='/news/news_show';</script>";die;
        }else{
            echo "<script>alert('编辑失败');location.href='/news/news_edit';</script>";die;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $res=News::destroy($id);
        if($res){
            echo "<script>alert('删除成功');location.href='/news/news_show';</script>";die;
        }else{
            echo "<script>alert('删除失败');location.href='/news/news_show';</script>";die;
        }
    }
}
