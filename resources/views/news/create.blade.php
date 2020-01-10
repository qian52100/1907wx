@extends('layouts.admin')
@section('title', '新闻模块--添加')
@section('content')
    <h2>添加新闻</h2><a href="{{url('news/news_show')}}">新闻展示</a>
    <form action="{{url('/news/news_store')}}" method="post">
        <div class="form-group">
            <label for="exampleInputEmail1">新闻标题</label>
            <input type="text" class="form-control" id="exampleInputEmail1" placeholder="新闻标题" name="n_title">
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">新闻内容</label>
            <textarea name="n_content"  class="form-control" cols="30" rows="10"></textarea>
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">新闻作者</label>
            <input type="text" class="form-control" id="exampleInputEmail1" placeholder="新闻作者" name="n_people">
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">访问量</label>
            <input type="text" class="form-control" id="exampleInputEmail1" placeholder="访问量" name="n_sort">
        </div>
        <button type="submit" class="btn btn-default">添加</button>
    </form>
@endsection
