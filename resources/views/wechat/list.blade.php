@extends('layouts.admin')
@section('title', '一周气温展示')
@section('content')
    <h4>一周气温展示</h4>
        <meta charset="utf-8"><link rel="icon" href="https://jscdn.com.cn/highcharts/images/favicon.ico">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            /* css 代码  */
        </style>
        <script src="https://code.highcharts.com.cn/highcharts/highcharts.js"></script>
        <script src="https://code.highcharts.com.cn/highcharts/highcharts-more.js"></script>
        <script src="https://code.highcharts.com.cn/highcharts/modules/exporting.js"></script>
        <script src="https://img.hcharts.cn/highcharts-plugins/highcharts-zh_CN.js"></script>
    <body>
        城市:<input type="text" id="city">
        <input type="button" value="搜索" id="search">(城市名可以为拼音和汉字)
    <div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
    <script>
        //点击搜索按钮
        $("#search").on('click',function(){
            //获取城市名
            var city=$("#city").val();
            if(city==''){
                alert('请填写城市');
                return false;
            }
            //正则验证 只能是汉字或者拼音
            var reg=/^[a-zA-Z]+$|^[\u4e00-\u9fa5]+$/;
            var res=reg.test(city);
            if(!res){
                alert('城市名只能为汉字和拼音');
                return;
            }
            $.ajax({
                url:"{{url('wechat/weather')}}",
                data:{city:city},
                dataType:"json",
                success:function(res){
                    //展示天气图表
                    weather(res.result);
                }
            })
        })
        function weather(weatherDate){
            console.log(weatherDate) //json天气数据
            //拼接日期和气温数组
            var categories=[];   //x轴日期
            var data=[];  //x轴对应的最高气温和最低气温
            $.each(weatherDate,function(i,v){
                //push向数组末尾添加一个或多个元素 返回新的长度 1234567 循环天数
                categories.push(v.days);
                var arr=[parseInt(v.temp_low),parseInt(v.temp_high)];  //最低气温,最高气温
                //循环一周温度 7次
                data.push(arr);
            })

            var chart = Highcharts.chart('container', {
                chart: {
                    type: 'columnrange', // columnrange 依赖 highcharts-more.js
                    inverted: true
                },
                title: {
                    text: '一周温度变化范围'
                },
                subtitle: {
                    text: weatherDate[0]['citynm']
                },
                xAxis: {
                    categories: categories
                },
                yAxis: {
                    title: {
                        text: '温度 ( °C )'
                    }
                },
                tooltip: {
                    valueSuffix: '°C'
                },
                plotOptions: {
                    columnrange: {
                        dataLabels: {
                            enabled: true,
                            formatter: function () {
                                return this.y + '°C';
                            }
                        }
                    }
                },
                legend: {
                    enabled: false
                },
                series: [{
                    name: '温度',
                    data: data
                }]
            });
        }
    </script>
    </body>
@endsection
<script src="/static/jquery.js"></script>
<script>
    //HighCharts图标，js处理数据+ajax，php调用接口，缓存使用，时间处理等
    //一进入页面加入代码，直接发送ajax请求到后台拿到数据，展示默认北京天气
    $.ajax({
        url:"{{url('wechat/weather')}}",
        data:{city:"北京"},
        dataType:"json",
        success:function(res){
            //展示天气图表
            weather(res.result);
        }
    })
</script>


