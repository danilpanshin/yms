@php use Carbon\Carbon; @endphp
@extends('layout.app')

@section('title', 'Поставщики')

@section('content')
    <style>

    </style>
    <div class="supplier_section">
        <div class="row my-3">
            <div class="col-12 text-end">
                <a href="{{ route('stock_admin.claim.add') }}" class="btn btn-primary">Создать заявку</a>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <h2>Сегодня</h2>
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
                            <td>{{ $row['booking_date']->format('Y.m.d') }} | {{ $row['start_time']->format('H:i') }}
                                - {{ $row['end_time']->format('H:i') }}</td>
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

        @php
            $gates = [];
            $gate_names = [];
            $gate_dates = [];
            $gate_dates_start = [];
            foreach($booking as $row){
                if($row['booking_date']->format('ymd') == now()->format('ymd')){
                    if(!in_array($row['gate_id'], $gates)){
                        $gates[] = $row['gate_id'];
                        $gate_names[$row['gate_id']] = $row['gate_name'];
                        $gate_dates[$row['gate_id']] = [];
                        $gate_dates_start[$row['gate_id']] = [];
                    }

                    $start_time = $row['booking_date']->setHour((int)$row['start_time']->format('H'))->setMinute((int)$row['start_time']->format('i'));
                    $end_time = $row['booking_date']->setHour((int)$row['end_time']->format('H'))->setMinute((int)$row['end_time']->format('i'));

                    $gate_dates_start[$row['gate_id']][] = $start_time->format('H_i');

                    for($i=$start_time->unix();$i<=$end_time->unix();$i+=15*60){
                        $current_carbon = Carbon::createFromTimestamp($i);
                        $_arg = sprintf('%02d', $current_carbon->setTimezone('Europe/Moscow')->format('H')) . '_' . sprintf('%02d', (floor($current_carbon->setTimezone('Europe/Moscow')->format('i'))));
                        // if(!in_array($_arg, $gate_dates[$row['gate_id']])){
                            $gate_dates[$row['gate_id']][] = $_arg;
                        // }
                    }
                }
            }

            sort($gates);

            foreach($gate_dates as &$gate_date_values){
                sort($gate_date_values);
            }

            $colors = ['info', 'primary', 'success', 'danger', 'warning',];

        @endphp


        <div class="row">
            <div class="col-12">
                <h2>Заявки сегодня ({{ $booking->count() }})</h2>
                <table class="table table-responsive table-hover table-striped table-borderless">
                    <tr>
                        <td style="background: none; !important;"></td>
                        <td class="justify-content-center text-center" colspan="{{ 24 * 4 }}">Час</td>
                    </tr>

                    <tr class="timeline_row" style="height: 15px;">
                        <td style="width: 150px; font-size: 14px;">Ворота</td>
                        @for($hour = 0; $hour <=23; $hour+=1)
                            <td colspan="4" class="text-center time_minute_0 time_minute_45" style="font-size: 14px;">{{ sprintf('%02d', $hour) }}</td>
                        @endfor
                    </tr>
                    @php $color_number = 0; $color_number_max = count($colors) - 1; @endphp
                    @foreach($gates as $gate)
                        <tr class="timeline_row">
                            <td style="width: 150px; font-size: 14px;">{{ $gate_names[$gate] }}</td>
                            @for($hour = 0; $hour <=23; $hour+=1)
                               @for($minute = 0; $minute <= 45; $minute+=15)
                                   @php
                                       if(in_array(sprintf('%02d', $hour) . '_' . sprintf('%02d', $minute), $gate_dates_start[$gate])){
                                           $color_number += 1;
                                           if($color_number > $color_number_max){
                                               $color_number = 0;
                                           }
                                       }
                                       $current_color = $colors[$color_number];
                                   @endphp
                                   <td class="time_minute_{{ $minute }} time_{{ sprintf('%02d', $hour) }}_{{ sprintf('%02d', $minute) }}"
                                       style="font-size: 2px; height: 30px;"
                                       title="{{ $gate_names[$gate] }} - {{ $hour }}:{{ $minute }}">
                                       @if(in_array(sprintf('%02d', $hour) . '_' . sprintf('%02d', $minute), $gate_dates[$gate]))
                                           <div class="badge bg-{{ $current_color }} border-0 rounded-0 w-100" style="margin-top: 5px; height: 20px; opacity: 0.9">&nbsp;</div>
                                       @else
                                           <div class="badge bg-transparent border-0 rounded-0 w-100" style="margin-top: 5px; height: 20px; opacity: 0.9">&nbsp;</div>
                                       @endif
                                   </td>
                               @endfor
                            @endfor
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <h2>Будущие заявки ({{ $bookingLastAll->count() }})</h2>
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
                            <td>{{ $row['booking_date']->format('Y.m.d') }} | {{ $row['start_time']->format('H:i') }} - {{ $row['end_time']->format('H:i') }}</td>
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


{{--    @foreach($booking as $row)--}}
{{--        @if($row['booking_date']->format('Y-m-d') == date('Y-m-d', time()))--}}
{{--            {--}}
{{--                x: '{{ $row['gate_name'] }}',--}}
{{--                y: [--}}
{{--                    new Date('{{ $row['booking_date']->format('Y-m-d') }} {{ $row['start_time']->format('H:i') }}').getTime(),--}}
{{--                    new Date('{{ $row['booking_date']->format('Y-m-d') }} {{ $row['end_time']->format('H:i') }}').getTime()--}}
{{--                ]--}}
{{--            },--}}
{{--       @endif--}}
{{--    @endforeach--}}



@endsection