@extends('layout.app')

@section('content')
    <form action="" method="post">
        @csrf
        <div class="row text-center justify-content-center">
            <div class="mb-3 col-12 .col-xl-3 .col-lg-4 col-md-6 col-sm-12 col-xs-12">
                <label for="InputLogin1" class="form-label">Логин</label>
                <input name="name" type="text" class="form-control" id="InputLogin1" aria-describedby="emailHelp">
            </div>
        </div>
        <div class="row text-center justify-content-center">
            <div class="mb-3 col-12 .col-xl-3 .col-lg-4 col-md-6 col-sm-12 col-xs-12">
                <label for="InputPassword1" class="form-label">Пароль</label>
                <input name="password" type="password" class="form-control" id="InputPassword1">
            </div>
        </div>
        <div class="row text-center justify-content-center">
            <div class="mb-3 col-12 .col-xl-3 .col-lg-4 col-md-6 col-sm-12 col-xs-12">
                <button type="submit" class="btn btn-primary">Войти</button>
            </div>
        </div>
    </form>
@endsection