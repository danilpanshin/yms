<?php

namespace App\Http\Controllers;

use App\Models\AjaxJsonResponse;
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

class SupplierDriverController extends Controller
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

    /** Driver */
    public function driver(): View|Application|Factory
    {
        $status = (Route::currentRouteName() == 'supplier.driver.with_trashed' ? 'inactive' : 'active');
        return view('supplier.driver.index', [
            'list_arr' => $this->supplierService->drivers($status, true, false, 15),
            'count' => [
                'active' => $this->supplierService->drivers_count_active(),
                'inactive' => $this->supplierService->drivers_count_inactive()
            ],
        ]);
    }

    public function driver_one($id): JsonResponse
    {
        return response()->json($this->supplierService->driver($id));
    }

    public function driver_ac(Request $request): JsonResponse
    {
        return response()->json([
            'results' => $this->supplierService->driver_ac($request->get('term'), 10)
        ]);
    }

    public function driver_add_post(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'phone' => 'required',
            'license_id' => 'required',
            'email' => 'required|email'
        ]);
        $this->supplierService->driver_add($validated);
        if($request->ajax()){
            return AjaxJsonResponse::make('ok');
        }
        return redirect()->back();
    }

    public function driver_edit_post(Request $request, int $id = null): JsonResponse|RedirectResponse
    {
        $id = $request->id ?? $id ?? null;

        $validated = $request->validate([
            'id' => 'integer',
            'name' => 'required|max:255',
            'phone' => '',
            'license_id' => '',
            'email' => 'required|email'
        ]);
        $validated['id'] = $id;
        $this->supplierService->driver_edit($validated);
        if($request->ajax()){
            return AjaxJsonResponse::make('ok');
        }
        return redirect()->back();
    }

    public function driver_delete_post($id, Request $request): RedirectResponse
    {
        $id = $request->id ?? $id ?? null;
        $this->supplierService->driver_delete($id);
        return redirect()->back();
    }

    public function driver_restore_post($id, Request $request): RedirectResponse
    {
        $id = $request->id ?? $id ?? null;
        $this->supplierService->driver_restore($id);
        return redirect()->back();
    }
}