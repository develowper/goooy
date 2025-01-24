<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sample extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'agency_id',
        'product_id',
        'variation_id',
        'guarantee_months',
        'guarantee_expires_at',
        'produced_at',
        'barcode',
        'admin_id',
        'operator_id',
        'customer_id',
        'repo_id',
        'price',
    ];

    public static function makeBarcode($id, mixed $produced_at, mixed $guarantee_months)
    {
        $seperated = explode('/', $produced_at);
        $divideToDay = 1;
        foreach ($seperated as $idx => $item) {
            $seperated[$idx] = str_pad($item, 2, "0", STR_PAD_LEFT);

        }
        $produced_at = join('', $seperated);
        $guarantee_months = str_pad($guarantee_months, 2, "0", STR_PAD_LEFT);
        $res = "$id$produced_at$guarantee_months";

        $checksum = self::getChecksum($res);
        return "$res$checksum";
    }

    public static function validateBarcode($value)
    {
        if (!$value || strlen("$value") < 11) return false;
        $valueChecksum = substr($value, -2);
        $checksum = self::getChecksum(substr($value, 0, strlen($value) - 2));
        return $valueChecksum == $checksum;
    }

    public static function getChecksum($value)
    {
        $checksum = 0;
        $day = intval(substr($value, -4, 2)) ?? 1;

        foreach (str_split($value) as $idx => $char) {
            $checksum += ($char * ($idx + 1));
        }
        $res = round(($checksum / $day) % 100);
        return str_pad($res, 2, "0", STR_PAD_LEFT);
    }

}
