@extends('layouts.admin')
@section('title', '渠道管理--添加')
@section('content')
    <h2>渠道添加</h2><a href="{{url('channel/channel_show')}}">渠道展示</a>
    <form action="{{url('channel/store')}}" method="post">
        <div class="form-group">
            <label for="exampleInputEmail1">渠道名称</label>
            <input type="text" class="form-control" id="exampleInputEmail1" placeholder="渠道名称" name="channel_name">
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">渠道标识</label>
            <input type="text" class="form-control" id="exampleInputEmail1" placeholder="渠道标识" name="channel_status">
        </div>
        <button type="submit" class="btn btn-default">添加</button>
    </form>
@endsection

