@extends('layout.app')

@section('title', 'Личный кабинет');

@section('content')
    <script src="/assets/ac/apexcharts.js"></script>
    <div class="supplier_section">
        <div class="row my-3">
            <div class="col-12 text-end">
                <a href="{{ route('supplier.claim.add') }}" class="btn btn-primary">Создать заявку</a>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <h2>Скоро</h2>
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
{{--                        <td></td>--}}
                    </tr>
                    @foreach($booking as $row)
                        <tr>
                            <td>{{ $row['id'] }}</td>
                            <td><span class="badge bg-info">{{ $row['status'] }}</span></td>
                            <td>{{ $row['driver_name'] }}</td>
                            <td>{{ $row['expeditor_name'] }}</td>
                            <td>{{ $row['booking_date']->format('Y.m.d') }} | {{ $row['start_time']->format('H:m') }} - {{ $row['end_time']->format('H:m') }}</td>
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

        <div class="row">
            <div class="col-12">
                <h2>Будущие заявки</h2>
                <div id="supplier_date_chart"></div>
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
                            <td>{{ $row['gbc'] }}</td>
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
{{--                        <td></td>--}}
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
<script>
    var options = {
        series: [
            {
                data: [
                    @foreach($booking as $row)
{{--                        @if($row['booking_date']->format('Y-m-d') == date('Y-m-d', time()))--}}
                            {
                                x: '{{ $row['gate_name'] }}',
                                y: [
                                    new Date('{{ $row['booking_date']->format('Y-m-d') }} {{ $row['start_time']->format('H:m') }}').getTime(),
                                    new Date('{{ $row['booking_date']->format('Y-m-d') }} {{ $row['end_time']->format('H:m') }}').getTime()
                                ]
                            },
{{--                        @endif--}}
                    @endforeach
                ]
            }
        ],
        chart: {
            height: 350,
            type: 'rangeBar',
            zoom: {
                enabled: false
            },
            locales: [{
                "name": "ru",
                "options": {
                    "months": [
                        "Январь",
                        "Февраль",
                        "Март",
                        "Апрель",
                        "Май",
                        "Июнь",
                        "Июль",
                        "Август",
                        "Сентябрь",
                        "Октябрь",
                        "Ноябрь",
                        "Декабрь"
                    ],
                    "shortMonths": [
                        "Янв",
                        "Фев",
                        "Мар",
                        "Апр",
                        "Май",
                        "Июн",
                        "Июл",
                        "Авг",
                        "Сен",
                        "Окт",
                        "Ноя",
                        "Дек"
                    ],
                    "days": [
                        "Воскресенье",
                        "Понедельник",
                        "Вторник",
                        "Среда",
                        "Четверг",
                        "Пятница",
                        "Суббота"
                    ],
                    "shortDays": ["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
                    "toolbar": {
                        "exportToSVG": "Сохранить SVG",
                        "exportToPNG": "Сохранить PNG",
                        "exportToCSV": "Сохранить CSV",
                        "menu": "Меню",
                        "selection": "Выбор",
                        "selectionZoom": "Выбор с увеличением",
                        "zoomIn": "Увеличить",
                        "zoomOut": "Уменьшить",
                        "pan": "Перемещение",
                        "reset": "Сбросить увеличение"
                    }
                }
            }
            ],
            defaultLocale: 'ru'
        },
        plotOptions: {
            bar: {
                horizontal: true,
                barHeight: '50%',
                rangeBarGroupRows: true
            }
        },
        colors: [
            "#008FFB", "#00E396", "#FEB019", "#FF4560", "#775DD0",
            "#3F51B5", "#546E7A", "#D4526E", "#8D5B4C", "#F86624",
            "#D7263D", "#1B998B", "#2E294E", "#F46036", "#E2C044"
        ],
        fill: {
            type: 'solid'
        },
        xaxis: {
            type: 'datetime'
        },
        tooltip: {
            intersect: false,
            shared: false,
        }
    };

    var chart = new ApexCharts(document.querySelector("#supplier_date_chart"), options);
    chart.render();
</script>
@endsection