@extends('layout.app')

@section('title', 'Профиль');

@section('content')
    <div class="row my-2">
        <table class="table table-striped table-hover">
            <tr><td class="fw-bold" style="width: 200px;">ID 1</td><td>{{ $data->id }}</td></tr>
            <tr><td class="fw-bold" style="width: 200px;">ID 2</td><td>{{ $data->rs_id }}</td></tr>
            <tr><td class="fw-bold" style="width: 200px;">ID 3</td><td>{{ $data->one_ass_id }}</td></tr>
            <tr><td class="fw-bold" style="width: 200px;">Наименование</td><td>{{ $data->name }}</td></tr>
            <tr><td class="fw-bold" style="width: 200px;">ИНН</td><td>{{ $data->inn }}</td></tr>
            <tr><td class="fw-bold" style="width: 200px;">Телефон</td><td>{{ $data->phone }}</td></tr>
            <tr><td class="fw-bold" style="width: 200px;">Email</td><td>{{ $data->email }}</td></tr>
            <tr><td class="fw-bold" style="width: 200px;">Адрес</td><td>{{ $data->address }}</td></tr>
            <tr><td class="fw-bold" style="width: 200px;">Город</td><td>{{ $data->city }}</td></tr>
            <tr><td class="fw-bold" style="width: 200px;">Регион</td><td>{{ $data->state }}</td></tr>
            <tr><td class="fw-bold" style="width: 200px;">Страна</td><td>{{ $data->country }}</td></tr>
            <tr><td class="fw-bold" style="width: 200px;">Почтовый индекс</td><td>{{ $data->zip }}</td></tr>
        </table>
    </div>
@endsection