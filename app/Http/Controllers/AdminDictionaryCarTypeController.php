<?php

namespace App\Http\Controllers;

use App\Models\CarType;
use Illuminate\Http\Request;

class AdminDictionaryCarTypeController extends Controller
{
    use CrudController;
    private string $class = CarType::class;

    private string $list_view = 'admin.dictionary.car_type';

    public function index(){
        $car_types = CarType::orderBy('name', 'asc')->get();
        return view('admin.dictionary.car_types', compact('car_types'));
    }
}
