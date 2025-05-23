<?php

namespace App\Http\Controllers;

use App\Models\Acceptance;
use Illuminate\Http\Request;

class AdminDictionaryAcceptanceController extends Controller
{
    use CrudController;
    private string $class = Acceptance::class;

    private string $list_view = 'admin.dictionary.acceptance';

    public function index(){
        $acceptances = Acceptance::orderBy('name', 'asc')->get();
        return view('admin.dictionary.acceptance', compact('acceptances'));
    }
}
