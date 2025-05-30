@extends('layout.app')

@section('content')
    @php
        $auth_data = [
            'lll_admin' => 'iZx7CWi88dycGhjWEMn8qmeYQFB4erFv',
            'admin' => 'ZxRu74OzHqKcL9iXg9qpJb9mOlBTeTEk',
            'supplier' => 'eIvqwlEi7yce1KSVKf3M2onNkjD99vah',
            'driver' => 'uwcemQUDzMbFm4ozyC9TlOTypuIJY9pJ',
            'manager' => 'x64QSLkYuKsNsak8OipjBxXHnXxqd1Xq',
            'stock_admin' => 'dAmyn7Whnw1XqtNr1Dgh45c4rZp8fShL',

            'supplier_1' => 'FhOOqR3nQSa2zrsA0Kq0s4S78SkbIM2X',
            'supplier_2' => 'szWDCX1mvaTA5vv4leHidfcuFBUv8nip',
            'supplier_3' => '9eYbpe94hi48YVb2mal1gPECDnZXGFpI',
            'supplier_4' => 'VqmklvaSaSnNG3pJ2o6nXYgn1j1eTvgB',
            'supplier_5' => 'ktIp0G4ckb47L5HzwkZFVAoAx0xe2sYj',
            'supplier_6' => 'KO7y86IW7cbcC8QMElDV2T0Jm37PF3ug',
            'supplier_7' => 'SG0vP9IySaRTy8NMOf2E9p5OQ2mtWAIW',
            'supplier_8' => 'upDeJXhbW9RJPuDLsvuOUpveefWUltCM',
            'supplier_9' => 'n9ong9KCyriRNf9J30aMJnGT2lJfm23W',
            'supplier_10' => 'qsvVqPhhgddylplplwHfbHGZzqJnUIlV',
            'supplier_11' => 'XcC0ghlsJn0qlLwxodvPv7JfYxbYaoQg',
            'supplier_12' => 'gnAp4t1UVVh4Svypqf1pIpXWjmcvFnyN',
            'supplier_13' => 'GR67GyoDhtzX54UslJztd63XafYKYNrq',
            'supplier_14' => 'CgJRdPxNQFsDbwJrN0GM4a0O5accNqp4',
            'supplier_15' => '6qFDAsVuDuJLHYwtlQc44ZPkIQYefc4i',
            'supplier_16' => 'RVI77SJx4WVhjxK1JAIOD1Rir94esGud',
            'supplier_17' => 'FU56jR2reNZcN0puHWeBY3N67FXO7S37',
            'supplier_18' => 'TlKL8t9Tjl3sjq45YWTzVFVtv5EY0ttq',
            'supplier_19' => '2b34SM6O7s8mBURyX9jbZDOnXZlfqy3z',
            'supplier_20' => 'AmkPCaR07qctvA4mVBV8sMkhUunowxZ1',
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