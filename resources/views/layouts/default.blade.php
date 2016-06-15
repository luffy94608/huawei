<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <title>@yield('title','我的服务')</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="/bower_components/weui/dist/style/weui.css"/>
    <link rel="stylesheet" href="/styles/app.css"/>
    <link rel="stylesheet" href="/styles/toast.css"/>

    {{--<link rel="stylesheet" href="/styles/font-awesome.css">--}}
    <script type="text/javascript">
        document.global_config_data = {
            version: '{{Config::get('app')['version']}}',
            page:'{{isset($page) ? $page : ''}}',
            resource_root: '{{Config::get('app')['url']}}',
        };

    </script>

</head>
<body class="@yield('bodyBg','')">

{{--内容区域--}}
@section('content')

@show

{{--模板--}}
@include('templages.weui',[])

<script src='/bower_components/requirejs/require.js' data-main='/scripts/main.js' type='text/javascript'></script>
</body>
</html>
