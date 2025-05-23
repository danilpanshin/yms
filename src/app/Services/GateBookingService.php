<?php

namespace App\Services;

use App\Models\Gate;
use App\Models\BusyPeriod;
use App\Models\GateBooking;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class GateBookingService
{
    protected int $timeStep = 15; // шаг времени в минутах
    protected int $palletsPerHour = 33; // 33 паллета в час
    protected array $workingHours = ['08:00', '20:00']; // рабочие часы

    /**
     * Получить доступные временные слоты для бронирования
     */
    public function getAvailableSlots(string $date, int $palletsCount, bool $hasGbort): Collection
    {
        // Рассчитываем необходимое время
        $requiredMinutes = $this->calculateRequiredTime($palletsCount, $hasGbort);

        // Получаем все занятые периоды для всех ворот
        $bookedSlots = $this->getBookedSlots($date);

        // Получаем системные занятые периоды
        $busyPeriods = $this->getBusyPeriods();

        // Получаем все ворота с учетом наличия гидроборта
        $availableGates = Gate::where('is_active', true)
            ->where('gbort', $hasGbort)
            ->pluck('id');

        // Генерируем все возможные слоты
        $allSlots = $this->generateTimeSlots();

        // Фильтруем слоты
        return $this->filterSlots(
            $allSlots,
            $bookedSlots,
            $busyPeriods,
            $availableGates,
            $requiredMinutes
        );
    }

    /**
     * Рассчитываем необходимое время в минутах
     */
    protected function calculateRequiredTime(int $palletsCount, bool $hasGbort): int
    {
        $baseHours = $palletsCount / $this->palletsPerHour;
        $baseMinutes = $baseHours * 60;

        // Если есть гидроборт, добавляем 30 минут
        if ($hasGbort) {
            $baseMinutes += 30;
        }

        // Округляем до ближайшего шага времени в большую сторону
        return ceil($baseMinutes / $this->timeStep) * $this->timeStep;
    }

    /**
     * Получаем все занятые слоты для всех ворот на указанную дату
     */
    protected function getBookedSlots(string $date): Collection
    {
        return GateBooking::where('booking_date', $date)
            ->whereNull('deleted_at')
            ->get(['gate_id', 'start_time', 'end_time']);
    }

    /**
     * Получаем системные занятые периоды
     */
    protected function getBusyPeriods(): Collection
    {
        return BusyPeriod::all(['start_time', 'end_time']);
    }

    /**
     * Генерируем все возможные временные слоты в течение рабочего дня
     */
    protected function generateTimeSlots(): Collection
    {
        $slots = new Collection();

        $start = Carbon::createFromTimeString($this->workingHours[0]);
        $end = Carbon::createFromTimeString($this->workingHours[1]);

        $current = $start->copy();

        while ($current->addMinutes($this->timeStep) <= $end) {
            $slotStart = $current->copy()->subMinutes($this->timeStep);
            $slotEnd = $current->copy();

            $slots->push([
                'start' => $slotStart->format('H:i'),
                'end' => $slotEnd->format('H:i'),
            ]);
        }

        return $slots;
    }

    /**
     * Фильтруем слоты по всем условиям
     */
    protected function filterSlots(
        Collection $allSlots,
        Collection $bookedSlots,
        Collection $busyPeriods,
        Collection $availableGates,
        int $requiredMinutes
    ): Collection {
        $requiredSlots = ceil($requiredMinutes / $this->timeStep);

        return $allSlots->filter(function ($slot) use ($bookedSlots, $busyPeriods, $availableGates, $requiredSlots) {
            // Проверяем, что слот не пересекается с системными занятыми периодами
            if ($this->isSlotInBusyPeriods($slot, $busyPeriods)) {
                return false;
            }

            // Проверяем, что есть хотя бы одни ворота, где этот слот свободен
            return $this->isSlotAvailableInAnyGate($slot, $bookedSlots, $availableGates, $requiredSlots);
        })->values();
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

    /**
     * Проверяем, свободен ли слот хотя бы в одних воротах
     */
    protected function isSlotAvailableInAnyGate(
        array $slot,
        Collection $bookedSlots,
        Collection $availableGates,
        int $requiredSlots
    ): bool {
        $slotStart = Carbon::createFromTimeString($slot['start']);
        $slotEnd = Carbon::createFromTimeString($slot['end']);

        $busyPeriods = $this->getBusyPeriods();

        // Проверяем для каждого доступного gate
        foreach ($availableGates as $gateId) {
            $isAvailable = true;

            // Проверяем все бронирования для этих ворот
            foreach ($bookedSlots as $booking) {
                if ($booking->gate_id != $gateId) {
                    continue;
                }

                $bookingStart = Carbon::createFromTimeString($booking->start_time);
                $bookingEnd = Carbon::createFromTimeString($booking->end_time);

                // Если есть пересечение, слот занят
                if ($slotStart < $bookingEnd && $slotEnd > $bookingStart) {
                    $isAvailable = false;
                    break;
                }
            }

            if ($isAvailable) {
                // Проверяем, что следующие requiredSlots-1 слотов тоже свободны
                $allConsecutiveSlotsAvailable = true;

                for ($i = 1; $i < $requiredSlots; $i++) {
                    $nextSlotStart = $slotStart->copy()->addMinutes($this->timeStep * $i);
                    $nextSlotEnd = $slotEnd->copy()->addMinutes($this->timeStep * $i);

                    // Проверяем, что следующий слот не выходит за рабочие часы
                    $workingEnd = Carbon::createFromTimeString($this->workingHours[1]);
                    if ($nextSlotEnd > $workingEnd) {
                        $allConsecutiveSlotsAvailable = false;
                        break;
                    }

                    // Проверяем, что следующий слот свободен
                    $nextSlot = [
                        'start' => $nextSlotStart->format('H:i'),
                        'end' => $nextSlotEnd->format('H:i'),
                    ];

                    if ($this->isSlotInBusyPeriods($nextSlot, $busyPeriods) ||
                        !$this->isSlotAvailableInAnyGate($nextSlot, $bookedSlots, $availableGates, 1)) {
                        $allConsecutiveSlotsAvailable = false;
                        break;
                    }
                }

                if ($allConsecutiveSlotsAvailable) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Сохраняем новое бронирование
     * @throws \Exception
     */
    public function createBooking(array $data): GateBooking
    {
        $date = $data['booking_date'];
        $startTime = $data['start_time'];
        $requiredMinutes = $this->calculateRequiredTime($data['pallets_count'], $data['gbort']);

        // Находим подходящие ворота
        $gateId = $this->findAvailableGate($date, $startTime, $requiredMinutes, $data['gbort']);

        if (!$gateId) {
            throw new \Exception('No available gates for the selected time slot');
        }

        // Рассчитываем end_time
        $start = Carbon::createFromTimeString($startTime);
        $end = $start->copy()->addMinutes($requiredMinutes);

        // Создаем бронирование
        return GateBooking::create([
            'gate_id' => $gateId,
            'booking_date' => $date,
            'start_time' => $start->format('H:i:s'),
            'end_time' => $end->format('H:i:s'),
            'pallets_count' => $data['pallets_count'],
            'weight' => $data['weight'] ?? 0,
            'purpose' => $data['purpose'],
            'car_number' => $data['car_number'],
            'acceptances_id' => $data['acceptances_id'],
            'gbort' => $data['gbort'],
            'car_type_id' => $data['car_type_id'],
            'driver_id' => $data['driver_id'],
            'user_id' => $data['user_id'],
            'expeditor_id' => $data['expeditor_id'],
            'is_internal' => $data['is_internal'] ?? false,
            'status' => $data['status'] ?? 'pending',
        ]);
    }

    /**
     * Находим доступные ворота для указанного времени
     */
    protected function findAvailableGate(
        string $date,
        string $startTime,
        int $requiredMinutes,
        bool $hasGbort
    ): ?int {
        $bookedSlots = $this->getBookedSlots($date);
        $busyPeriods = $this->getBusyPeriods();

        $availableGates = Gate::where('is_active', true)
            ->where('gbort', $hasGbort)
            ->pluck('id');

        $requiredSlots = ceil($requiredMinutes / $this->timeStep);
        $start = Carbon::createFromTimeString($startTime);
        $end = $start->copy()->addMinutes($requiredMinutes);

        foreach ($availableGates as $gateId) {
            $isAvailable = true;

            // Проверяем основной слот
            $slot = [
                'start' => $start->format('H:i'),
                'end' => $end->format('H:i'),
            ];

            if ($this->isSlotInBusyPeriods($slot, $busyPeriods)) {
                continue;
            }

            // Проверяем все бронирования для этих ворот
            foreach ($bookedSlots as $booking) {
                if ($booking->gate_id != $gateId) {
                    continue;
                }

                $bookingStart = Carbon::createFromTimeString($booking->start_time);
                $bookingEnd = Carbon::createFromTimeString($booking->end_time);

                if ($start < $bookingEnd && $end > $bookingStart) {
                    $isAvailable = false;
                    break;
                }
            }

            if ($isAvailable) {
                return $gateId;
            }
        }

        return null;
    }
}