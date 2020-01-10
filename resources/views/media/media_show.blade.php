@extends('layouts.admin')

@section('title', '素材管理--展示')
@section('content')
    <h2>展示素材</h2><a href="{{url('/wechat/media_add')}}">添加素材</a>
    <table class="table table-hover">
        <thead>
        <tr>
            <th>素材编号</th>
            <th>素材名称</th>
{{--            <th>素材格式</th>--}}
            <th>素材展示</th>
            <th>类型</th>
            <th>微信服务器media_id</th>
            <th>添加时间</th>
            <th>过期时间</th>
        </tr>
        </thead>
        <tbody>
        @if($data)
            @foreach($data as $v)
        <tr>
            <td>{{$v->media_id}}</td>
            <td>{{$v->media_name}}</td>
{{--            <td>{{$v->media_format}}</td>--}}
            <td>
                @if($v->media_format=='image')
                    <img src="/{{$v->media_url}}"  width="60px">
                    @elseif($v->media_format=='voice')
                    <audio src="/{{$v->media_url}}" controls="controls" width="40px" height="40px"></audio>
                    @elseif($v->media_format=='video')
                    <video src="/{{$v->media_url}}" controls="controls" width="60px"height="50px"></video>
                @endif
            </td>
            <td>{{$v->media_type==1 ? '临时素材' : '永久素材'}}</td>
            <td>{{$v->wechat_media_id}}</td>
            <td>{{date("Y-m-d H:i:s",$v->add_time)}}</td>
            <td>{{date("Y-m-d H:i:s",strtotime('+3 day',$v->add_time))}}</td>
        </tr>
  @endforeach
            @endif
        <tr><td colspan="7">{{$data->links()}}</td></tr>
        </tbody>
    </table>
@endsection
