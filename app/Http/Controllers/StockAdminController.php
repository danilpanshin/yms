<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGateBookingRequest;
use App\Models\AjaxJsonResponse;
use App\Models\Car;
use App\Models\CarStatus;
use App\Models\Driver;
use App\Models\FB_Corr;
use App\Models\FB_SupplierTransport;
use App\Models\Gate;
use App\Models\Supplier;
use App\Services\GateBookingService;
use App\Models\Expeditor;
use App\Models\GateBooking;
use App\Services\SmsService;
use App\Services\SupplierService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class StockAdminController extends Controller
{

    protected GateBookingService $bookingService;
    protected SupplierService $supplierService;

    public function __construct()
    {
        $this->bookingService = new GateBookingService();
        $this->supplierService = new SupplierService();
    }

    public function index(){
        $drivers = Driver::select('drivers.*', DB::raw('(SELECT COUNT(*) FROM gate_bookings as gb WHERE gb.driver_id = drivers.id) as gbc'))
            ->orderBy('drivers.id', 'desc')
            ->limit(10)
            ->get();

        $car_statuses = CarStatus::all();

        # $cars = Car::where('user_id', '=', Auth::user()->id)->get();
        $booking = GateBooking::select('gate_bookings.*', 'car_statuses.name as status',
            'drivers.name as driver_name', 'expeditors.name as expeditor_name', 'car_types.name as car_type_name',
            'acceptances.name as acceptance_name', 'gates.name as gate_name', 'suppliers.name as supplier_name'
        )
            ->where('gate_bookings.booking_date', '=', Carbon::now()->setHour(0)->setMinute(0)->setSecond(0))
            ->leftJoin('drivers', 'drivers.id', '=', 'gate_bookings.driver_id')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'gate_bookings.user_id')
            ->leftJoin('expeditors', 'expeditors.id', '=', 'gate_bookings.expeditor_id')
            ->leftJoin('acceptances', 'acceptances.id', '=', 'gate_bookings.acceptances_id')
            ->leftJoin('car_types', 'car_types.id', '=', 'gate_bookings.car_type_id')
            ->leftJoin('car_statuses', 'car_statuses.id', '=', 'gate_bookings.car_status_id')
            ->leftJoin('gates', 'gates.id', '=', 'gate_bookings.gate_id')
            ->orderBy('gate_bookings.booking_date')
            ->orderBy('gate_bookings.start_time')
            ->get();

        $bookingLast = GateBooking::select('gate_bookings.*', 'car_statuses.name as status',
            'drivers.name as driver_name', 'expeditors.name as expeditor_name', 'car_types.name as car_type_name',
            'acceptances.name as acceptance_name', 'gates.name as gate_name', 'suppliers.name as supplier_name'
        )
            ->where('gate_bookings.booking_date', '<', Carbon::now()->setHour(0)->setMinute(0)->setSecond(0))
            ->leftJoin('drivers', 'drivers.id', '=', 'gate_bookings.driver_id')
            ->leftJoin('expeditors', 'expeditors.id', '=', 'gate_bookings.expeditor_id')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'gate_bookings.user_id')
            ->leftJoin('acceptances', 'acceptances.id', '=', 'gate_bookings.acceptances_id')
            ->leftJoin('car_types', 'car_types.id', '=', 'gate_bookings.car_type_id')
            ->leftJoin('car_statuses', 'car_statuses.id', '=', 'gate_bookings.car_status_id')
            ->leftJoin('gates', 'gates.id', '=', 'gate_bookings.gate_id')
            ->orderBy('gate_bookings.booking_date', 'desc')
            ->orderBy('gate_bookings.start_time', 'desc')
            ->limit(10)
            ->get();

        $bookingLastAll = GateBooking::select('gate_bookings.*', 'gates.name as gate_name')
            ->where('gate_bookings.booking_date', '>', Carbon::now()->setHour(0)->setMinute(0)->setSecond(0))
            ->where('gate_bookings.booking_date', '<', Carbon::now()->addDays(7)->setHour(0)->setMinute(0)->setSecond(0))
            ->leftJoin('gates', 'gates.id', '=', 'gate_bookings.gate_id')
            ->orderBy('gate_bookings.booking_date', 'desc')
            ->orderBy('gate_bookings.start_time', 'desc')
            ->get();

        return view('stock_admin.index', compact('drivers', 'booking', 'bookingLast', 'bookingLastAll', 'car_statuses'));
    }

    public function supplier(Request $request)
    {
        # $res = SmsService::send('+7(926)-334-27-39', 'Тест 3');

        $list = new Supplier();
        if($request->get('s')){
            $list = $list->where(function (Builder $query) use ($request) {
                $query->where('name', 'like', '%'.$request->get('s').'%')
                    ->orWhere('email', 'like', '%'.$request->get('s').'%')
                    ->orWhere('phone', 'like', '%'.$request->get('s').'%')
                    ->orWhere('address', 'like', '%'.$request->get('s').'%')
                    ->orWhere('city', 'like', '%'.$request->get('s').'%')
                    ->orWhere('state', 'like', '%'.$request->get('s').'%')
                    ->orWhere('country', 'like', '%'.$request->get('s').'%')
                    ->orWhere('zip', 'like', '%'.$request->get('s').'%')
                    ->orWhere('inn', 'like', '%'.$request->get('s').'%')
                    ->orWhere('rs_id', 'like', '%'.$request->get('s').'%')
                    ->orWhere('one_ass_id', 'like', '%'.$request->get('s').'%');
            });
        }
        $list = $list->paginate(15);
        return view('stock_admin.supplier.index', [
            'list' => $list,
            'search_text' => $request->get('s'),
        ]);
    }

    public function supplier_ac(Request $request){
        $res = Supplier::where('name', 'like', '%'.$request->get('term').'%');
        $res->orWhere('email', 'like', '%'.$request->get('term').'%');
        $res->orWhere('phone', 'like', '%'.$request->get('term').'%');
        $res->orWhere('inn', 'like', '%'.$request->get('term').'%');
        $res->orderBy('id', 'desc')->limit(10);
        $res_arr = $res->get();
        $result = [];
        foreach($res_arr as $res_row){
            $result[] = [
                'id' => $res_row->id,
                'text' => "фио: {$res_row->name} | email: {$res_row->email} | тел.: {$res_row->phone} | инн.: {$res_row->inn}",
            ];
        }
        return response()->json(['results' => $result]);
    }

    public function history(Request $request){}

    public function claim(Request $request){
        $search_date = null;

        $gates = Gate::all();

        $car_statuses = CarStatus::all();

        $list = GateBooking::select('gate_bookings.*', 'car_statuses.name as status', 'drivers.name as driver_name',
            'expeditors.name as expeditor_name', 'car_types.name as car_type_name', 'acceptances.name as acceptance_name',
            'gates.name as gate_name', 'suppliers.name as supplier_name')
            ->leftJoin('drivers', 'drivers.id', '=', 'gate_bookings.driver_id')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'gate_bookings.user_id')
            ->leftJoin('expeditors', 'expeditors.id', '=', 'gate_bookings.expeditor_id')
            ->leftJoin('acceptances', 'acceptances.id', '=', 'gate_bookings.acceptances_id')
            ->leftJoin('car_types', 'car_types.id', '=', 'gate_bookings.car_type_id')
            ->leftJoin('car_statuses', 'car_statuses.id', '=', 'gate_bookings.car_status_id')
            ->leftJoin('gates', 'gates.id', '=', 'gate_bookings.gate_id');
        if($request->get('s')){
            $list = $list->where(function (Builder $query) use ($request) {
                $query->where('drivers.name', 'like', '%'.$request->get('s').'%')
                    ->orWhere('expeditors.name', 'like', '%'.$request->get('s').'%')
                    ->orWhere('gates.name', 'like', '%'.$request->get('s').'%')
                    ->orWhere('suppliers.name', 'like', '%'.$request->get('s').'%')
                    ->orWhere('acceptances.name', 'like', '%'.$request->get('s').'%')
                    ->orWhere('suppliers.name', 'like', '%'.$request->get('s').'%');
            });
        }
        if($request->get('d')){
            $search_date = Carbon::createFromFormat('Y-m-d', $request->get('d'))->format('Y-m-d');
            $list = $list->where('gate_bookings.booking_date', '=', $search_date);
        }

        if($request->get('cs')){
            $list = $list->where('gate_bookings.car_status_id', '=', $request->get('cs'));
        }

        if($request->get('g')){
            $list = $list->where('gate_bookings.gate_id', '=', $request->get('g'));
        }

        $list = $list->orderBy('gate_bookings.booking_date' ,'desc')
            ->orderBy('gate_bookings.start_time', 'desc')
            ->paginate(15)->withQueryString();
        return view('stock_admin.claim.index', [
            'list' => $list,
            'search_text' => $request->get('s'),
            'search_date' => $search_date,
            'gates' => $gates,
            'car_statuses' => $car_statuses,
            'search_gate' => $request->get('g'),
            'search_car_status' => $request->get('cs'),
        ]);
    }

    public function claim_add(Request $request)
    {

        return view('stock_admin.claim.add');
    }

    public function car(Request $request)
    {

        return redirect(route('supplier'));
    }


    /** Driver */
    public function driver(Request $request){
        $count['inactive'] = Driver::onlyTrashed()->count();
        $count['active'] = Driver::withoutTrashed()->count();
        if(Route::currentRouteName() == 'stock_admin.driver.with_trashed'){
            $list_arr_obj = Driver::onlyTrashed();
        } else {
            $list_arr_obj = Driver::withoutTrashed();
        }
        $list_arr_obj->select('drivers.*', 'suppliers.name as supplier_name');
        $list_arr_obj->leftJoin('suppliers', 'suppliers.id', '=', 'drivers.user_id');
        if($request->get('s')){
            $list_arr_obj->where('drivers.name', 'like', '%'.$request->get('s').'%')
                ->orWhere('drivers.email', 'like', '%'.$request->get('s').'%')
                ->orWhere('drivers.phone', 'like', '%'.$request->get('s').'%')
                ->orWhere('drivers.license_id', 'like', '%'.$request->get('s').'%')
                ->orWhere('suppliers.name', 'like', '%'.$request->get('s').'%')
            ;
        }
        $list_arr = $list_arr_obj
            ->orderBy('id', 'desc')
            ->orderBy('deleted_at', 'asc')
            ->paginate(15);
        return view('stock_admin.driver.index', [
            'list_arr' => $list_arr,
            'count' => $count,
            'search_text' => $request->get('s')
        ]);
    }

    public function driver_one($id){
        return response()->json(Driver::find($id));
    }

    public function driver_ac(Request $request){
        $sid = $request->get('sid');
        $res = Driver::select('id', 'name', 'license_id', 'email', 'phone');
        if($sid) {
            $res->where('user_id', '=', $sid);
        }
        if($request->get('term')) {
            $res->where('name', 'like', '%' . $request->get('term') . '%');
            $res->orWhere('license_id', 'like', '%' . $request->get('term') . '%');
            $res->orWhere('email', 'like', '%' . $request->get('term') . '%');
            $res->orWhere('phone', 'like', '%' . $request->get('term') . '%');
        }
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
            'email' => 'required|email',
            'sid' => 'integer|exists:suppliers,id',
        ]);
        $new = (new Driver())->fill($validated);
        $new->user_id = $validated['sid'] ?? null;
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

    public function driver_edit_post(Request $request, int $id = null){
        $id = $request->id ?? $id ?? null;

        $validated = $request->validate([
            'id' => 'integer',
            'name' => 'required|max:255',
            'phone' => '',
            'additional_phone' => '',
            'license_id' => '',
            'email' => 'required|email',
        ]);
        $validated['id'] = $id;
        $this->supplierService->driver_edit($validated);
        if($request->ajax()){
            return AjaxJsonResponse::make('ok');
        }
        return redirect()->back();
    }

    /** END Driver */

    /** Expeditor */
    public function expeditor(Request $request){
        $count['inactive'] = Expeditor::onlyTrashed()->count();
        $count['active'] = Expeditor::withoutTrashed()->count();
        if(Route::currentRouteName() == 'supplier.expeditor.with_trashed'){
            $list_arr_obj = Expeditor::onlyTrashed();
        } else {
            $list_arr_obj = Expeditor::withoutTrashed();
        }

        $list_arr_obj->select('expeditors.*', 'suppliers.name as supplier_name');
        $list_arr_obj->leftJoin('suppliers', 'suppliers.id', '=', 'expeditors.user_id');

        if($request->get('s')) {
            $list_arr_obj->where('suppliers.name', 'like', '%' . $request->get('s') . '%');
            $list_arr_obj->orWhere('suppliers.email', 'like', '%' . $request->get('s') . '%');
            $list_arr_obj->orWhere('suppliers.phone', 'like', '%' . $request->get('s') . '%');
            $list_arr_obj->orWhere('suppliers.name', 'like', '%'.$request->get('s').'%');
        }
        $list_arr = $list_arr_obj
            ->orderBy('suppliers.id', 'desc')
            ->orderBy('suppliers.deleted_at', 'asc')
            ->paginate(15);
        return view('stock_admin.expeditor.index', [
            'list_arr' => $list_arr,
            'count' => $count,
            'search_text' => $request->get('s'),
        ]);
    }

    public function supplier_add(Request $request){

        return view('stock_admin.supplier.add');
    }

    public function rs_supplier_ac(Request $request){
        $term = $request->get('term');
        $res_obj = FB_Corr::where('CORR_NAME', 'like', '%' . $term . '%')
            ->orWhere('CORR_INN', 'like', '%' . $term . '%')
            ->orWhere('CORR_ID', 'like', '%' . $term . '%');
        $res_obj->where(function (Builder $query) use ($request) {
            $query->where('CORR_ISACTIVE', '=', 1)
                ->where('CORR_ISTRANSCOMPANY', '=', 1);
        });
        $res = $res_obj->orderBy('CORR_ID', 'desc')
            ->limit(10)
            ->get();

        $result = [];
        foreach($res as $res_row){
            $result[] = [
                'id' => $res_row->CORR_ID,
                'text' => "id: {$res_row->CORR_ID} | фио: {$res_row->CORR_NAME} | inn: {$res_row->CORR_INN} | тел.: {$res_row->Phone} | email.: {$res_row->Email}",
            ];
        }

        return response()->json(['results' => $result]);
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
            'email' => 'required|email',
            'sid' => 'integer|exists:suppliers,id',
        ]);
        $new = (new Expeditor())->fill($validated);
        $new->user_id = $validated['sid'] ?? null;
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
        $validated['date'] = Carbon::createFromFormat('Y-m-d', $validated['date']);
        $arr = $this->getAllAvailableIntervals($validated['date'], ceil($validated['pallets_count'] / 60) * 60, $validated['gbort']);
        # dump($arr);
        # $hours = $this->bookingService->calculateRequiredHoursTime($validated['pallets_count']);
