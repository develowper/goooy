<?php

namespace App\Models\DrZantia;

use App\Http\Helpers\Variable;
use App\Models\Agency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreOrder extends Model
{
    use HasFactory;

    protected $table = 'pre_orders';

    protected $fillable = [
        'items',
        'agency_id',
        'transaction_id',
        'user_id',
        'status',
        'province_id',
        'county_id',
        'district_id',
        'receiver_fullname',
        'receiver_phone',
        'postal_code',
        'address',
        'location',
        'total_items',
        'total_items_price',
        'total_shipping_price',
        'change_price',
        'total_price',
        'pay_type',

        'payed_at',];

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }

    public function getAvailableStatuses()
    {
        $statuses = collect(Variable::ORDER_STATUSES)->map(function ($e) {
            if ($e['name'] == 'canceled')
                $e['message'] = __('accept_cancel_order');
            elseif ($e['name'] == 'ready')
                $e['message'] = __('order_will_be_ready_send');
            elseif ($e['name'] == 'refunded')
                $e['message'] = __('order_will_cancel_and_refunded');
            elseif ($e['name'] == 'sending')
                $e['message'] = __('order_will_be_sending');
            elseif ($e['name'] == 'processing')
                $e['message'] = __('order_will_be_processing');
            elseif ($e['name'] == 'delivered')
                $e['message'] = __('order_will_be_delivered');
            elseif ($e['name'] == 'rejected')
                $e['message'] = __('order_will_cancel_and_refunded');

            return $e;
        });
        switch ($this->status) {
            case    'pending':
                return $statuses->whereIn('name', ['canceled']);
            case    'processing':
                return $statuses->whereIn('name', ['ready', 'refunded']);
            case    'ready':
                return $statuses->whereIn('name', ['processing', 'delivered', 'refunded']);
            case    'sending':
                return $statuses->whereIn('name', ['ready', 'delivered', 'rejected']);
            case    'delivered':
            case    'canceled':
                return $statuses->whereIn('name', []);
            case    'rejected':
                return $statuses->whereIn('name', []);
            case    'refunded':
                return $statuses->whereIn('name', []);
            default:
                return $statuses->whereIn('name', []);
        }
    }
}
