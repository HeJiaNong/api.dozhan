<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title','dozhan')</title>
    <link rel="stylesheet" type="text/css" href="https://necolas.github.io/normalize.css/latest/normalize.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/main.css') }}">

    <script type="text/javascript" src="{{ asset('js/vue.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vue-resource.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vee-validate.js') }}"></script>

    {{-- meta部分 --}}
    @yield('meta')

</head>

<body>

{{-- 引入nav布局 --}}
@include('layouts.nav')

{{-- 内容部分 --}}
@yield('content')

</body>

{{-- 页脚 --}}
@yield('footer')

</html>