<!DOCTYPE html>
<html>
<head>
    <base href="/static/wechat/">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> - @yield('title')</title>
    <meta name="keywords" content="">
    <meta name="description" content="">
    <link rel="shortcut icon" href="favicon.ico">
    <link href="css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <!-- 全局js -->
    <script src="js/jquery.min.js?v=2.1.4"></script>
    <script src="js/bootstrap.min.js?v=3.3.6"></script>
</head>

<body class="gray-bg">

<div class="container" style="margin-top:6%">
    @yield('content')
</div>
</body>
</html>
