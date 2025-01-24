<?php

namespace App\Models;

use App\Http\Helpers\Util;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Catalog extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_fa',
        'name_en',
        'pn',
        'price',
        'image_url',
        'image_indicator',
        'in_shop',
        'in_repo',
    ];

    static function seed()
    {
        set_time_limit(0);
        $i = 0;
        Catalog::truncate();
        $res = Util::fromCSV(Storage::path('catalog.csv'));
        foreach ($res as $row) {
            $row['price'] = intVal($row['price'] ?? 0);
            $row['in_shop'] = intVal($row['in_shop'] ?? 0);
            $row['in_repo'] = intVal($row['in_repo'] ?? 0);
            $row['image_indicator'] = $row['image_indicator'] ?? null;
            Catalog::create($row);
            echo "$i created " . PHP_EOL;
            $i++;
        }
    }

}