//        $slots = $this->bookingService->getAvailableSlots(
//            $validated['date'],
//            $validated['pallets_count'],
//            $validated['gbort']
//        );


        return response()->json(['hours' => [],  'data' => $arr]);
    }


    public function getAllAvailableIntervals(Carbon $bookingDate, int $durationMinutes, bool $gbort, int $slotStep = 15): array
    {
        $dayStart = Carbon::createFromTime(0, 0);
        $dayEnd = Carbon::createFromTime(24, 0);
        $available = [];

        // Получаем ID ворот с нужным gbort
        $gateIds = DB::table('gates')
            ->where('gbort', $gbort)
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->pluck('id');

        // Занятые интервалы из бронирований (только нужные ворота)
        $busyBookings = DB::table('gate_bookings')
            ->whereIn('gate_id', $gateIds)
            ->whereDate('booking_date', $bookingDate->toDateString())
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
            $slotEnd = $slotStart->copy()->addMinutes($durationMinutes);

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

        return $available;
    }

    public function findGateAndTimeSlot(
        Carbon $bookingDate,
        string $minStartTime,
        int $durationMinutes,
        bool $gbort,
        int $slotStep = 15
    ): ?array {
        $dayEnd = Carbon::createFromTime(24, 0);
        $slotCount = (int) ceil($durationMinutes / $slotStep);

        // Все подходящие ворота
        $gates = DB::table('gates')
            ->where('gbort', $gbort)
            ->whereNull('deleted_at')
            ->get();

        // Глобальные busy периоды
        $busyPeriods = DB::table('busy_periods')
            ->whereNull('deleted_at')
            ->select('start_time', 'end_time')
            ->get();

        foreach ($gates as $gate) {
            $bookings = DB::table('gate_bookings')
                ->where('gate_id', $gate->id)
                ->whereDate('booking_date', $bookingDate->toDateString())
                ->whereNull('deleted_at')
                ->select('start_time', 'end_time')
                ->get();

            $cursor = Carbon::createFromTimeString($minStartTime);

            while ($cursor->lt($dayEnd)) {
                $slotStart = $cursor->copy();
                $slotEnd = $slotStart->copy()->addMinutes($durationMinutes);

                if ($slotEnd->gt($dayEnd)) {
                    break;
                }

                $slotStartStr = $slotStart->format('H:i');
                $slotEndStr = $slotEnd->format('H:i');

                $hasConflict = false;

                foreach ($busyPeriods as $interval) {
                    if ($slotStartStr < $interval->end_time && $slotEndStr > $interval->start_time) {
                        $hasConflict = true;
                        break;
                    }
                }

                if (!$hasConflict) {
                    foreach ($bookings as $interval) {
                        if ($slotStartStr < $interval->end_time && $slotEndStr > $interval->start_time) {
                            $hasConflict = true;
                            break;
                        }
                    }
                }

                if (!$hasConflict) {
                    return [
                        'gate_id' => $gate->id,
                        'start' => $slotStartStr,
                        'end' => $slotEndStr,
                    ];
                }

                $cursor->addMinutes($slotStep);
            }
        }

        return null;
    }

    public function claim_add_post(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'booking_date' => 'required|date|after_or_equal:today',
            'pallets_count' => 'required|integer|min:1',
            'gbort' => 'required|boolean',
            'weight' => 'required|numeric',
            # 'purpose' => 'required|string|max:255',
            'car_number' => 'required|string|max:20',
            # 'acceptances_id' => 'required|exists:acceptances,id',
            'car_type_id' => 'required|exists:car_types,id',
            'driver_id' => 'exists:drivers,id',
            'expeditor_id' => 'exists:expeditors,id',
            # 'is_internal' => 'boolean',
            "supplier_id" => "required|integer|exists:suppliers,id",
            'start_time' => 'required|date_format:H:i',
        ]);

        $booked = false;

        $bdate = Carbon::createFromFormat('Y-m-d', $validated['booking_date']);
        $time_slot = $this->findGateAndTimeSlot($bdate, $validated['start_time'], ceil($validated['pallets_count'] / 60) * 60, $validated['gbort']);

        if($time_slot) {
            $validated['purpose'] = '';
            $validated['acceptances_id'] = 1;

            $booked = true;

            $_ = explode(':', $time_slot['start']);
            $_end = explode(':', $time_slot['end']);

            $date = $bdate;
            $startTimeSms = $bdate->setHour((int)$_[0])->setMinute((int)$_[1])->setSecond(0)->format('H:i');
            $startTime = $bdate->setHour((int)$_[0])->setMinute((int)$_[1])->setSecond(0)->format('H:i:s');
            $end_time = $bdate->setHour((int)$_end[0])->setMinute((int)$_end[1])->setSecond(0)->format('H:i:s');

            // Создаем бронь
            $booking = new GateBooking();
            $booking->gate_id = $time_slot['gate_id'];
            $booking->booking_date = $date->format('Y-m-d');
            $booking->start_time = $startTime;
            $booking->end_time = $end_time;
            $booking->pallets_count = $validated['pallets_count'];
            $booking->weight = $validated['weight'];
            $booking->purpose = $validated['purpose'];
            $booking->car_number = $validated['car_number'];
            $booking->acceptances_id = $validated['acceptances_id'];
            $booking->gbort = $validated['gbort'];
            $booking->car_type_id = $validated['car_type_id'];
            $booking->driver_id = $validated['driver_id'] ?? null;
            $booking->user_id = $validated['supplier_id'];
            $booking->expeditor_id = $validated['expeditor_id'] ?? null;
            $booking->is_internal = $validated['is_internal'] ?? false;
            $booking->car_status_id = 10;
            $booking->save();

            if($validated['driver_id']){
                $driver = Driver::find($validated['driver_id']);
                $gate = Gate::find($time_slot['gate_id']);
                SmsService::send($driver->phone,
                    'Создана заявка на логистику №' . $booking->id
                    . (($validated['car_number']) ? ', авто ' . $validated['car_number'] : '')
                    . ', время ' . $date->format('Y-m-d') . ' ' . $startTimeSms
                    . ', ворота ' . $gate->number);
            }

        }


        if (!$booked) {
            return response()->json([
                'message' => 'Не удалось найти свободные ворота на указанную дату и время'
            ], 422);
        }

        return response()->json([
            'message' => 'Бронь успешно создана',
            'booking' => $booking,
            'gate_name' => $booking->gate->name
        ]);
    }
}
