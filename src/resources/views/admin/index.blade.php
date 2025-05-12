@extends('layout.app')

@section('content')
    <div class="row">
        @foreach([
            ['name' => 'Поставщики', 'link' => '/admin/cars'],
            ['name' => 'Машины', 'link' => '/admin/cars'],
            ['name' => 'Водители', 'link' => '/admin/drivers'],
            ['name' => 'Машины', 'link' => '/admin/cars'],
        ] as $unit)
            <div class="col-auto">
                <a class="btn btn-info p-4 m-2" href="{{ $unit['link']  }}">{{ $unit['name']  }}</a>
            </div>
        @endforeach
    </div>
@endsection