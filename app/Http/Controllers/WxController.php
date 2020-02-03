<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WxController extends Controller
{
    public function index(){
        echo $echostr=request()->echostr;die;
    }
}
