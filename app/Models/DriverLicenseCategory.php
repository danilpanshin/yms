<?php

namespace App\Models;

class DriverLicenseCategory extends BaseModel
{
    protected $table = 'driver_license_category';

    protected $fillable = ['name', 'driver_license_category_id', 'description', 'example', 'literal'];
}
