@extends('layouts.admin')
@section('title', '素材管理--添加')
@section('content')
    <h2>添加素材</h2><a href="{{url('wechat/media_show')}}">素材展示</a>
    <form action="{{url('/wechat/media_store')}}" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="exampleInputEmail1">素材名称</label>
            <input type="text" class="form-control" id="exampleInputEmail1" placeholder="素材名称" name="media_name">
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">素材类型</label>
            <select name="media_type"class="form-control" id="exampleInputPassword1">
                <option value="1">临时素材</option>
                <option value="2" >永久素材</option>
            </select>
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">素材格式</label>
            <select name="media_format"class="form-control" id="exampleInputPassword1">
                <option value="image">图片</option>
                <option value="voice" >语音</option>
                <option value="video" >视频</option>
                <option value="thumb" >缩略图</option>
            </select>
        </div>
        <div class="form-group">
            <label for="exampleInputFile">素材文件</label>
            <input type="file" id="exampleInputFile" name="file">
        </div>
        <button type="submit" class="btn btn-default">添加</button>
    </form>
@endsection
