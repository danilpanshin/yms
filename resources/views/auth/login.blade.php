@extends('layout.app')

@section('content')
    @php
        $auth_data = [
            'lll_admin' => 'zytWKbjUPEZ0yMyjyfiK2PdEuY4l0Q5n',
            'admin' => 'P8B1Q7FBI48VGETnUQVMSFqUn9jyRLO6',
            'driver' => 'ixPjrO1WmfU2NWdaZNJU9q2OevxY494M',
            'manager' => 'm3EFOLf3k7qC5BaBuE3IXHL28I5HtDJx',
            'stock_admin' => 'B5M9FYSHJQeuVjJS85LlgrgQM7ZZHOf1',
            'supplier_1' => 'nGc8z6jOABC6aCKWWnFdd1W3WbXQpSWZ',
            'supplier_2' => 'sp9YElgyv1Yfwwg1mUvfNcXN2SnxwORV',
            'supplier_3' => 'YEkPchPTevg4UsAyCplYDlAmVI0I6Xnm',
            'supplier_4' => '23v2TUeVfvifz3FBpI2zIoMoBEOPAW1s',
            'supplier_5' => 'BdwHS4lkTkjlLpamM7aABEi4zzYUwvHY',
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