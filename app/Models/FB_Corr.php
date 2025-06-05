<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FB_Corr extends Model
{
    protected $connection = 'firebird';
    protected $table = 'CORR';
}
