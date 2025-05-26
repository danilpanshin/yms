<?php

namespace App\Services;

use App\Models\BusyPeriod;
use App\Models\Gate;
use App\Models\GateBooking;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class GateBookingService
{
    protected int $timeStep = 15; // шаг времени в минутах
    protected int $palletsPerHour = 33; // 33 паллета в час

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

    /**
     * Рассчитываем необходимое время в минутах
     */
    public function calculateRequiredTime(int $palletsCount): int
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