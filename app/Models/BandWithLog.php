<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BandWithLog extends Model
{
    use HasFactory;
    protected $table = 'bandwidth_logs';
    protected $guarded = [];
}
