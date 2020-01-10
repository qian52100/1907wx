@extends('layouts.admin')
@section('title', '菜单管理--添加')
@section('content')
    <h2>菜单添加</h2><a href="{{url('menu/menu_show')}}">菜单展示</a>
    <form action="{{url('/menu/menu_store')}}" method="post">
        <div class="form-group">
            <label for="exampleInputEmail1">菜单名称</label>
            <input type="text" class="form-control" id="exampleInputEmail1" placeholder="菜单名称" name="media_name">
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">菜单类型</label>
            <select name="media_type"class="form-control" id="exampleInputPassword1">
                <option value="1">点击类型</option>
                <option value="2">跳转类型</option>
            </select>
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">菜单标识(如果选择点击类型,请输入标识,如果选择跳转类型,请输入url地址)</label>
            <input type="text" class="form-control" id="exampleInputEmail1" name="media_name">
        </div>
        <div class="form-group">
            <label for="exampleInputFile">上级菜单</label>
            <select name="media_type"class="form-control" id="exampleInputPassword1">
                <option value="1">一级菜单</option>
                <option value="2">二级菜单</option>
            </select>
        </div>
        <button type="submit" class="btn btn-default">添加</button>
    </form>
@endsection

