<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta name="description" content=""/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="author" content=""/>
    <title>3L YMP - @yield('title')</title>
    <!-- Favicon-->
    <link rel="icon" type="image/png" href="/favicon.png"/>
    <!-- Bootstrap icons-->
    <link href="/assets/b/fonts/bootstrap-icons.css" rel="stylesheet"/>
    <!-- fileinput CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.8/css/fileinput.min.css"
          rel="stylesheet"/>
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
    <script src="{{ asset('/assets/js/select2.js') }}"></script>
    <!-- Bootstrap core JS-->

    <!-- Own JS-->
    <script src="/assets/js/scripts.js"></script>
    <style>
        .select2-container {
            width: auto !important;
        }

        nav svg{ width: 20px; }
        .sm\:hidden{ display: none; }

    </style>
</head>
<body>
<!-- Navigation-->
<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top print-hide">
    <div class="container px-4">
        <a class="navbar-brand" href="/" style="font-size: 36px">
            <img src="/assets/images/logo.svg" alt="" style="height: 60px"/>
            YMP
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            @if (auth()->check())
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    @if (auth()->check() && auth()->user()->is_supplier())
                        <li class="nav-item">
                            <a class="nav-link @if(request()->routeIs('supplier')) active @endif" aria-current="page"
                               href="{{ route('supplier') }}">Личный кабинет</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link @if(request()->routeIs('supplier.claim')) active @endif" aria-current="page"
                               href="{{ route('supplier.claim') }}">Заявки</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link @if(request()->routeIs('supplier.driver')) active @endif" aria-current="page"
                               href="{{ route('supplier.driver') }}">Водители</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link @if(request()->routeIs('supplier.expeditor')) active @endif" aria-current="page"
                               href="{{ route('supplier.expeditor') }}">Экспедиторы</a>
                        </li>

                        <li class="nav-item">
                            <a class="disabled nav-link @if(request()->routeIs('supplier.car')) active @endif" aria-current="page"
                               href="{{ route('supplier.car') }}">Авто (в разработке)</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link @if(request()->routeIs('supplier.profile')) active @endif" aria-current="page"
                               href="{{ route('supplier.profile') }}">Профиль</a>
                        </li>

                    @endif

                    @if (auth()->check() && auth()->user()->is_stock_admin())
                        <li class="nav-item">
                            <a class="nav-link @if(request()->routeIs('stock_admin')) active @endif" aria-current="page"
                               href="{{ route('stock_admin') }}">Личный кабинет</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link @if(request()->routeIs(['stock_admin.claim', 'stock_admin.claim.add'])) active @endif" aria-current="page"
                               href="{{ route('stock_admin.claim') }}">Заявки</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link @if(request()->routeIs(['stock_admin.supplier', 'stock_admin.supplier.add'])) active @endif" aria-current="page"
                               href="{{ route('stock_admin.supplier') }}">Поставщики</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link @if(request()->routeIs('stock_admin.driver')) active @endif" aria-current="page"
                               href="{{ route('stock_admin.driver') }}">Водители</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link @if(request()->routeIs('stock_admin.expeditor')) active @endif" aria-current="page"
                               href="{{ route('stock_admin.expeditor') }}">Экспедиторы</a>
                        </li>

                    @endif

                    @if (auth()->check() && auth()->user()->is_admin())
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle @if(request()->routeIs([
                                'admin', 'admin.log', 'admin.setting', 'admin.settings', 'admin.statistics',
                                'admin.user.list', 'admin.user.add', 'admin.user.edit'
                            ])) active @endif" data-bs-toggle="dropdown" href="#" role="button">Админка</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('admin') }}">Панель</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="{{ route('admin.user') }}">Пользователи</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.dictionary') }}">Справочники</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="{{ route('admin.settings') }}">Настройки</a></li>
                            </ul>
                        </li>
                    @endif
                    @if(auth()->check() && auth()->user()->is_manager())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('manager') }}">
                                <i class="bi bi-gear"></i> Панель менеджера
                            </a>
                        </li>
                    @endif
                </ul>
                <ul class="navbar-nav mb-2 mb-lg-0 ms-lg-4 justify-content-end">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                        >
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                            @if (auth()->check() && auth()->user()->is_supplier())
                                <li><a class="dropdown-item" href="{{ route('supplier.profile') }}">Профиль</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                            @endif
                            <li>
                                <form method="POST" id="logout_form" action="{{ custom_secure_url(route('logout', [], false)) }}">
                                    @csrf
                                </form>
                                <a class="dropdown-item" href="#"
                                   onclick="$('#logout_form').submit(); return false;">{{ __('Log Out') }}</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            @endif
        </div>
    </div>
