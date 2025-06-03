<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @param int $id
 * @param string $name
 * @param string $number
 * @param string $comment
 */
class Supplier extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'phone', 'email', 'address', 'city', 'state', 'country', 'zip', 'inn', 'rs_id', '1c_id'];

}
