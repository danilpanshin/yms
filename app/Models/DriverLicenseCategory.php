<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @param int $id
 * @param string $fio
 * @param string $driver_licence
 * @param string $comment
 * @param int $active
 * @param string $created_at
 * @param string $updated_at
 * @param string $deleted_at
 */
class DriverLicenseCategory extends Model
{
    use SoftDeletes;

    protected $table = 'driver_license_category';

    protected $fillable = ['name', 'driver_license_category_id', 'description', 'example', 'literal'];
}
