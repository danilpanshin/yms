@extends('layout.app')

@section('title', 'ЛК Поставщика');

@section('content')
{{--    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>--}}
    <div class="supplier_section">
        <div class="row my-3">
            <div class="col-12">
                <a href="{{ route('supplier.claim.add') }}" class="btn btn-primary">Создать заявку</a>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <table class="table table-striped">
                    <tr>
                        <td>Номер</td>
                        <td>Статус</td>
                        <td>ФИО Водителя</td>
                        <td>ФИО Экспедитора</td>
                        <td>Дата/Время</td>
                        <td>Номер ТС</td>
                        <td>Тип поставки</td>
                        <td>Тип ТС</td>
                        <td>Г/Б</td>
                        <td>Масса</td>
                        <td>Ворота</td>
                        <td></td>
                    </tr>
                    @foreach($booking as $row)
                        <tr>
                            <td>{{ $row['id'] }}</td>
                            <td><span class="badge bg-info">{{ $row['status'] }}</span></td>
                            <td>{{ $row['driver_name'] }}</td>
                            <td>{{ $row['expeditor_name'] }}</td>
                            <td>{{ $row['booking_date']->format('Y.m.d') }} {{ $row['start_time']->format('H:m') }}</td>
                            <td>{{ $row['car_number'] }}</td>
                            <td>{{ $row['acceptances_name'] }}</td>
                            <td>{{ $row['car_type_name'] }}</td>
                            <td>{!! $row['gbort'] ? '<span class="badge bg-success">Да</span>' : '<span class="badge bg-warning">Нет</span>' !!}</td>
                            <td>{{ $row['weight'] }}</td>
                            <td>{{ $row['gate_name'] }}</td>
                            <td>
                                <a href="#" class="btn btn-sm btn-secondary"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12 col-sm-12 col-md-12 col-xl-6">
                <h2>Водители</h2>
                <table class="table table-striped">
                    <tr>
                        <td>Номер</td>
                        <td>ФИО</td>
                        <td>Заявок</td>
                    </tr>
                    @foreach($drivers as $row)
                        <tr>
                            <td>{{ $row['id'] }}</td>
                            <td>{{ $row['name'] }}</td>
                            <td>{{ rand(0, 50) }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-xl-6">
                <h2>Авто</h2>
                <table class="table table-striped">
                    <tr>
                        <td>ФИО Водителя</td>
                        <td>Номер ТС</td>
                        <td>Тип ТС</td>
                        <td>Г/Б</td>
                    </tr>
                </table>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-xl-12">
                <h2>Недавние</h2>
                <table class="table table-striped">
                    <tr>
                        <tr>
                        <td>Номер</td>
                        <td>Статус</td>
                        <td>ФИО Водителя</td>
                        <td>ФИО Экспедитора</td>
                        <td>Дата/Время</td>
                        <td>Номер ТС</td>
                        <td>Тип поставки</td>
                        <td>Тип ТС</td>
                        <td>Г/Б</td>
                        <td>Масса</td>
                        <td>Ворота</td>
                        <td></td>
                    </tr>
                    @foreach($bookingLast as $row)
                        <tr>
                            <td>{{ $row['id'] }}</td>
                            <td><span class="badge bg-info">{{ $row['status'] }}</span></td>
                            <td>{{ $row['driver_name'] }}</td>
                            <td>{{ $row['expeditor_name'] }}</td>
                            <td>{{ $row['booking_date']->format('Y.m.d') }} {{ $row['start_time']->format('H:m') }}</td>
                            <td>{{ $row['car_number'] }}</td>
                            <td>{{ $row['acceptances_name'] }}</td>
                            <td>{{ $row['car_type_name'] }}</td>
                            <td>{!! $row['gbort'] ? '<span class="badge bg-success">Да</span>' : '<span class="badge bg-warning">Нет</span>' !!}</td>
                            <td>{{ $row['weight'] }}</td>
                            <td>{{ $row['gate_name'] }}</td>
                            <td>
                                <a href="#" class="btn btn-sm btn-secondary"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@endsection