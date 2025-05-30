<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGateBookingRequest;
use App\Models\AjaxJsonResponse;
use App\Models\Car;
use App\Models\Driver;
use App\Models\Gate;
use App\Models\Supplier;
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

class StockAdminController extends Controller
{
    protected GateBookingService $bookingService;
    protected $paginationTheme = 'bootstrap';

    public function __construct(GateBookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index(){
        $drivers = Driver::select('drivers.*', DB::raw('(SELECT COUNT(*) FROM gate_bookings as gb WHERE gb.driver_id = drivers.id) as gbc'))
            ->orderBy('drivers.id', 'desc')
            ->limit(10)
            ->get();
        # $cars = Car::where('user_id', '=', Auth::user()->id)->get();
        $booking = GateBooking::select('gate_bookings.*', 'car_statuses.name as status',
            'drivers.name as driver_name', 'expeditors.name as expeditor_name', 'car_types.name as car_type_name',
            'acceptances.name as acceptance_name', 'gates.name as gate_name', 'suppliers.name as supplier_name'
        )
            ->where('gate_bookings.booking_date', '>=', Carbon::now()->setHour(0)->setMinute(0)->setSecond(0))
            ->leftJoin('drivers', 'drivers.id', '=', 'gate_bookings.driver_id')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'gate_bookings.user_id')
            ->leftJoin('expeditors', 'expeditors.id', '=', 'gate_bookings.expeditor_id')
            ->leftJoin('acceptances', 'acceptances.id', '=', 'gate_bookings.acceptances_id')
            ->leftJoin('car_types', 'car_types.id', '=', 'gate_bookings.car_type_id')
            ->leftJoin('car_statuses', 'car_statuses.id', '=', 'gate_bookings.car_status_id')
            ->leftJoin('gates', 'gates.id', '=', 'gate_bookings.gate_id')
            ->orderBy('gate_bookings.booking_date')
            ->orderBy('gate_bookings.start_time')
            ->limit(15)
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

        return view('stock_admin.index', compact('drivers', 'booking', 'bookingLast', 'bookingLastAll'));
    }

    public function supplier()
    {
        return view('stock_admin.supplier.index', ['list' => Supplier::paginate(15)]);
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
        return view('stock_admin.claim.index', [
            'list' => GateBooking::select('gate_bookings.*', 'car_statuses.name as status', 'drivers.name as driver_name',
                'expeditors.name as expeditor_name', 'car_types.name as car_type_name', 'acceptances.name as acceptance_name',
                'gates.name as gate_name', 'suppliers.name as supplier_name')
                ->leftJoin('drivers', 'drivers.id', '=', 'gate_bookings.driver_id')
                ->leftJoin('suppliers', 'suppliers.id', '=', 'gate_bookings.user_id')
                ->leftJoin('expeditors', 'expeditors.id', '=', 'gate_bookings.expeditor_id')
                ->leftJoin('acceptances', 'acceptances.id', '=', 'gate_bookings.acceptances_id')
                ->leftJoin('car_types', 'car_types.id', '=', 'gate_bookings.car_type_id')
                ->leftJoin('car_statuses', 'car_statuses.id', '=', 'gate_bookings.car_status_id')
                ->leftJoin('gates', 'gates.id', '=', 'gate_bookings.gate_id')
                ->orderBy('gate_bookings.booking_date' ,'desc')
                ->orderBy('gate_bookings.start_time')
                ->paginate(15),
            'count' => [
                'active' => GateBooking::withoutTrashed()->where('user_id', '=', Auth::user()->id)->count(),
                'inactive' => GateBooking::onlyTrashed()->where('user_id', '=', Auth::user()->id)->count(),
            ]
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
    public function driver(){
        $count['inactive'] = Driver::onlyTrashed()->where('user_id', '=', Auth::user()->id)->count();
        $count['active'] = Driver::where('user_id', '=', Auth::user()->id)->count();
        if(Route::currentRouteName() == 'supplier.driver.with_trashed'){
            $list_arr_obj = Driver::onlyTrashed();
        } else {
            $list_arr_obj = Driver::withoutTrashed();
        }
        $list_arr = $list_arr_obj
            ->where('user_id', '=', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->orderBy('deleted_at', 'asc')
            ->paginate(15);
        return view('stock_admin.driver.index', compact('list_arr', 'count'));
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
        $list_arr = $list_arr_obj
            ->where('user_id', '=', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->orderBy('deleted_at', 'asc')
            ->paginate(15);
        return view('stock_admin.expeditor.index', compact('list_arr', 'count'));
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
            # 'purpose' => 'required|string|max:255',
            'car_number' => 'required|string|max:20',
            # 'acceptances_id' => 'required|exists:acceptances,id',
            'car_type_id' => 'required|exists:car_types,id',
            'driver_id' => 'required|exists:drivers,id',
            'expeditor_id' => 'exists:expeditors,id',
            # 'is_internal' => 'boolean',
            "supplier_id" => "required|integer|exists:suppliers,id",
            'start_time' => 'required|date_format:H:i',
        ]);

        $bdate = Carbon::createFromFormat('Y-m-d', $validated['booking_date']);

        $validated['purpose'] = '';
        $validated['acceptances_id'] = 1;
        # $validated['expeditor_id'] = null;
//        $gate = $this->bookingService->findRandomSlotForInterval(
//            $validated['booking_date'],
//            $validated['pallets_count'],
//            $validated['gbort'],
//            $validated['start_time']
//        );

        $requiredHours = $this->bookingService->calculateRequiredHoursTime($validated['pallets_count']);

        $gate_arr = Gate::all()->toArray();
        $gate = array_rand($gate_arr);
        $gate = $gate_arr[$gate];

        $booked = true;

        $_ = explode(':', $validated['start_time']);

        $date = $bdate;
        $startTime = $bdate->setHour((int)$_[0])->setMinute((int)$_[1])->setSecond(0);

        // Создаем бронь
        $booking = new GateBooking();
        $booking->gate_id = $gate['id'];
        $booking->booking_date = $date;
        $booking->start_time = $startTime->format('H:i:s');
        $booking->end_time = $startTime->copy()->addHours($requiredHours)->format('H:i:s');
        $booking->pallets_count = $validated['pallets_count'];
        $booking->weight = $validated['weight'];
        $booking->purpose = $validated['purpose'];
        $booking->car_number = $validated['car_number'];
        $booking->acceptances_id = $validated['acceptances_id'];
        $booking->gbort = $validated['gbort'];
        $booking->car_type_id = $validated['car_type_id'];
        $booking->driver_id = $validated['driver_id'];
        $booking->user_id = $validated['supplier_id'];
        $booking->expeditor_id = $validated['expeditor_id'];
        $booking->is_internal = $validated['is_internal'] ?? false;
        $booking->car_status_id = 10;

        $booking->save();


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
