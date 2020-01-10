@extends('layouts.admin')
@section('title', '渠道管理--展示')
@section('content')
    <h2>渠道展示</h2><a href="{{url('channel/channel_add')}}">添加渠道</a>
    <table class="table table-hover">
        <thead>
        <tr>
            <th>编号</th>
            <th>渠道名称</th>
            <th>渠道标识</th>
            <th>渠道二维码</th>
            <th>关注人数</th>
        </tr>
        </thead>
        <tbody>
        @if($data)
            @foreach($data as $v)
                <tr>
                    <td>{{$v->channel_id}}</td>
                    <td>{{$v->channel_name}}</td>
                    <td>{{$v->channel_status}}</td>
                    <td><img src="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket={{$v->ticket}}" width="100px"></td>
                    <td>{{$v->num}}</td>
                </tr>
            @endforeach
        @endif
        <tr><td colspan="5">{{$data->links()}}</td></tr>
        </tbody>
    </table>
@endsection
