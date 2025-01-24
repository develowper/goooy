<?php

namespace App\Models\DrZantia;

use App\Models\Car;

class Cart
{

    public static ?Cart $instance;
    public int $total_items;
    public int $total_price;
    public int $total_items_price;
    public int $total_shipping_price;
    public string $error_message;
    public array $errors;
    public $address;
    public $items;


    static function instance(): Cart
    {
//        self::clear();
        return $instance ?? self::fromSession();
    }

    private static function fromSession(): Cart
    {


        self::$instance = new Cart();

        $defCart = (object)[
            'user_id' => optional(auth('sanctum')->user())->id,
            'ip' => request()->ip(),
            'address_idx' => null,
            'items' => (object)[],
        ];
        $cart = json_decode(session('dz_cart') ?? json_encode($defCart));


        foreach ($cart ?? [] as $key => $value) {
            self::$instance->$key = $value;
        }
        foreach ($defCart ?? [] as $key => $value) {

            self::$instance->$key = self::$instance->$key ?? $value;
        }
//        dd(self::$instance);
        return self::$instance;
    }

    static function save()
    {
        session()->put('dz_cart', json_encode(self::$instance));

    }

    public function items(): object
    {

//        dd(self::$instance);
        return (object)(self::$instance->items ?? []);
    }

    public function update(array $array)
    {
        foreach ($array as $idx => $item) {
            self::$instance->$idx = $item;
        }
        self::save();
        return self::$instance;
    }

    public function get($key)
    {

        return self::$instance->$key ?? null;
    }

    public function setItem($catalog, int $qty)
    {

        $items = $this->items();

        if (!$qty || $qty <= 0) {
            if (isset($items->{$catalog->id}))
                unset($items->{$catalog->id});
        } else {
            if (isset($items->{$catalog->id}))
                $items->{$catalog->id}->qty = $qty;
            else {
                $items->{$catalog->id} = (object)[
                    'name_fa' => $catalog->name_fa,
                    'name_en' => $catalog->name_en,
                    'pn' => $catalog->pn,
                    'image_url' => $catalog->image_url,
                    'image_indicator' => $catalog->image_indicator,
                    'price' => $catalog->price,
                    'qty' => $qty,
                ];
            }
        }
        self::$instance->items = $items;
        self::save();
        return self::$instance;
    }

    public static function clear()
    {
        self::$instance = null;
        self::save();
    }

    public function totalItems()
    {
        $c = 0;

        foreach (self::$instance->items() as $item) {
            $c += $item->qty;
        }
        return $c;
    }

}