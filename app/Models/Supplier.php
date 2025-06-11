<?php

namespace App\Models;

class Supplier extends BaseModel
{
    protected $fillable = ['name', 'phone', 'email', 'address', 'city', 'state', 'country', 'zip', 'inn', 'rs_id', '1c_id'];

}
