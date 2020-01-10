@extends('layouts.admin')
@section('title', '新闻模块--展示')
@section('content')
    <h2>展示新闻</h2><a href="{{url('news/news_add')}}">添加新闻</a>
    <form action="" method="">
        <input type="text" placeholder="标题关键字" value="{{$query['n_title']??''}}" name="n_title">
        <input type="text" placeholder="新闻作者" value="{{$query['n_people']??''}}" name="n_people">
        <input type="submit" value="搜索">
    </form>
    <table class="table table-hover">
        <thead>
        <tr>
            <th>ID</th>
            <th>新闻标题</th>
            <th>新闻内容</th>
            <th>新闻作者</th>
            <th>新闻时间</th>
            <th>访问量</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        @if($data)
            @foreach($data as $v)
                <tr>
                    <td>{{$v->n_id}}</td>
                    <td>{{$v->n_title}}</td>
                    <td>{{$v->n_content}}</td>
                    <td>{{$v->n_people}}</td>
                    <td>{{date("Y-m-d H:i:s",$v->n_time)}}</td>
                    <td>{{$v->n_sort}}</td>
                    <td>
                        <a href="{{url('news/news_destroy/'.$v->n_id)}}" class="btn btn-danger">删除</a>
                        <a href="{{url('news/news_edit/'.$v->n_id)}}" class="btn btn-info">编辑</a>
                    </td>
                </tr>
            @endforeach
        @endif
        <tr><td colspan="7">{{$data->appends($query)->links()}}</td></tr>
        </tbody>
    </table>
@endsection
