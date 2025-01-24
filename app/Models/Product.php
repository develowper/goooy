<?php

namespace App\Models;

use App\Http\Helpers\Variable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'tags',
        'sell_count',
//        'in_repo',
//        'in_shop',
//        'price',
//        'auction_price',
        'charged_at',
        'rate',
        'status',
        'description',
//        'in_auction',
//        'repo_id',
        'weight',
    ];
    protected $casts = [

    ];

    public static function getImages($id)
    {

        $images = array_fill(0, Variable::VARIATION_IMAGE_LIMIT, null);

        if (!$id) return $images;
        $allFiles = Storage::allFiles("public/" . Variable::IMAGE_FOLDERS[Product::class] . "/$id");
        foreach ($allFiles as $idx => $path) {
            $images[$idx] = route('storage.products') . "/$id/" . basename($path, ""); //suffix=format
        }
        return $images;
    }
}
