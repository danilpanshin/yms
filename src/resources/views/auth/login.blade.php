@extends('layout.app')

@section('content')
    <form action="" method="post">
        @csrf
        <div class="mb-3">
            <label for="InputLogin1" class="form-label">Логин</label>
            <input type="text" class="form-control" id="InputLogin1" aria-describedby="emailHelp">
        </div>
        <div class="mb-3">
            <label for="InputPassword1" class="form-label">Пароль</label>
            <input type="password" class="form-control" id="InputPassword1">
        </div>
        <button type="submit" class="btn btn-primary">Войти</button>
    </form>
@endsection