<?php

namespace App\Models;

class Driver extends BaseModel
{
    protected $fillable = ['name', 'email', 'license_id', 'phone', 'additional_phone'];
}
