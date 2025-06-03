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
class Gate extends Model
{
    use SoftDeletes;

    protected $fillable = ['wh_number', 'number', 'name', 'comment'];

}
