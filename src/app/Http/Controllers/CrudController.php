<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

trait CrudController
{
    public function add_post(Request $request): RedirectResponse
    {
        if(method_exists($this, 'add_validator')){
            $validatedData = $request->validate($this->add_validator());
        } else {
            $validatedData = $request->all();
        }

        $class = new $this->class();
        $class->fill($validatedData);
        $class->save();

        return redirect()->route($this->list_view);
    }

    public function edit_post(Request $request): RedirectResponse
    {
        if(method_exists($this, 'edit_validator')){
            $validatedData = $request->validate($this->edit_validator());
        } else {
            $validatedData = $request->all();
        }
        $class = new $this->class();
        $class = $class->find($request->id);
        $class->fill($validatedData);
        $class->save();

        return redirect()->route($this->list_view);
    }

    public function delete_post(Request $request){
        $class = new $this->class();
        $class = $class->find($request->id);
        $class->delete();

        return redirect()->route($this->list_view);
    }

    public function one($id){
        $class = new $this->class();
        return response()->json($class->find($id));
    }
}