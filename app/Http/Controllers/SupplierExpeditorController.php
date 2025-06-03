<?php

namespace App\Http\Controllers;

use App\Models\AjaxJsonResponse;
use App\Models\Expeditor;
use App\Services\GateBookingService;
use App\Services\SupplierService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class SupplierExpeditorController extends Controller
{
    protected GateBookingService $bookingService;
    protected SupplierService $supplierService;
    protected int $user_id;

    public function __construct(GateBookingService $bookingService, SupplierService $supplierService)
    {
        $this->user_id = Auth::user()->id;
        $this->bookingService = new $bookingService($this->user_id);
        $this->supplierService = new $supplierService($this->user_id);
    }

    public function expeditor(): View|Application|Factory
    {
        $status = (Route::currentRouteName() == 'supplier.driver.with_trashed' ? 'inactive' : 'active');
        return view('supplier.expeditor.index', [
            'list_arr' => $this->supplierService->expeditors($status, true, false, 15),
            'count' => [
                'active' => $this->supplierService->expeditors_count_active(),
                'inactive' => $this->supplierService->expeditors_count_inactive()
            ],
        ]);
    }

    public function expeditor_one($id){
        return response()->json($this->supplierService->expeditor($id));
    }

    public function expeditor_ac(Request $request){
        $res = Expeditor::where('user_id', '=', $this->user_id)->select('id', 'name', 'email', 'phone');
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

    public function expeditor_add_post(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'phone' => 'required',
            'email' => 'required|email'
        ]);
        $new = (new Expeditor())->fill($validated);
        $new->user_id = $this->user_id;
        $new->save();
        if($request->ajax()){
            return AjaxJsonResponse::make('ok');
        }
        return redirect()->back();
    }

    public function expeditor_delete_post(Request $request, int $id = null): RedirectResponse
    {
        $id = $request->id ?? $id ?? null;
        Expeditor::where('user_id', '=', $this->user_id)->find($id)?->delete();
        return redirect()->back();
    }

    public function expeditor_restore_post($id, Request $request): RedirectResponse
    {
        $id = $request->id ?? $id ?? null;
        Expeditor::withTrashed()->where('user_id', '=', $this->user_id)->find($id)?->restore();
        return redirect()->back();
    }

    public function expeditor_edit_post($id, Request $request): RedirectResponse
    {

        return redirect()->back();
    }
}