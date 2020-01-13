<?php
//echo phpinfo();die;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/* Route::get('/', function () {
    return view('welcome');
});*/
//微信模块

// Route::get('phpinfo', function () {
//    phpinfo();
// });

//微信后台登陆 接口
Route::get('/','Admin\LoginController@login');  //登陆页
Route::post('login/dologin','Admin\LoginController@dologin');  //执行登陆页
Route::any('/wechat/index','WechatController@index');  //微信接口
Route::any('/index/menu','WeixinController@createMenu');  //创建菜单
Route::any('haha','Admin\IndexController@haha');   //获取最新刷新access_token

//素材管理
Route::prefix('wechat/')->group(function () {
    Route::any('/','Admin\IndexController@index');  //首页
    Route::any('media_add','Admin\MediaController@create');  //素材添加
    Route::any('media_store','Admin\MediaController@store');  //素材执行添加
    Route::any('media_show','Admin\MediaController@index');  //素材展示
    Route::any('weaid','Admin\IndexController@list');  //展示一周天气气温 视图
    Route::any('weather','Admin\IndexController@weather');  //获取一周天气气温 调接口
});
//新闻模块
Route::prefix('news/')->group(function () {
    Route::any('news_show','Admin\NewsController@index');  //新闻展示
    Route::any('news_add','Admin\NewsController@create');  //新闻添加
    Route::any('news_store','Admin\NewsController@store');  //新闻执行添加
    Route::any('news_destroy/{id}','Admin\NewsController@destroy');  //新闻删除
    Route::any('news_edit/{id}','Admin\NewsController@edit');  //新闻编辑
    Route::any('news_update/{id}','Admin\NewsController@update');  //新闻执行编辑
    Route::any('index','WeixinController@index');  //微信接口
});
//渠道管理
Route::prefix('channel/')->group(function () {
    Route::any('channel_show','Admin\ChannelController@index');  //渠道展示
    Route::any('channel_add','Admin\ChannelController@create');  //渠道添加
    Route::any('store','Admin\ChannelController@store');  //渠道执行添加
    Route::any('list','Admin\ChannelController@list');  //渠道执行添加

});
//菜单管理
Route::prefix('menu/')->group(function () {
    Route::any('menu_show','Admin\MenuController@index');  //菜单展示
    Route::any('menu_add','Admin\MenuController@create');  //菜单添加
});
Route::any('aaa','WeixinController@groupSending');  //微信群发
Route::get('wx/test','WeixinController@test');  //测试
Route::get('wx/auth','WeixinController@auth');  //接收code


Route::any('wx/gitpull','WeixinController@gitpull');  //接收code
