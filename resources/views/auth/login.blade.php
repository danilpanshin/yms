@extends('layout.app')

@section('content')
    @php
        $auth_data = [
            'lll_admin' => 'XAp9VzvnQubG8ogczTFdiUDDffk0t0qE',
            'admin' => '1yAeTLmTefzd0o7VYMS5TZ8riGBvMH0a',
            'driver' => 'aOyjQgmBpjWxBjlwgRqJLQydoknhAGhB',
            'manager' => '5PR9VtVwr3zFtTvvK4rHp1wSPnf6GxEQ',
            'stock_admin' => 'AgvKdya2dabM3IYLAwLjw4RE4N3Dp72O',
            'supplier' => 'PomER5dn4e51PdptCf6Xg0Nf951Sg4qj',
            'supplier_1' => 'Ncd22juEjP3UkukJK43WO73jtZQtcVfN',
            'supplier_12' => 'qzaX0620iFdLYY5JrHszeg7V3mB9qKgR'
        ];
    @endphp


    <form action="" method="post">
        @csrf
        <div class="row text-center justify-content-center">
            <div class="mb-3 col-12 .col-xl-3 .col-lg-4 col-md-6 col-sm-12 col-xs-12">
                <label for="InputLogin1" class="form-label">Логин</label>
                <input name="name" type="text" class="form-control" id="InputLogin1">
            </div>
        </div>
        <div class="row text-center justify-content-center">
            <div class="mb-3 col-12 .col-xl-3 .col-lg-4 col-md-6 col-sm-12 col-xs-12">
                <label for="InputPassword1" class="form-label">Пароль</label>
                <input name="password" type="password" class="form-control" id="InputPassword1">
            </div>
        </div>
        <div class="row text-center justify-content-center">
            <div class="mt-4 mb-4 col-12 .col-xl-3 .col-lg-4 col-md-6 col-sm-12 col-xs-12">
                <select class="form-select userAuthSelect">
                    @foreach($auth_data as $key => $row)
                        <option data-pass="{{ $row }}" data-login="{{ $key }}">{{ $key }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row text-center justify-content-center">
            <div class="mb-3 col-12 .col-xl-3 .col-lg-4 col-md-6 col-sm-12 col-xs-12">
                <button type="submit" class="btn btn-primary">Войти</button>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script>
        $(document).ready(function(){
            $('.userAuthSelect').on('change', function(){
                $ad = $(this).find(":selected");
                $('#InputPassword1').val($ad.data('pass'));
                $('#InputLogin1').val($ad.data('login'));
            });
        });
    </script>
@endsection