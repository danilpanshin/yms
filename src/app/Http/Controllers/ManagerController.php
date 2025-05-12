<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ManagerController extends Controller
{
    public function index(){}

    public function tickets(){}

    public function ticket_edit(Request $request){}

    public function ticket_remove(Request $request){}

    public function ticket_add(Request $request){}

    ### SUPPLIER ###

    public function generate_supplier_link(Request $request){}

    public function suppliers(Request $request){}

    public function supplier_one(Request $request){}

    public function suppliers_edit(Request $request){}

    public function supplier_remove(Request $request){}

    public function suppliers_add(Request $request){}

    public function supplier_activate(Request $request){}

    public function supplier_deactivate(Request $request){}

    ### END SUPPLIER ###

    ### DRIVER ###

    public function generate_driver_link(Request $request){}

    public function drivers(Request $request){}

    public function driver_one(Request $request){}

    public function driver_edit(Request $request){}

    public function driver_remove(Request $request){}

    public function driver_add(Request $request){}

    public function driver_activate(Request $request){}

    public function driver_deactivate(Request $request){}

    ### END DRIVER ###
}
