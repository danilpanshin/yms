@extends('layout.app')

@section('title', 'Поставщики')

@section('content')
    <div class="admin_users_section">
        <div class="row">
            <div class="col-12 col-sm-6 text-start">
                <div>
                    <span class="badge bg-success">Всего {{ $list->total() }}</span>
                    Тут будут фильтры
                </div>
            </div>
            <div class="col-12 col-sm-6 text-end">
                <a href="{{ route('stock_admin.supplier.add') }}" class="btn btn-primary">
                    Добавить
                </a>
            </div>
        </div>

        <div class="row mt-4">
            {{ $list->links('pagination::bootstrap-5') }}
        </div>

        <div class="row mt-2">
            <table class="table table-condensed table-striped rounded-3 overflow-hidden">
                <thead>
                <tr>
                    <td>Номер</td>
                    <td>Наименование</td>
                    <td>Email</td>
                    <td>Телефон</td>
                    <td>Адрес</td>
                    <td>Город</td>
                    <td>Округ</td>
                    <td>Страна</td>
                    <td>Индкес</td>
                    <td>Инн</td>
                    <td>РС ID</td>
                    <td>1С ID</td>
                </tr>
                </thead>
                <tbody>
                @foreach($list as $row)
                    <tr>
                        <td>{{ $row['id'] }}</td>
                        <td>{{ $row['name'] }}</td>
                        <td>{{ $row['email'] }}</td>
                        <td>{{ $row['phone'] }}</td>
                        <td>{{ $row['address'] }}</td>
                        <td>{{ $row['city'] }}</td>
                        <td>{{ $row['state'] }}</td>
                        <td>{{ $row['country'] }}</td>
                        <td>{{ $row['zip'] }}</td>
                        <td>{{ $row['inn'] }}</td>
                        <td>{{ $row['rs_id'] }}</td>
                        <td>{{ $row['one_ass_id'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="row mt-1">
            {{ $list->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection

@section('script')


    <script>
        $('.lllAjaxFormSubmit').on('submit', function(e){
            sendAjaxForm(e, $(this), function(res){ if(res){ document.location.reload(); } });
        });
    </script>
@endsection