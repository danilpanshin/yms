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
class Expeditor extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'email', 'phone'];
}