</nav>
<!-- Product section-->
<section class="py-2 pt-5 print-main-block" style="min-height: calc( 100vh - 85px ); margin-top: 50px;">
    <div class="container-fluid px-5">
        <div class="row align-items-center">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <h1 class="">@yield('title')</h1>
                </div>
                <div class="col-12 col-sm-6">
                    @php
                        $names = [
                            'admin' => 'Админ панель',
                            'dictionary' => 'Справочники',
                            'gate' => 'Ворота',
                            'car_type' => 'Типы авто',
                            'acceptance' => 'Типы приемки',
                            'supplier' => 'Поставщик',
                            'stock_admin' => 'Старший смены',
                            'claim' => 'Заявки',
                        ];
                        $path = Request::path();
                        $bc = [];
                        $all_bc = [];
                        foreach(explode('/', $path) as $path_row){
                            $all_bc[] = $path_row;
                            if(Route::has(implode('.',$all_bc))){
                                $bc[] = [
                                    'path' => implode('.',$all_bc),
                                    'name' => $path_row,
                                ];
                            }
                        }
                        array_pop($bc);
                    @endphp
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-end">
                            @foreach($bc as $bc_row)
                                <li class="breadcrumb-item @if(Route::is($bc_row['path'])) active @endif"><a href="{{ route($bc_row['path']) }}">{{ $names[$bc_row['name']] ?? $bc_row['name'] }}</a></li>
                            @endforeach
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- Page Content -->
            <div class="row">
                <div class="col-12">
                    @if ($errors->any())
                        <div class="alert alert-danger p-0 m-1">
                            <ul class="list-unstyled p-2 m-0">
                                @foreach ($errors->all() as $error)
                                    <li class="p-0 m-1">{{ trim($error) }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (session()->has('success'))
                        <div class="alert alert-success">
                            <span class="alert_close_btn"
                                  onclick="this.parentElement.style.display='none';">&times;</span>
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session()->has('message'))
                        <div class="alert alert-info">
                            <span class="alert_close_btn"
                                  onclick="this.parentElement.style.display='none';">&times;</span>
                            {{ session('message') }}
                        </div>
                    @endif
                    @if (session()->has('info'))
                        <div class="alert alert-info">
                            <span class="alert_close_btn"
                                  onclick="this.parentElement.style.display='none';">&times;</span>
                            {{ session('info') }}
                        </div>
                    @endif
                    @if (session()->has('warning'))
                        <div class="alert alert-warning">
                            <span class="alert_close_btn"
                                  onclick="this.parentElement.style.display='none';">&times;</span>
                            {{ session('warning') }}
                        </div>
                    @endif
                    @if (session()->has('danger'))
                        <div class="alert alert-danger">
                            <span class="alert_close_btn"
                                  onclick="this.parentElement.style.display='none';">&times;</span>
                            {{ session('danger') }}
                        </div>
                    @endif
                </div>
            </div>
            @yield('content')
        </div>
    </div>
</section>
<div class="footer bg-light mb-0 p-2" style="height: 35px;">
    <div class="container">
        <div class="row">
            <div class="text-gray-800 text-start col-sm-12 col-md-6">
                Copyright &copy; 3LogicGroup @if(date('Y') != '2025')
                    2025-{{ date('Y') }}
                @else
                    {{ date('Y') }}
                @endif
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
<script>
    function sendAjaxForm(e, form, callback){
        e.preventDefault();

        const actionUrl = form.attr('action');

        let loader = $(form).closest('.modal-body').find('.spinner-border');
        let errors_div = $(form).closest('.modal-body').find('.errors');

        $(loader).show();
        $(form).hide();

        function parseResp(data){
            console.log(data)
            let errors_text_arr = [];
            if (typeof data.message !== "undefined") {
                if(data.message === 'ok' || data.message === true){
                    callback(true)
                } else {
                    if (typeof data.errors !== "undefined" && data.errors) {
                        errors_text_arr.push('<h4>Произошла ошибка при сохранении</h4>')

                        $.each(data.errors, function (error_name, error_list) {
                            errors_text_arr.push('<b class="col-2">' + error_name + '</b> <span class="col-10">' + error_list.join(' ') + '</span>')
                        });
                    }
                    $(errors_div).html('<div class="bg-warning p-3 m-1 row">' + errors_text_arr.join('<br>') + '</div>');
                    callback(false)
                }
            } else {
                errors_text_arr.push('Неизвестная ошибка сервера');
                $(errors_div).html('<div class="bg-warning p-3 m-1 row">' + errors_text_arr.join('<br>') + '</div>');
                callback(false)
            }
        }

        $.ajax({
            type: "POST",
            url: actionUrl,
            data: form.serialize(),
            dataType: 'json',
            success: function(data)
            {
                parseResp(data)
            },
            error: function($xhr) {
                const data = $xhr.responseJSON;
                parseResp(data)
            },
            complete: function(){
                $(loader).hide();
                $(form).show();
            }
        });
    }
</script>
</body>
</html>
