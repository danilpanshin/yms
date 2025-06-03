@extends('layout.app')

@section('title', 'Панель администрирования')

@section('content')
    <div class="row">
        <div class="h2">Справочники</div>
        @foreach([
            ['name' => 'Типы авто', 'link' => route('admin.dictionary.car_type'), 'icon' => ''],
            ['name' => 'Типы приемки', 'link' => route('admin.dictionary.acceptance'), 'icon' => ''],
            ['name' => 'Ворота', 'link' => route('admin.dictionary.gate'), 'icon' => ''],
        ] as $unit)
        <div class="col-auto p-2">
            <a href="{{ $unit['link'] }}" class="btn btn-primary mb-2 px-5">
                <i class="bi bi-car-front text-center p-0 m-0" style="font-size: 16px;"></i>
                {{ $unit['name'] }}
            </a>
        </div>
        @endforeach

        <div class="h2">Настройки</div>
        @foreach([
            ['name' => 'Пользователи', 'link' => route('admin.user'), 'icon' => ''],
            ['name' => 'Настройки', 'link' => route('admin.settings'), 'icon' => ''],
        ] as $unit)
            <div class="col-auto p-2">
                <a href="{{ $unit['link'] }}" class="btn btn-primary mb-2 px-5">
                    <i class="bi bi-car-front text-center p-0 m-0" style="font-size: 16px;"></i>
                    {{ $unit['name'] }}
                </a>
            </div>
        @endforeach
    </div>
@endsection