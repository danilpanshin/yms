<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class AdminUsersController extends Controller
{
    use CrudController;
    private string $class = User::class;

    private string $list_view = 'admin.user';

    private function edit_validator(): array
    {
        return [
            'email' => ['required', 'unique:users'],
            'name' => ['required'],
        ];
    }

    private function add_validator(): array
    {
        return [
            'email' => ['required', 'unique:users'],
            'name' => ['required'],
            'password' => ['required', 'min:8'],
        ];
    }

    public function index(){
        $roles = Role::all();
        $list_arr = User::with(['permissions', 'roles'])->get();
        return view('admin.user.index', compact('list_arr', 'roles'));
    }

    public function add_post(Request $request){
        $validatedData = $request->validate($this->add_validator());
        $class = new User();
        $class->fill($validatedData);
        $class->save();

        if($request->role and !empty($request->role)){
            foreach($request->role as $role){
                $role = (int)$role;
                if($role <= 3 and Auth::user()->hasRole(['admin', 'stock_admin'])){
                    $class->assignRole(Role::findOrFail($role));
                } else if ($role > 3){
                    $class->assignRole(Role::findOrFail($role));
                }
            }

        }

        return redirect()->route($this->list_view);
    }

    public function one($id){
        $roles = Role::all();
        $user = $this->class::with(['permissions', 'roles'])->find($id);
        $user->all_roles = $roles;
        $role_ids = [];
        foreach($user->roles as $role){
            $role_ids[] = $role['id'];
        }
        $user->role_ids = $role_ids;
        return response()->json($user);
    }
}
