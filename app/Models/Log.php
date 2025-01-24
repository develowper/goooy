<?php

namespace App\Models;

use App\Http\Helpers\Util;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Log extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'model',
        'from',
        'to',
    ];


}