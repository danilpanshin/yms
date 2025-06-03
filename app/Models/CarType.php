<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * @param int $id
 * @param string $name
 * @param string $comment
 * @param float $max_weight
 * @param int $max_pallets
 * @param int $tail_lift
 * @param int $active
 * @param string $created_at
 * @param string $updated_at
 * @param string $deleted_at
 */
class CarType extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'comment'];
}
