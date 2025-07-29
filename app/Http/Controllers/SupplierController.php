<?php

namespace App\Http\Controllers;

use App\Models\Gate;
use App\Services\GateBookingService;
use App\Models\GateBooking;
use App\Services\SupplierService;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    protected GateBookingService $bookingService;
    protected SupplierService $supplierService;
    protected int $user_id;

    public function __construct(GateBookingService $bookingService, SupplierService $supplierService)
    {
        $this->bookingService = $bookingService;
        $this->supplierService = $supplierService;
        $this->user_id = Auth::user()->id;
    }

    public function index(): View|Application|Factory
    {
        return view('supplier.index', [
            'cars' => [], // $this->supplierService->cars( false, 10),
            'drivers' => $this->supplierService->drivers( 'active', true, 10),
            'booking' => $this->bookingService->get_list(true, false, true, false, 1000, true),
            'bookingLast' => $this->bookingService->get_list(false, true, true, false, 10, true),
        ]);
    }

    public function profile(Request $request){}

    public function claim_add(Request $request): View|Application|Factory
    {
        return view('supplier.claim.add');
    }

    public function claim_add_with_invite(Request $request): View|Application|Factory
    {
        return view('supplier.claim.add');
    }

    public function claim(): View|Application|Factory
    {
        return view('supplier.claim.index', [
            'list' => $this->bookingService->get_list(false, false, true, 15, false, true),
        ]);
    }


    public function getAllAvailableIntervals(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'pallets_count' => 'required|integer|min:1',
            'gbort' => 'required|integer',
        ]);
        $slotStep = 15;
        $minutes_required = $this->bookingService->calculateRequiredMinutesTime($validated['pallets_count']);

        $dayStart = Carbon::createFromTime(8, 0);
        $dayEnd = Carbon::createFromTime(20, 0);
        $available = [];

        // Получаем ID ворот с нужным gbort
        $gateIds = DB::table('gates')
            ->where('gbort', $validated['gbort'])
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->pluck('id');

        // Занятые интервалы из бронирований (только нужные ворота)
        $busyBookings = DB::table('gate_bookings')
            ->whereIn('gate_id', $gateIds)
            ->whereDate('booking_date', $validated['date']->toDateString())
            ->whereNull('deleted_at')
            ->select('start_time', 'end_time')
            ->get();

        // Занятые глобальные периоды
        $busyPeriods = DB::table('busy_periods')
            ->whereNull('deleted_at')
            ->select('start_time', 'end_time')
            ->get();

        $busyIntervals = collect()->merge($busyBookings)->merge($busyPeriods);

        // Перебор временных окон
        $cursor = $dayStart->copy();
        while ($cursor->lt($dayEnd)) {
            $slotStart = $cursor->copy();
            $slotEnd = $slotStart->copy()->addMinutes($minutes_required);

            if ($slotEnd->gt($dayEnd)) {
                break;
            }

            $slotStartStr = $slotStart->format('H:i');
            $slotEndStr = $slotEnd->format('H:i');

            $conflict = $busyIntervals->contains(function ($interval) use ($slotStartStr, $slotEndStr) {
                return $slotStartStr < $interval->end_time && $slotEndStr > $interval->start_time;
            });

            if (!$conflict) {
                $available[] = ['start' => $slotStartStr, 'end' => $slotEndStr];
            }

            $cursor->addMinutes($slotStep);
        }

        return response()->json(['data' => $available]);
        # return $available;
    }



    /** Slots */
    public function findAvailableSlots(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'pallets_count' => 'required|integer|min:1',
            'gbort' => 'required|integer',
        ]);



        $hours = $this->bookingService->calculateRequiredHoursTime($validated['pallets_count']);
        $slots = $this->bookingService->getAvailableSlots(
            $validated['date'],
            $validated['pallets_count'],
            $validated['gbort']
        );


        return response()->json(['hours' => $hours,  'data' => $slots]);
    }

    public function claim_add_post(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'booking_date' => 'required|date|after_or_equal:today',
            'pallets_count' => 'required|integer|min:1',
            'gbort' => 'required|boolean',
            'weight' => 'required|numeric',
            'purpose' => 'required|string|max:255',
            'car_number' => 'required|string|max:20',
            'acceptances_id' => 'required|exists:acceptances,id',
            'car_type_id' => 'required|exists:car_types,id',
            'driver_id' => 'required|exists:drivers,id',
            'expeditor_id' => 'exists:expeditors,id',
            'is_internal' => 'boolean',
        ]);

        // Рассчитываем необходимое время
        $requiredHours = ceil($validated['pallets_count'] / 33);
        $requiredMinutes = $requiredHours * 60;

        // Ищем подходящие ворота
        $gates = Gate::where('is_active', true)
            ->where('gbort', $validated['gbort'])
            ->get();

        $date = Carbon::parse($validated['booking_date']);
        $preferredTime = isset($validated['preferred_time'])
            ? Carbon::parse($validated['preferred_time'])
            : null;

        $booked = false;
        $booking = null;

        foreach ($gates as $gate) {
            // Получаем все бронирования для этих ворот на выбранную дату
            $bookings = GateBooking::where('gate_id', $gate->id)
                ->whereDate('booking_date', $date)
                ->orderBy('start_time')
                ->get(['start_time', 'end_time']);

            // Добавляем границы рабочего дня
            $busyPeriods = collect([
                ['start' => '00:00:00', 'end' => '08:00:00'],
                ['start' => '19:00:00', 'end' => '23:59:59']
            ]);

            // Добавляем существующие бронирования
            $busyPeriods = $busyPeriods->merge($bookings->map(function($booking) {
                return [
                    'start' => Carbon::parse($booking->start_time)->format('H:i:s'),
                    'end' => Carbon::parse($booking->end_time)->format('H:i:s')
                ];
            }));

            // Сортируем периоды по времени начала
            $busyPeriods = $busyPeriods->sortBy('start')->values();

            $previousEnd = null;

            foreach ($busyPeriods as $period) {
                $currentStart = Carbon::parse($period['start']);
                $currentEnd = Carbon::parse($period['end']);

                if ($previousEnd === null) {
                    $previousEnd = $currentEnd;
                    continue;
                }

                // Находим свободное окно
                $freeWindowStart = $previousEnd;
                $freeWindowEnd = $currentStart;

                if ($freeWindowStart < $freeWindowEnd) {
                    $freeDuration = $freeWindowStart->diffInMinutes($freeWindowEnd);

                    if ($freeDuration >= $requiredMinutes) {
                        // Определяем время начала брони
                        $startTime = $freeWindowStart->copy();

                        // Если есть предпочтительное время, пытаемся найти ближайший слот
                        if ($preferredTime) {
                            $preferredStart = Carbon::createFromTime(
                                $preferredTime->hour,
                                $preferredTime->minute,
                                0
                            );

                            if ($preferredStart >= $freeWindowStart &&
                                $preferredStart->copy()->addMinutes($requiredMinutes) <= $freeWindowEnd) {
                                $startTime = $preferredStart;
                            }
                        }

                        // Создаем бронь
                        $booking = new GateBooking();
                        $booking->gate_id = $gate->id;
                        $booking->booking_date = $date;
                        $booking->start_time = $startTime->format('H:i:s');
                        $booking->end_time = $startTime->copy()->addMinutes($requiredMinutes)->format('H:i:s');
                        $booking->pallets_count = $validated['pallets_count'];
                        $booking->weight = $validated['weight'];
                        $booking->purpose = $validated['purpose'];
                        $booking->car_number = $validated['car_number'];
                        $booking->acceptances_id = $validated['acceptances_id'];
                        $booking->gbort = $validated['gbort'];
                        $booking->car_type_id = $validated['car_type_id'];
                        $booking->driver_id = $validated['driver_id'];
                        $booking->user_id = auth()->id();
                        $booking->expeditor_id = $validated['expeditor_id'];
                        $booking->is_internal = $validated['is_internal'] ?? false;
                        $booking->status = 'confirmed';

                        $booking->save();
                        $booked = true;
                        break 2; // Выходим из обоих циклов
                    }
                }

                $previousEnd = max($previousEnd, $currentEnd);
            }
        }

        if (!$booked) {
            return response()->json([
                'message' => 'Не удалось найти свободные ворота на указанную дату'
            ], 422);
        }

        return response()->json([
            'message' => 'Бронь успешно создана',
            'booking' => $booking,
            'gate_name' => $booking->gate->name
        ]);
    }
}
