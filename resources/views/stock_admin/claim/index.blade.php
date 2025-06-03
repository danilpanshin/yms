@extends('layout.app')

@section('title', 'Заявки')

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
                <a href="{{ route('stock_admin.claim.add') }}" class="btn btn-primary">
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
                    <td>Статус</td>
                    <td>Поставщик</td>
                    <td>ФИО Водителя</td>
                    <td>ФИО Экспедитора</td>
                    <td>Дата/Время</td>
                    <td>Номер ТС</td>
                    <td>Тип поставки</td>
                    <td>Тип ТС</td>
                    <td>Г/Б</td>
                    <td>Масса</td>
                    <td>Ворота</td>
                </tr>
                </thead>
                <tbody>
                @foreach($list as $row)
                    <tr>
                        <td>{{ $row['id'] }}</td>
                        <td><span class="badge bg-info">{{ $row['status'] }}</span></td>
                        <td>{{ $row['supplier_name'] }}</td>
                        <td>{{ $row['driver_name'] }}</td>
                        <td>{{ $row['expeditor_name'] }}</td>
                        <td>{{ $row['booking_date']->format('Y.m.d') }} {{ $row['start_time']->format('H:m') }} | {{ $row['end_time']->format('H:m') }}</td>
                        <td>{{ $row['car_number'] }}</td>
                        <td>{{ $row['acceptances_name'] }}</td>
                        <td>{{ $row['car_type_name'] }}</td>
                        <td>{!! $row['gbort'] ? '<span class="badge bg-success">Да</span>' : '<span class="badge bg-warning">Нет</span>' !!}</td>
                        <td>{{ $row['weight'] }}</td>
                        <td>{{ $row['gate_name'] }}</td>
{{--                        <td>--}}
{{--                            <a href="#" class="btn btn-sm btn-secondary"><i class="bi bi-eye"></i></a>--}}
{{--                        </td>--}}
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