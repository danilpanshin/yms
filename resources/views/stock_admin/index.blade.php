@extends('layout.app')

@section('title', 'Поставщики')

@section('content')
    <script src="/assets/ac/apexcharts.js"></script>
    <div class="supplier_section">
        <div class="row my-3">
            <div class="col-12 text-end">
                <a href="{{ route('stock_admin.claim.add') }}" class="btn btn-primary">Создать заявку</a>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <h2>Скоро</h2>
                <table class="table table-striped">
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
                        {{--                        <td></td>--}}
                    </tr>
                    @foreach($booking as $row)
                        <tr>
                            <td>{{ $row['id'] }}</td>
                            <td><span class="badge bg-info">{{ $row['status'] }}</span></td>
                            <td>{{ $row['supplier_name'] }}</td>
                            <td>{{ $row['driver_name'] }}</td>
                            <td>{{ $row['expeditor_name'] }}</td>
                            <td>{{ $row['booking_date']->format('Y.m.d') }} | {{ $row['start_time']->format('H:i') }} - {{ $row['end_time']->format('H:i') }}</td>
                            <td>{{ $row['car_number'] }}</td>
                            <td>{{ $row['acceptance_name'] }}</td>
                            <td>{{ $row['car_type_name'] }}</td>
                            <td>{!! $row['gbort'] ? '<span class="badge bg-success">Да</span>' : '<span class="badge bg-warning">Нет</span>' !!}</td>
                            <td>{{ $row['weight'] }}</td>
                            <td>{{ $row['gate_name'] }}</td>
                            {{--                            <td>--}}
                            {{--                                <a href="#" class="btn btn-sm btn-secondary"><i class="bi bi-eye"></i></a>--}}
                            {{--                            </td>--}}
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>


        <div class="row mb-3">
            <div class="col-12">
                <h2>Недавно</h2>
                <table class="table table-striped">
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
                        {{--                        <td></td>--}}
                    </tr>
                    @foreach($bookingLast as $row)
                        <tr>
                            <td>{{ $row['id'] }}</td>
                            <td><span class="badge bg-info">{{ $row['status'] }}</span></td>
                            <td>{{ $row['supplier_name'] }}</td>
                            <td>{{ $row['driver_name'] }}</td>
                            <td>{{ $row['expeditor_name'] }}</td>
                            <td>{{ $row['booking_date']->format('Y.m.d') }} | {{ $row['start_time']->format('H:i') }} - {{ $row['end_time']->format('H:i') }}</td>
                            <td>{{ $row['car_number'] }}</td>
                            <td>{{ $row['acceptance_name'] }}</td>
                            <td>{{ $row['car_type_name'] }}</td>
                            <td>{!! $row['gbort'] ? '<span class="badge bg-success">Да</span>' : '<span class="badge bg-warning">Нет</span>' !!}</td>
                            <td>{{ $row['weight'] }}</td>
                            <td>{{ $row['gate_name'] }}</td>
                            {{--                            <td>--}}
                            {{--                                <a href="#" class="btn btn-sm btn-secondary"><i class="bi bi-eye"></i></a>--}}
                            {{--                            </td>--}}
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>


    </div>
@endsection

@section('script')

@endsection