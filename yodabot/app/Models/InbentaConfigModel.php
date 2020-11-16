<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InbentaConfigModel extends Model
{
    use HasFactory;

    protected $table = 'inbenta_config';

    protected $fillable = [
        'name', 'value'
    ];
}
