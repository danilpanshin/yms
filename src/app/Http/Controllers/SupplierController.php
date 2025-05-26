<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGateBookingRequest;
use App\Models\AjaxJsonResponse;
use App\Models\Car;
use App\Models\Driver;
use App\Models\Gate;
use App\Services\GateBookingService;
use App\Models\Expeditor;
use App\Models\GateBooking;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class SupplierController extends Controller
{
    protected GateBookingService $bookingService;

    public function __construct(GateBookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index(){
        $drivers = Driver::where('user_id', '=', Auth::user()->id)->orderBy('id', 'desc')->limit(10)->get();
        # $cars = Car::where('user_id', '=', Auth::user()->id)->get();
        $booking = GateBooking::select('gate_bookings.*', 'drivers.name as driver_name', 'expeditors.name as expeditor_name', 'car_types.name as car_type_name', 'acceptances.name as acceptance_name', 'gates.name as gate_name')
            ->where('gate_bookings.user_id', '=', Auth::user()->id)
            ->where('gate_bookings.booking_date', '>=', Carbon::now())
            ->leftJoin('drivers', 'drivers.id', '=', 'gate_bookings.driver_id')
            ->leftJoin('expeditors', 'expeditors.id', '=', 'gate_bookings.expeditor_id')
            ->leftJoin('acceptances', 'acceptances.id', '=', 'gate_bookings.acceptances_id')
            ->leftJoin('car_types', 'car_types.id', '=', 'gate_bookings.car_type_id')
            ->leftJoin('gates', 'gates.id', '=', 'gate_bookings.gate_id')
            ->orderBy('gate_bookings.booking_date')
            ->orderBy('gate_bookings.start_time')
            ->limit(1000)
            ->get();

        $bookingLast = GateBooking::select('gate_bookings.*', 'drivers.name as driver_name', 'expeditors.name as expeditor_name', 'car_types.name as car_type_name', 'acceptances.name as acceptance_name', 'gates.name as gate_name')
            ->where('gate_bookings.user_id', '=', Auth::user()->id)
            ->where('gate_bookings.booking_date', '<=', Carbon::now())
            ->leftJoin('drivers', 'drivers.id', '=', 'gate_bookings.driver_id')
            ->leftJoin('expeditors', 'expeditors.id', '=', 'gate_bookings.expeditor_id')
            ->leftJoin('acceptances', 'acceptances.id', '=', 'gate_bookings.acceptances_id')
            ->leftJoin('car_types', 'car_types.id', '=', 'gate_bookings.car_type_id')
            ->leftJoin('gates', 'gates.id', '=', 'gate_bookings.gate_id')
            ->orderBy('gate_bookings.booking_date', 'desc')
            ->orderBy('gate_bookings.start_time', 'desc')
            ->limit(10)
            ->get();

        return view('supplier.index', compact('drivers', 'booking', 'bookingLast'));
    }

    public function profile(Request $request){}

    public function history(Request $request){}

    public function claim_add(Request $request)
    {

        return view('supplier.claim.add');
    }

    public function car(Request $request)
    {

        return redirect(route('supplier'));
    }


    /** Driver */
    public function driver(){
        $count['inactive'] = Driver::onlyTrashed()->where('user_id', '=', Auth::user()->id)->count();
        $count['active'] = Driver::where('user_id', '=', Auth::user()->id)->count();
        if(Route::currentRouteName() == 'supplier.driver.with_trashed'){
            $list_arr_obj = Driver::onlyTrashed();
        } else {
            $list_arr_obj = Driver::withoutTrashed();
        }
        $list_arr = $list_arr_obj->where('user_id', '=', Auth::user()->id)->orderBy('id', 'desc')->orderBy('deleted_at', 'asc')->get();
        return view('supplier.driver.index', compact('list_arr', 'count'));
    }

    public function driver_one($id){
        return response()->json(Driver::where('user_id', '=', Auth::user()->id)->find($id));
    }

    public function driver_ac(Request $request){
        $res = Driver::where('user_id', '=', Auth::user()->id)->select('id', 'name', 'license_id', 'email', 'phone');
        $res->where('name', 'like', '%'.$request->get('term').'%');
        $res->orWhere('license_id', 'like', '%'.$request->get('term').'%');
        $res->orWhere('email', 'like', '%'.$request->get('term').'%');
        $res->orWhere('phone', 'like', '%'.$request->get('term').'%');
        $res->orderBy('id', 'desc')->limit(10);
        $res_arr = $res->get();
        $result = [];
        foreach($res_arr as $res_row){
            $result[] = [
                'id' => $res_row->id,
                'text' => "фио: {$res_row->name} | вод. права: {$res_row->license_id} | email: {$res_row->email} | тел.: {$res_row->phone}",
            ];
        }
        return response()->json(['results' => $result]);
    }

    public function driver_add_post(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'phone' => 'required',
            'license_id' => 'required',
            'email' => 'required|email'
        ]);
        $new = (new Driver())->fill($validated);
        $new->user_id = Auth::user()->id;
        $new->save();
        if($request->ajax()){
            return AjaxJsonResponse::make('ok');
        }
        return redirect()->back();
    }

    public function driver_delete_post($id, Request $request)
    {
        Driver::where('user_id', '=', Auth::user()->id)->find($request->id)?->delete();
        return redirect()->back();
    }

    public function driver_restore_post($id, Request $request){
        Driver::withTrashed()->where('user_id', '=', Auth::user()->id)->find($request->id)?->restore();
        return redirect()->back();
    }

    public function driver_edit_post($id, Request $request){

        return redirect()->back();
    }

    /** END Driver */

    /** Expeditor */
    public function expeditor(){
        $count['inactive'] = Expeditor::onlyTrashed()->count();
        $count['active'] = Expeditor::count();
        if(Route::currentRouteName() == 'supplier.expeditor.with_trashed'){
            $list_arr_obj = Expeditor::onlyTrashed();
        } else {
            $list_arr_obj = Expeditor::withoutTrashed();
        }
        $list_arr = $list_arr_obj->where('user_id', '=', Auth::user()->id)->orderBy('id', 'desc')->orderBy('deleted_at', 'asc')->get();
        return view('supplier.expeditor.index', compact('list_arr', 'count'));
    }

    public function expeditor_one($id){
        return response()->json(Expeditor::where('user_id', '=', Auth::user()->id)->find($id));
    }

    public function expeditor_ac(Request $request){
        $res = Expeditor::where('user_id', '=', Auth::user()->id)->select('id', 'name', 'email', 'phone');
        $res->where('name', 'like', '%'.$request->get('term').'%');
        $res->orWhere('email', 'like', '%'.$request->get('term').'%');
        $res->orWhere('phone', 'like', '%'.$request->get('term').'%');
        $res->orderBy('id', 'desc')->limit(10);
        $res_arr = $res->get();
        $result = [];
        foreach($res_arr as $res_row){
            $result[] = [
                'id' => $res_row->id,
                'text' => "фио: {$res_row->name} | email: {$res_row->email} | тел.: {$res_row->phone}",
            ];
        }
        return response()->json(['results' => $result]);
    }

    public function expeditor_add_post(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'phone' => 'required',
            'email' => 'required|email'
        ]);
        $new = (new Expeditor())->fill($validated);
        $new->user_id = Auth::user()->id;
        $new->save();
        if($request->ajax()){
            return AjaxJsonResponse::make('ok');
        }
        return redirect()->back();
    }

    public function expeditor_delete_post($id, Request $request)
    {
        Expeditor::where('user_id', '=', Auth::user()->id)->find($request->id)?->delete();
        return redirect()->back();
    }

    public function expeditor_restore_post($id, Request $request){
        Expeditor::withTrashed()->where('user_id', '=', Auth::user()->id)->find($request->id)?->restore();
        return redirect()->back();
    }

    public function expeditor_edit_post($id, Request $request){

        return redirect()->back();
    }

    /** END Expeditor */


    /** Slots */
    public function findAvailableSlots(Request $request)
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
