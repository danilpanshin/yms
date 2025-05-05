<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta name="description" content=""/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="author" content=""/>
    <title>3L YMS - @yield('title')</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="/favicon.ico"/>
    <!-- Bootstrap icons-->
    <link href="/assets/b/fonts/bootstrap-icons.css" rel="stylesheet"/>
    <!-- fileinput CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.8/css/fileinput.min.css" rel="stylesheet"/>
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="/assets/b/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="/assets/css/styles.css?t_={{ filemtime(public_path() . '/assets/css/styles.css') }}" rel="stylesheet"/>
    <!-- jQuery core JS-->
    <script src="/assets/js/jquery.min.js"></script>
    <!-- fileinput JS -->
    <script src="/assets/js/buffer.min.js" type="text/javascript"></script>
    <script src="/assets/js/filetype.min.js" type="text/javascript"></script>
    <script src="/assets/js/piexif.min.js" type="text/javascript"></script>
    <script src="/assets/js/sortable.min.js" type="text/javascript"></script>
    <script src="/assets/b/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/fileinput.min.js"></script>
    <script src="/assets/js/fileinput_locales.js"></script>
    <!-- Bootstrap core JS-->

    <!-- Own JS-->
    <script src="/assets/js/scripts.js"></script>
</head>
<body>
<!-- Navigation-->
<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top print-hide">
    <div class="container px-4">
        <a class="navbar-brand" href="/" style="font-size: 36px">
            <img src="/assets/images/logo.svg" alt="" style="height: 60px"/>
            YMS
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
{{--                <li class="nav-item">--}}
{{--                    <a class="nav-link @if(request()->routeIs('item.list')) active @endif" aria-current="page"--}}
{{--                       href="{{ route('item.list') }}">Список</a>--}}
{{--                </li>--}}
{{--                <li class="nav-item">--}}
{{--                    <a class="nav-link @if(request()->routeIs('item.new')) active @endif" aria-current="page"--}}
{{--                       href="{{ route('item.new') }}">Добавить</a>--}}
{{--                </li>--}}
{{--                <li class="nav-item">--}}
{{--                    <a class="nav-link @if(request()->routeIs('statistics')) active @endif" aria-current="page"--}}
{{--                       href="{{ route('statistics') }}">Статистика</a>--}}
{{--                </li>--}}
{{--                <li class="nav-item">--}}
{{--                    <a class="nav-link @if(request()->routeIs('log')) active @endif" aria-current="page"--}}
{{--                       href="{{ route('log') }}">Активность</a>--}}
{{--                </li>--}}
{{--                @if (auth()->check() && (auth()->user()->is_manager() || auth()->user()->is_admin()))--}}
{{--                    <li class="nav-item">--}}
{{--                        <a class="nav-link @if(request()->routeIs('item.all_xls')) active @endif" aria-current="page"--}}
{{--                           href="{{ route('item.all_xls') }}">Скачать</a>--}}
{{--                    </li>--}}
{{--                @endif--}}
                @if (auth()->check() && auth()->user()->is_admin())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle @if(request()->routeIs([
                                'admin', 'admin.log', 'admin.setting', 'admin.settings', 'admin.statistics',
                                'admin.user.list', 'admin.user.add', 'admin.user.edit'
                            ])) active @endif" data-bs-toggle="dropdown" href="#" role="button">Админка</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('admin.user.list') }}">Пользователи</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.settings') }}">Настройки</a></li>
                        </ul>
                    </li>
                @endif

            </ul>
            @if (auth()->check())
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                        >
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="return false;">Профиль</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" id="logout_form" action="{{ route('logout') }}">
                                    @csrf
                                </form>
                                <a class="dropdown-item" href="#" onclick="$('#logout_form').submit(); return false;">{{ __('Log Out') }}</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            @endif
        </div>
    </div>
</nav>
<!-- Product section-->
<section class="py-2 pt-5 print-main-block" style="min-height: calc( 100vh - 105px ); margin-top: 50px;">
    <div class="container-fluid px-5">
        <div class="row align-items-center">
            <h1 class="pt-4 pb-4">@yield('title')</h1>
            <!-- Page Content -->
            <div class="row">
                <div class="col-12">
                    @if (session()->has('success'))
                        <div class="alert alert-success">
                            <span class="alert_close_btn" onclick="this.parentElement.style.display='none';">&times;</span>
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session()->has('message'))
                        <div class="alert alert-info">
                            <span class="alert_close_btn" onclick="this.parentElement.style.display='none';">&times;</span>
                            {{ session('message') }}
                        </div>
                    @endif
                    @if (session()->has('info'))
                        <div class="alert alert-info">
                            <span class="alert_close_btn" onclick="this.parentElement.style.display='none';">&times;</span>
                            {{ session('info') }}
                        </div>
                    @endif
                    @if (session()->has('warning'))
                        <div class="alert alert-warning">
                            <span class="alert_close_btn" onclick="this.parentElement.style.display='none';">&times;</span>
                            {{ session('warning') }}
                        </div>
                    @endif
                    @if (session()->has('danger'))
                        <div class="alert alert-danger">
                            <span class="alert_close_btn" onclick="this.parentElement.style.display='none';">&times;</span>
                            {{ session('danger') }}
                        </div>
                    @endif
                </div>
            </div>
            @yield('content')
        </div>
    </div>
</section>
<div class="footer bg-light mb-0 p-2" style="height: 40px; margin-top: 15px;">
    <div class="container">
        <div class="row">
            <div class="text-gray-800 text-start col-sm-12 col-md-6">
                Copyright &copy; 3LogicGroup @if(date('Y') != '2025')2025-{{ date('Y') }}@else{{ date('Y') }}@endif
            </div>
            <div class="text-gray-800 text-end col-sm-12 col-md-6">
                <span style="font-size: 10px; padding-left: 20px;">
                    gen: {{ sprintf("%01.4f", round(microtime(true) - LARAVEL_START, 4)) }} |
                    ver: {{ date('d-m-Y H:i', (int)file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/../time_commit')) }}
                </span>
            </div>
        </div>
    </div>
</div>
@yield('script')
</body>
</html>
