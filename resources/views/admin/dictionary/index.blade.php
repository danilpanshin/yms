@extends('layout.app')

@section('title', 'Справочники')

@section('content')
    <div class="row">
        <div class="col-4 p-2">
            <div class="card text-center">
                <i class="bi bi-car-front text-center p-0 m-0" style="font-size: 180px;"></i>
                <div class="card-body p-0 m-0">
                    <h5 class="card-title fs-3">Типы авто</h5>
                    <p class="card-text">Список типов авто с параметрамми</p>
                    <a href="{{ route('admin.dictionary.car_type') }}" class="btn btn-primary mb-2 px-5">Настроить</a>
                </div>
            </div>
        </div>

        <div class="col-4 p-2">
            <div class="card text-center">
                <i class="bi bi-signpost text-center p-0 m-0" style="font-size: 180px;"></i>
                <div class="card-body p-0 m-0">
                    <h5 class="card-title fs-3">Типы приемки</h5>
                    <p class="card-text">Список типов приемки с параметрамми</p>
                    <a href="{{ route('admin.dictionary.acceptance') }}" class="btn btn-primary mb-2 px-5">Настроить</a>
                </div>
            </div>
        </div>

        <div class="col-4 p-2">
            <div class="card text-center">
                <i class="bi bi-house-door text-center p-0 m-0" style="font-size: 180px;"></i>
                <div class="card-body p-0 m-0">
                    <h5 class="card-title fs-3">Ворота</h5>
                    <p class="card-text">Список ворот на складе с параметрамми</p>
                    <a href="{{ route('admin.dictionary.gate') }}" class="btn btn-primary mb-2 px-5">Настроить</a>
                </div>
            </div>
        </div>
    </div>
@endsection