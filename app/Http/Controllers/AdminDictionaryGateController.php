<?php

namespace App\Http\Controllers;

use App\Models\Gate;
use Illuminate\Http\Request;

class AdminDictionaryGateController extends Controller
{
    use CrudController;
    private string $class = Gate::class;

    private string $list_view = 'admin.dictionary.gate';

    public function index(){
        $gates = Gate::orderBy('number', 'asc')->get();
        return view('admin.dictionary.gates', compact('gates'));
    }
}
