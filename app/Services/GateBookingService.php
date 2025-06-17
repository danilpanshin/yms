<?php

namespace App\Services;

use App\Models\BusyPeriod;
use App\Models\Gate;
use App\Models\GateBooking;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class GateBookingService
{


    /**
     * @throws \Exception
     */
    function findAvailableGates($booking, $gates, $busyTimes, $startTime, $durationMinutes): array
    {
        // Преобразуем входные данные в удобный формат
        $requestedStart = new DateTime($startTime);
        $requestedEnd = clone $requestedStart;
        $requestedEnd->add(new DateInterval('PT' . $durationMinutes . 'M'));

        // Проверяем стандартные доступные слоты
        $availableGates = $this->checkAvailableGates($booking, $gates, $busyTimes, $requestedStart, $requestedEnd);

        if (!empty($availableGates)) {
            return $availableGates;
        }

        // Если не нашли, проверяем слоты +-2 часа
        return $this->findNearbyAvailableSlots($booking, $gates, $busyTimes, $requestedStart, $durationMinutes);
    }

    /**
     * @throws \Exception
     */
    function checkAvailableGates($booking, $gates, $busyTimes, $requestedStart, $requestedEnd): array
    {
        $availableGates = [];

        // Группируем брони по gate_id для удобства
        $bookingsByGate = [];
        foreach ($booking as $book) {
            $bookingsByGate[$book['gate_id']][] = $book;
        }

        foreach ($gates as $gate) {
            $gateId = $gate['gate_id'];
            $isAvailable = true;

            // Проверяем общие запрещенные периоды
            foreach ($busyTimes as $busy) {
                $busyStart = new DateTime($busy['start_time']);
                $busyEnd = new DateTime($busy['end_time']);

                if ($this->timeOverlap($requestedStart, $requestedEnd, $busyStart, $busyEnd)) {
                    $isAvailable = false;
                    break;
                }
            }

            if (!$isAvailable) {
                continue;
            }

            // Проверяем существующие брони для этих ворот
            if (isset($bookingsByGate[$gateId])) {
                foreach ($bookingsByGate[$gateId] as $book) {
                    $bookStart = new DateTime($book['start_time']);
                    $bookEnd = new DateTime($book['end_time']);

                    if ($this->timeOverlap($requestedStart, $requestedEnd, $bookStart, $bookEnd)) {
                        $isAvailable = false;
                        break;
                    }
                }
            }

            if ($isAvailable) {
                $availableGates[] = [
                    'gate_id' => $gateId,
                    'start_time' => $requestedStart->format('H:i'),
                    'end_time' => $requestedEnd->format('H:i')
                ];
            }
        }

        return $availableGates;
    }

    /**
     * @throws \Exception
     */
    function findNearbyAvailableSlots($booking, $gates, $busyTimes, $originalStart, $durationMinutes): array
    {
        // Проверяем слоты в пределах +-2 часов с шагом 15 минут
        $timeIntervals = [-120, -105, -90, -75, -60, -45, -30, -15, 15, 30, 45, 60, 75, 90, 105, 120];

        foreach ($timeIntervals as $minutes) {
            $newStart = clone $originalStart;
            $newStart->add(new DateInterval('PT' . $minutes . 'M'));
            $newEnd = clone $newStart;
            $newEnd->add(new DateInterval('PT' . $durationMinutes . 'M'));

            $availableGates = $this->checkAvailableGates($booking, $gates, $busyTimes, $newStart, $newEnd);

            if (!empty($availableGates)) {
                return $availableGates;
            }
        }

        return []; // Ничего не найдено
    }

    function timeOverlap($start1, $end1, $start2, $end2): bool
    {
        return $start1 < $end2 && $start2 < $end1;
    }



    protected int $timeStep = 15; // шаг времени в минутах
    protected int $palletsPerHour = 33; // 33 паллета в час

    protected int $user_id;

    public function __construct($user_id = null){
        $this->user_id = $user_id ?? Auth::user()->id;
    }

    public function get_list($future = false, $past = false, $time_to_seconds = true, $paginate = false, $limit = false, $filter_joins = true)
    {
        $res = GateBooking::select(
            'gate_bookings.*', 'drivers.name as driver_name', 'expeditors.name as expeditor_name',
            'car_types.name as car_type_name', 'acceptances.name as acceptance_name', 'gates.name as gate_name'
        );
        $res->where('gate_bookings.user_id', '=', $this->user_id);

        if($future || $past) {
            $datetime = Carbon::now();
            if ($time_to_seconds) {
                $datetime->setHour(0)->setMinute(0)->setSecond(0);
            }
        }

        if($future) {
            $res->where('gate_bookings.booking_date', '>=', $datetime);
        }
        if($past) {
            $res->where('gate_bookings.booking_date', '<', $datetime);
        }
        $res->leftJoin('drivers', 'drivers.id', '=', 'gate_bookings.driver_id');
        $res->leftJoin('expeditors', 'expeditors.id', '=', 'gate_bookings.expeditor_id');
        $res->leftJoin('acceptances', 'acceptances.id', '=', 'gate_bookings.acceptances_id');
        $res->leftJoin('car_types', 'car_types.id', '=', 'gate_bookings.car_type_id');
        $res->leftJoin('gates', 'gates.id', '=', 'gate_bookings.gate_id');
        if($filter_joins) {
            $res->where(function($query) {
                return $query
                    ->where('drivers.user_id', '=', $this->user_id)
                    ->orWhereNull('gate_bookings.driver_id');
            });
            $res->where(function($query) {
                return $query
                    ->where('expeditors.user_id', '=', $this->user_id)
                    ->orWhereNull('gate_bookings.expeditor_id');
            });
        }
        $res->orderBy('gate_bookings.booking_date');
        $res->orderBy('gate_bookings.start_time');
        if($limit and is_int($limit) and $limit > 0){
            $result = $res->limit($limit)->get();
        } elseif($paginate and is_int($paginate) and $paginate > 0){
            $result = $res->paginate($paginate);
        } else {
            $result = $res->get();
        }

        return $result;
    }
























    /**
     * Получить доступные временные слоты для бронирования
     */
    public function getAvailableSlots(string $date, int $palletsCount, bool $hasGbort): array
    {
        // Рассчитываем необходимое время
        $requiredMinutes = $this->calculateRequiredTime($palletsCount);

        // Получаем все занятые периоды для всех ворот
        $bookedSlots = $this->getBookedSlots($date)->toArray();

        // Получаем системные занятые периоды
        $busyPeriods = $this->getBusyPeriods()->toArray();

        $gates = Gate::where('is_active', true)->get();

        $slots = [];
        foreach($gates as $gate) {
            $slots[$gate->id] = [];
        }

        foreach($bookedSlots as $bookedSlot) {
            $slots[$bookedSlot['gate_id']][] = $bookedSlot;
        }

        // Фильтруем слоты
        $free = $this->findAvailableSlots($slots, $busyPeriods);
        return $this->findAvailableIntervals($free, $requiredMinutes);
    }

    function findAvailableIntervals(array $freeSlots, int $durationMinutes, int $step = 15): array
    {
        $result = [];

        foreach ($freeSlots as $slot) {
            $start = strtotime($slot['start']);
            $end = strtotime($slot['end']);

            // Если конец равен 24:00, преобразуем в 23:59
            if ($slot['end'] === '24:00') {
                $end = strtotime('23:59');
            }

            $current = $start;

            while ($current + ($durationMinutes * 60) <= $end) {
                $intervalEnd = $current + ($durationMinutes * 60);

                $result[] = [
                    'start' => date('H:i', $current),
                    'end' => ($slot['end'] === '24:00' && $intervalEnd >= strtotime('23:59'))
                        ? '24:00'
                        : date('H:i', $intervalEnd)
                ];

                // Переходим к следующему интервалу с заданным шагом
                $current += ($step * 60);
            }
        }

        return $result;
    }

    function findAvailableSlots(array $arrays, array $exclude): array
    {
        // Преобразуем время в минуты для удобства сравнения
        $timeToMinutes = function($time) {
            [$hours, $minutes] = explode(':', $time);
            return (int)$hours * 60 + (int)$minutes;
        };

        // Создаем массив всех возможных 15-минутных интервалов дня
        $allSlots = [];
        for ($i = 0; $i < 1440; $i += 15) {
            $start = sprintf('%02d:%02d', floor($i / 60), $i % 60);
            $end = sprintf('%02d:%02d', floor(($i + 15) / 60), ($i + 15) % 60);
            if ($i + 15 > 1440) {
                $end = '23:59';
            }
            $allSlots[] = ['start' => $start, 'end' => $end];
        }

        // Помечаем слоты, которые нужно исключить
        $excludeSlots = [];
        foreach ($exclude as $interval) {
            $start = $timeToMinutes($interval['start_time']);
            $end = $timeToMinutes($interval['end_time']);

            foreach ($allSlots as $key => $slot) {
                $slotStart = $timeToMinutes($slot['start']);
                $slotEnd = $timeToMinutes($slot['end']);

                if ($slotStart < $end && $slotEnd > $start) {
                    $excludeSlots[$key] = true;
                }
            }
        }

        // Помечаем слоты, которые заняты во всех массивах
        $commonBusySlots = [];
        foreach ($allSlots as $key => $slot) {
            $slotStart = $timeToMinutes($slot['start']);
            $slotEnd = $timeToMinutes($slot['end']);

            $isBusyInAll = true;
            foreach ($arrays as $array) {
                $isBusyInArray = false;
                foreach ($array as $interval) {
                    $start = $timeToMinutes($interval['start_time']);
                    $end = $timeToMinutes($interval['end_time']);

                    if ($slotStart < $end && $slotEnd > $start) {
                        $isBusyInArray = true;
                        break;
                    }
                }
                if (!$isBusyInArray) {
                    $isBusyInAll = false;
                    break;
                }
            }

            if ($isBusyInAll) {
                $commonBusySlots[$key] = true;
            }
        }

        // Объединяем исключаемые слоты
        $slotsToRemove = $excludeSlots + $commonBusySlots;

        // Фильтруем слоты
        $availableSlots = [];
        foreach ($allSlots as $key => $slot) {
            if (!isset($slotsToRemove[$key])) {
                $availableSlots[] = $slot;
            }
        }

        // Объединяем смежные слоты
        $mergedSlots = [];
        $current = null;

        foreach ($availableSlots as $slot) {
            if ($current === null) {
                $current = $slot;
            } else {
                if ($slot['start'] === $current['end']) {
                    $current['end'] = $slot['end'];
                } else {
                    $mergedSlots[] = $current;
                    $current = $slot;
                }
            }
        }

        if ($current !== null) {
            $mergedSlots[] = $current;
        }

        return $mergedSlots;
    }

    function findRandomSlotForInterval(string $date, int $pallets, bool $hasGbort, string $start_time)
    {
        $freeSlots = $this->getAvailableSlots($date, $pallets, $hasGbort);

        $interval = [];

        $duration_minutes = $this->calculateRequiredTime($pallets);

        $start_time_arr = explode(':', $start_time);
        $intervalStart = Carbon::make($date)->setHour((int)$start_time_arr[0])->setMinute((int)$start_time_arr[1])->setSecond(0);
        $start_time_unix = $intervalStart->getTimestamp();
        $intervalEnd_unix = $start_time_unix + ($duration_minutes * 60);
        $intervalEnd = Carbon::createFromTimestamp($intervalEnd_unix);


        // Преобразуем время в минуты для удобства сравнения
        $timeToMinutes = function($time) {
            [$hours, $minutes] = explode(':', $time);
            return (int)$hours * 60 + (int)$minutes;
        };

        $intervalStart = $timeToMinutes($intervalStart->format("H:i"));
        $intervalEnd = $timeToMinutes($intervalEnd->format("H:i"));

        $suitableSlots = [];

        foreach ($freeSlots as $key => $slot) {
            $slotStart = $timeToMinutes($slot['start']);
            $slotEnd = $timeToMinutes($slot['end'] === '24:00' ? '23:59' : $slot['end']);

            // Проверяем, влезает ли интервал в этот слот
            if ($intervalStart >= $slotStart && $intervalEnd <= $slotEnd) {
                $suitableSlots[$key] = $slot;
            }
        }

        // Если нет подходящих слотов
        if (empty($suitableSlots)) {
            return null;
        }

        // Выбираем случайный ключ из подходящих слотов
        $randomKey = array_rand($suitableSlots);

        // Выбираем случайный подходящий слот
        return $randomKey;
    }

    /**
     * Рассчитываем необходимое время в минутах
     */
    public function calculateRequiredMinutesTime(int $palletsCount): int
    {
        // Округляем до ближайшего шага времени в большую сторону
        return $this->calculateRequiredHoursTime($palletsCount) * 60;
    }

    public function calculateRequiredHoursTime(int $palletsCount): int
    {
        // Округляем до ближайшего шага времени в большую сторону
        return ceil($palletsCount / $this->palletsPerHour);
    }

    /**
     * Получаем все занятые слоты для всех ворот на указанную дату
     */
    protected function getBookedSlots(string $date): Collection
    {
        return GateBooking::where('booking_date', $date)->get(['start_time', 'end_time', 'gate_id', 'booking_date', 'id']);
    }

    /**
     * Получаем системные занятые периоды
     */
    protected function getBusyPeriods(): Collection
    {
        return BusyPeriod::all();
    }

    /**
     * Проверяем, попадает ли слот в занятые системные периоды
     */
    protected function isSlotInBusyPeriods(array $slot, Collection $busyPeriods): bool
    {
        $slotStart = Carbon::createFromTimeString($slot['start']);
        $slotEnd = Carbon::createFromTimeString($slot['end']);

        foreach ($busyPeriods as $period) {
            $periodStart = Carbon::createFromTimeString($period->start_time);
            $periodEnd = Carbon::createFromTimeString($period->end_time);

            if ($slotStart < $periodEnd && $slotEnd > $periodStart) {
                return true;
            }
        }

        return false;
    }
}