<?php

namespace App\Http\Controllers\DrZantia;

use App\Http\Controllers\Controller;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PreOrderController;
use App\Http\Helpers\Util;
use App\Http\Helpers\Variable;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\PreOrderRequest;
use App\Models\Admin;
use App\Models\Catalog;
use App\Models\DrZantia\Cart;
use App\Models\DrZantia\PreOrder;
use App\Models\RepositoryCartItem;
use App\Models\UserFinancial;
use App\Models\Variation;
use App\Models\Repository;
use App\Models\Setting;
use App\Models\ShippingMethod;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Morilog\Jalali\Jalalian;
use PHPUnit\Framework\Constraint\Count;
use stdClass;
use function App\Http\Controllers\str_starts_with;

class CartController extends Controller
{

    public function update(Request $request)
    {

//        DB::listen(function ($query) {
//            Log::info($query->sql);
//        });
        $user = auth('sanctum')->user();

        $ip = $request->ip();
        $productId = $request->variation_id;
        $qty = $request->qty;
        $cmnd = $request->cmnd;
        $cityId = session()->get('city_id');
        $addressIdx = $request->address_idx;
        $needAddress = true;
        $needSelfReceive = false;
        $paymentMethod = 'online';
        if (($user instanceof Admin) && ($productId || in_array($request->current, ['checkout.payment', 'checkout.shipping'])))
            return response()->json(['message' => __('admin_can_not_order')], Variable::ERROR_STATUS);
//        if ($cmnd == 'count') {
//            $product = Product::with('repository')->find($productId);
//            if (isset($qty) && !$product)
//                return response()->json(['message' => __('product_not_found')], Variable::ERROR_STATUS);
//
//            $repository = $product->getRelation('repository');
//            if (!$repository)
//                return response()->json(['message' => __('repository_not_found')], Variable::ERROR_STATUS);
//            if (!$cityId || !is_int($cityId))
//                return response()->json(['message' => __('select_city_from')], Variable::ERROR_STATUS);
//            if (!in_array($cityId, $repository->cities))
//                return response()->json(['message' => __('repository_not_support_city')], Variable::ERROR_STATUS);
//        }

        $cart = Cart::instance();
//        dd($cart);
        //set cart address
        $addresses = $user->addresses ?? [];
        //clear address
        if ($request->exists('address_idx') && $request->address_idx == null) {
            $cart->update(['address_idx' => null]);

            $cart->save();
        }
        $addressIdx = $addressIdx ?? $cart->get('address_idx');
        $addressIdx = $addressIdx !== null ? intval($addressIdx) : null;
        $address = null;
        if ($user && is_int($addressIdx) && $addressIdx >= 0 && count($addresses) > $addressIdx) {
            $address = $addresses[$addressIdx];
            $cityId = $address['district_id'] ?? $address['county_id'] ?? $cityId;
            $cityId = intval($cityId);
            if (isset($request->address_idx)) {
                session()->put('city_id', $cityId);
                $cart->update(['address_idx' => $addressIdx]);
            }
        }
        $cart->update(['address' => $address]);


//        $productRepositories = Repository::whereIn('id', $cartItems->pluck('product.repo_id'))->get();

        $beforeQty = 0;

        $cart->total_price = 0;
        $cart->total_items_price = 0;
        $cart->total_items = 0;
        $cart->total_shipping_price = 0;
        $errors = $cart->errors ?? [];
        //add/remove/update an item
        if ($productId && is_int($qty)) {

            $catalog = Catalog::where('id', $productId)->first();

            if ($catalog)
                $cart->setItem($catalog, $qty);
        }

        foreach ($cart->items() as $cartItem) {
//            dd($cartItems);

            $itemTotalPrice = $cartItem->qty * $cartItem->price;
//            $cartItem->save();
            $cartItem->total_price = $itemTotalPrice;
            $cart->total_items_price += $itemTotalPrice;
            $cart->total_items += $cartItem->qty;
            $cart->total_price += $itemTotalPrice;
        }
        $cart->total_price += $cart->total_shipping_price;

//        $cart->errors = $errors;


        //select shipping


        $needAddress = $needAddress && in_array($request->current, ['dz.checkout.payment', 'dz.checkout.shipping']);


        if ($needAddress && $address == null) {
            $cart->error_message = sprintf(__('validator.required'), __('address'));
            $errors[] = ['key' => 'address', 'type' => 'address', 'message' => $cart->error_message];

        }
        if (in_array($request->current, ['dz.checkout.payment', 'dz.checkout.shipping']) && $cart->total_items == 0) {

            $cart->error_message = __('cart_is_empty');
            $errors[] = ['key' => 'item', 'type' => 'item', 'message' => $cart->error_message];

        }
        $cart->errors = $errors ?? [];


        $cart->need_address = $needAddress;
        $cart->need_self_receive = $needSelfReceive;
        if (!$user) {
            session()->put('redirect_to', route('dz.checkout.cart'));
        }
        if ($request->cmnd == 'create_order' && count($cart->errors) == 0) {

//            return Http::post(route('dz.preorder.create'), (array)$cart);
            $cart->update($cart->address);

            $items = [];
            foreach ($cart->items as $key => $value) {
                $items[] = array_merge(['id' => $key], (array)$value);
            }
            $cart->items = $items;

            $req = new PreOrderRequest((array)$cart);
            return (new PreOrderController())->create($req);

            return app()->handle($req);
//            return app()->handle(PreOrderRequest::create(route('dz.preorder.create'), 'POST', (array)$cart));


        } else return response()->json(['message' => __('cart_updated'), 'cart' => $cart], Variable::SUCCESS_STATUS);
    }

    public
    function createCart()
    {

    }
}
