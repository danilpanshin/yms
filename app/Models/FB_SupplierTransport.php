<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FB_SupplierTransport extends Model
{
    public $timestamps = false;
    protected $connection = 'firebird';
    protected $table = 'SUPPLIER_TRANSPORT';
}
