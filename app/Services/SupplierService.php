<?php

namespace App\Services;

use App\Models\Car;
use App\Models\Driver;
use App\Models\Expeditor;
use Illuminate\Support\Facades\Auth;

class SupplierService
{
    protected int $user_id;

    public function __construct($user_id = null){
        $this->user_id = $user_id ?? Auth::user()->id;
    }

    public function drivers($status = 'all', $order = true, int $limit = null, int $paginate = null)
    {
        if($status == 'inactive'){
            $list_arr_obj = Driver::onlyTrashed();
        } elseif($status == 'active') {
            $list_arr_obj = Driver::withoutTrashed();
        } else {
            $list_arr_obj = Driver::withTrashed();
        }

        $list_arr_obj->where('user_id', '=', $this->user_id);

        if($order) {
            $list_arr_obj->orderBy('id', 'desc')->orderBy('deleted_at', 'asc');
        }

        if($paginate && $paginate > 0){
            return $list_arr_obj->paginate($paginate);
        } else if($limit && $limit > 0){
            return $list_arr_obj->limit($limit)->get();
        } else {
            return $list_arr_obj->get();
        }
    }

    public function driver(int $driver_id): Driver
    {
        return Driver::where('user_id', '=', $this->user_id)
            ->where('id', '=', $driver_id)
            ->first();
    }

    public function driver_add($values): Driver
    {
        $new = (new Driver())->fill($values);
        $new->user_id = $this->user_id;
        $new->save();

        return $new;
    }

    public function driver_edit($values): Driver
    {
        $driver = $this->driver($values['id']);
        $driver->fill($values);
        $driver->save();

        return $driver;
    }

    public function driver_delete(int $driver_id): void
    {
        Driver::where('user_id', '=', $this->user_id)
            ->find($driver_id)
            ?->delete();
    }

    public function driver_restore(int $driver_id): void
    {
        Driver::withTrashed()
            ->where('user_id', '=', $this->user_id)
            ->find($driver_id)
            ?->restore();
    }

    public function driver_ac($search, $limit = 10): array
    {
        $res = Driver::where('user_id', '=', $this->user_id)->select('id', 'name', 'license_id', 'email', 'phone');
        $res->where('name', 'like', '%'.$search.'%');
        $res->orWhere('license_id', 'like', '%'.$search.'%');
        $res->orWhere('email', 'like', '%'.$search.'%');
        $res->orWhere('phone', 'like', '%'.$search.'%');
        $res->orderBy('id', 'desc')->limit($limit);
        $res_arr = $res->get();
        $result = [];
        foreach($res_arr as $res_row){
            $result[] = [
                'id' => $res_row->id,
                'text' => "фио: {$res_row->name} | вод. права: {$res_row->license_id} | email: {$res_row->email} | тел.: {$res_row->phone}",
            ];
        }

        return $result;
    }

    public function drivers_count_active(): int
    {
        return Driver::withoutTrashed()->where('user_id', '=', $this->user_id)->count();
    }

    public function drivers_count_inactive(): int
    {
        return Driver::onlyTrashed()->where('user_id', '=', $this->user_id)->count();
    }








    public function expeditors($status = 'all', $order = true, int $limit = null, int $paginate = null)
    {
        if($status == 'inactive'){
            $list_arr_obj = Expeditor::onlyTrashed();
        } elseif($status == 'active') {
            $list_arr_obj = Expeditor::withoutTrashed();
        } else {
            $list_arr_obj = Expeditor::withTrashed();
        }

        $list_arr_obj =  $list_arr_obj->where('user_id', '=', $this->user_id);

        if($order) {
            $list_arr_obj->orderBy('id', 'desc')->orderBy('deleted_at', 'asc');
        }

        if($paginate && $paginate > 0){
            return $list_arr_obj->paginate($paginate);
        } else if($limit && $limit > 0){
            return $list_arr_obj->limit($limit);
        } else {
            return $list_arr_obj->get();
        }
    }

    public function expeditor(int $id): Expeditor
    {
        return Expeditor::where('user_id', '=', $this->user_id)->where('id', '=', $id)->first();
    }

    public function expeditor_add($values): Expeditor
    {
        $new = (new Expeditor())->fill($values);
        $new->user_id = $this->user_id;
        $new->save();

        return $new;
    }

    public function expeditor_delete($id): void
    {
        Expeditor::where('user_id', '=', $this->user_id)->find($id)?->delete();
    }

    public function expeditor_restore($id): void
    {
        Expeditor::withTrashed()->where('user_id', '=', $this->user_id)->find($id)?->restore();
    }

    public function expeditor_ac($search, $limit = 10): array
    {
        $res = Expeditor::where('user_id', '=', $this->user_id)->select('id', 'name', 'license_id', 'email', 'phone');
        $res->where('name', 'like', '%'.$search.'%');
        $res->orWhere('license_id', 'like', '%'.$search.'%');
        $res->orWhere('email', 'like', '%'.$search.'%');
        $res->orWhere('phone', 'like', '%'.$search.'%');
        $res->orderBy('id', 'desc')->limit($limit);
        $res_arr = $res->get();
        $result = [];
        foreach($res_arr as $res_row){
            $result[] = [
                'id' => $res_row->id,
                'text' => "фио: {$res_row->name} | вод. права: {$res_row->license_id} | email: {$res_row->email} | тел.: {$res_row->phone}",
            ];
        }

        return $result;
    }

    public function expeditors_count_active(): int
    {
        return Expeditor::withoutTrashed()->where('user_id', '=', $this->user_id)->count();
    }

    public function expeditors_count_inactive(): int
    {
        return Expeditor::onlyTrashed()->where('user_id', '=', $this->user_id)->count();
    }




//    public function expeditors($user_id, $order = true, $limit = false)
//    {
//        $user_id = $user_id ?? $this->user_id;
//
//        $res =  Expeditor::where('user_id', '=', $user_id);
//
//        if($order) {
//            $res->orderBy('id', 'desc');
//        }
//
//        if($limit && is_int($limit) && $limit > 0){
//            $res->limit($limit);
//        }
//
//        return $res->get();
//    }








    public function cars($order = true, $limit = false)
    {
        $res =  Car::where('user_id', '=', $this->user_id);

        if($order) {
            $res->orderBy('id', 'desc');
        }

        if($limit && is_int($limit) && $limit > 0){
            $res->limit($limit);
        }

        return $res->get();
    }

}