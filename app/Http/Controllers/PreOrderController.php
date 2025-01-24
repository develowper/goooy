<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Pay;
use App\Http\Helpers\Telegram;
use App\Http\Helpers\Variable;
use App\Http\Requests\PreOrderRequest;
use App\Models\Admin;
use App\Models\Agency;
use App\Models\DrZantia\Cart;
use App\Models\DrZantia\PreOrder;
use App\Models\Order;
use App\Models\Setting;
use App\Models\ShippingMethod;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserFinancial;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PreOrderController extends Controller
{

    public function edit(Request $request, $id)
    {
        $user = $request->user();

        $data = PreOrder::find($id);

        $this->authorize('edit', [get_class($user), $data]);
        $data->items = json_decode($data->items);
        $data->products = $data->items ?? [];

        return Inertia::render('Panel/' . ($user instanceof Admin ? 'Admin/PreOrder' : 'Order') . '/Edit', [
            'statuses' => Variable::STATUSES,
            'data' => $data,

        ]);
    }

    public function userUpdate(Request $request)
    {
        $errorStatus = Variable::ERROR_STATUS;
        $successStatus = Variable::SUCCESS_STATUS;
        $user = $request->user();
        $data = PreOrder::find($request->id);
        if (!$data || $data->user_id != $user->id)
            return response()->json(['message' => __('order_not_found'), 'status' => $data->status,], $errorStatus);

        switch ($request->cmnd) {
            case 'pay':
                if (!$data->isPayable())
                    return response()->json(['message' => __('order_not_in_pay_status'), 'status' => $data->status,], $errorStatus);
                $now = Carbon::now();
                $payMethod = $request->payment_method ?? 'online';
                $description = sprintf(__('pay_orders_*_*'), $data->id, $user->phone);
                $response = ['order_id' => Carbon::now()->getTimestampMs(), 'status' => 'success', 'url' => route('user.panel.order.index')];

                if ($payMethod == 'wallet') {
                    $sum = $data->total_price;
                    $settingDebit = Setting::getValue("max_debit_$user->role") ?? 0;
                    $uf = UserFinancial::firstOrCreate(['user_id' => $user->id], ['wallet' => 0]);
                    $wallet = $uf->wallet ?? 0;
                    $maxDebit = $uf->max_debit ?? $settingDebit;
                    if (($wallet + $maxDebit) - $sum < 0)
                        return response()->json(['message' => sprintf(__('validator.min_wallet'), number_format($sum - ($wallet + $maxDebit)) . " " . __('currency'), $wallet)], Variable::ERROR_STATUS);

                } else
                    $response = Pay::makeUri(Carbon::now()->getTimestampMs(), "{$data->total_price}0", $user->fullname, $user->phone, $user->email, $description, $user->id, Variable::$BANK);

                $t = Transaction::where('for_type', 'pre-order')
                    ->where('for_id', $data->id)
                    ->where('from_type', 'user')
                    ->where('from_id', $user->id)->first();
                if ($t) {
                    $t->pay_id = $response['order_id'];
                    $t->amount = $data->total_price;
                    $t->payed_at = $payMethod == 'wallet' ? $now : null;
                    $t->pay_gate = $payMethod == 'online' ? Variable::$BANK : $payMethod;
                    $t->title = sprintf(($payMethod == 'wallet' ? __('pay_orders_wallet_*_*') : __('pay_orders_*_*')), $data->id, $user->phone);

                    $t->save();
                }
                if (!$t) {
                    $t = Transaction::create([
                        'title' => sprintf(($payMethod == 'wallet' ? __('pay_orders_wallet_*_*') : __('pay_orders_*_*')), $data->id, $user->phone),
                        'type' => "pay",
                        'pay_gate' => $payMethod == 'online' ? Variable::$BANK : $payMethod,
                        'for_type' => 'pre-order',
                        'for_id' => $data->id,
                        'from_type' => 'user',
                        'from_id' => $user->id,
                        'to_type' => 'agency',
                        'to_id' => 1,
                        'info' => null,
                        'coupon' => null,
                        'payed_at' => $payMethod == 'wallet' ? $now : null,
                        'amount' => $data->total_price,
                        'pay_id' => $response['order_id'],
                    ]);
                }
                if ($payMethod == 'wallet') {
                    $uf->wallet -= $data->total_price;
                    $uf->save();
                    $user->updateOrderNotifications();
                    $data->payed_at = $now;
                    $data->payment_method = 'wallet';
                    $data->transaction_id = $t->id;
                    $data->status = $data->status == 'pending' ? 'processing' : $data->status;
                    $data->save();
                    Telegram::log(null, 'transaction_created', $t);
                    Telegram::log(null, 'order_status_edited', $data);
                    return response(['status' => $data->status, 'payed_at' => $now->toDateTimeString(), 'wallet' => $uf->wallet ?? 0, 'message' => __('payed_successfully'), 'url' => $response['url']], Variable::SUCCESS_STATUS);

                }

                return response(['status' => $data->status, 'message' => __('redirect_to_payment_page'), 'url' => $response['url']], Variable::SUCCESS_STATUS);

                break;
        }
    }

    public function update(PreOrderRequest $request)
    {
        $response = ['message' => __('response_error')];
        $errorStatus = Variable::ERROR_STATUS;
        $successStatus = Variable::SUCCESS_STATUS;
        $id = $request->id;
        $cmnd = $request->cmnd;
        $status = $request->status;
        $user = $request->user();

        $data = PreOrder::find($id);

        if (!starts_with($cmnd, 'bulk'))
            $this->authorize('edit', [Admin::class, $data]);

        if ($cmnd) {
            switch ($cmnd) {
                case 'status':
//                    $availableStatuses = $data->getAvailableStatuses();
//
//                    if (!$availableStatuses->where('name', $status)->first())
//                        return response()->json(['message' => __('action_not_allowed'), 'status' => $data->status,], $errorStatus);

                    $data->status = $status;
                    $data->save();


                    return response()->json(['message' => __('updated_successfully'), 'status' => $data->status, 'statuses' => $data->getAvailableStatuses()], $successStatus);
                case 'delete':
                    $data->delete();
                    return response()->json(['message' => __('updated_successfully'), 'deleted' => true], $successStatus);
            }
        } elseif ($data) {


            $request->merge([
//                'cities' => json_encode($request->cities ?? [])
            ]);

            $items = $request->products;

            $data->total_price = $request->total_items_price + $data->change_price + $data->total_shipping_price;

//            foreach ($request->products as $product) {
//                $product->total_price = $product->qty * $product->price;
//            }
            $data = $data->fill($request->except('products'));
            $data->items = json_encode($request->products);

            $data->id = $request->id;

            $order = $data;

            if ($data->save()) {

                $order->products = $items;
                Telegram::log(null, 'preorder_edited', $order);

                return response()->json(['message' => __('updated_successfully'), 'order' => $order], $successStatus);
            } else
                return response()->json($response, $errorStatus);
        }

        return response()->json($response, $errorStatus);
    }

    public function create(PreOrderRequest $request)
    {
        $request->validate($request->rules(), $request->messages());


//        $cart = (new CartController())->update($request);
//        $cart = $cart ? $cart->getData() : null;
//        $cart = optional($cart)->cart ?? null;
//        dd($request->address);
        $user = auth('sanctum')->user();

        $cart = (object)$request->all();


        if (!$cart) {
            return response()->json(['message' => __('problem_in_create_order'), 'cart' => $cart], Variable::ERROR_STATUS);
        }

        if ($cart->errors) {
            return response()->json(['message' => __('please_correct_errors'), 'cart' => $cart], Variable::ERROR_STATUS);

        }
        if (count($cart->items) == 0) {
            return response()->json(['message' => __('cart_is_empty'), 'cart' => $cart], Variable::ERROR_STATUS);
        }

        //
        $preOrder = PreOrder::create([
            'user_id' => $user->id,
            'status' => 'request',
            'province_id' => $cart->province_id ?? null,
            'county_id' => $cart->county_id ?? null,
            'district_id' => $cart->district_id ?? null,
            'receiver_fullname' => $cart->receiver_fullname ?? null,
            'receiver_phone' => $cart->receiver_phone ?? null,
            'postal_code' => $cart->postal_code ?? null,
            'address' => $cart->address ?? null,
            'location' => ($cart->lat ?? false) && ($cart->lon ?? false) ? ($cart->lat . "," . $cart->lon) : null,
            'total_items' => $cart->total_items ?? 0,
            'total_items_price' => $cart->total_items_price ?? 0,
            'total_shipping_price' => $cart->total_shipping_price ?? null,
            'total_price' => $cart->total_price ?? 0,
            'change_price' => 0,
            'transaction_id' => null,
            'payed_at' => null,
            'agency_id' => 1,
            'items' => json_encode($cart->items)
        ]);

        $preOrderLog = $preOrder;
        $preOrderLog->items = $cart->items;
        Telegram::log(null, 'preorder_created', $preOrderLog);
        Cart::clear();
        return response(['status' => 'success', 'message' => __('your_order_registered_successfully'), 'url' => route('user.panel.preorder.index'), 'user' => $user], Variable::SUCCESS_STATUS);


    }

    protected
    function searchPanel(Request $request)
    {
        $userAdmin = $request->user();

        $search = $request->search;
        $page = $request->page ?: 1;
        $orderBy = $request->order_by ?: 'id';
        $dir = $request->dir ?: 'DESC';
        $paginate = $request->paginate ?: 24;
        $status = $request->status;
        $isFromAgency = $request->is_from_agency;
        $isToAgency = $request->is_to_agency;
        $query = PreOrder::query()->select('*');
        $agencies = [];
        if ($userAdmin instanceof Admin) {
            $myAgency = Agency::find($userAdmin->agency_id);
            $agencies = $userAdmin->allowedAgencies($myAgency)->get();
            $agencyIds = $agencies->pluck('id');
        }
        if ($search)
            $query = $query->whereIn('status', collect(Variable::ORDER_STATUSES)->filter(fn($e) => str_contains(__($e['name']), $search))->pluck('name'));
        if ($status)
            $query = $query->where('status', $status);

        if ($userAdmin instanceof Admin)
            $query->whereIntegerInRaw('agency_id', $agencyIds);
        if ($userAdmin instanceof User)
            $query->where('user_id', $userAdmin->id)->with('agency:id,name,phone');

//        $query->with('items.variation:id,name,weight,pack_id');


        $timeout = Setting::getValue('order_reserve_minutes') ?? 0;
        $now = Carbon::now();
        return tap($query->orderBy($orderBy, $dir)->paginate($paginate, ['*'], 'page', $page), function ($paginated) use ($agencies, $userAdmin, $timeout, $now) {
            return $paginated->getCollection()->transform(
                function ($item) use ($agencies, $userAdmin, $timeout, $now) {

                    if ($userAdmin instanceof Admin) {
                        $item->statuses = $item->getAvailableStatuses();
                        $item->setRelation('agency', $agencies->where('id', $item->agency_id)->first());
                    }
                    if ($timeout && $item->status == 'pending')
                        $item->pay_timeout = ($t = $now->diffInMinutes($item->created_at->addMinutes($timeout), false)) > 0 ? "$t " . __('minute') : null;

                    return $item;
                }

            );
        });
    }

    public function factor(Request $request, $id)
    {
        $user = $request->user();

        $data = PreOrder::find($id);

        $this->authorize('edit', [get_class($user), $data]);

        $agency = Agency::find($data->agency_id);

        if ($agency && !$agency->address)
            $agency->address = optional(Agency::find($agency->parent_id))->address;
        if (!$agency)
            Agency::find(1);
        if (($user instanceof Admin) && !$user->allowedAgencies(Agency::find($user->agency_id))->where('id', $data->agency_id)->exists())
            return response()->json(['message' => __('order_not_found'),], Variable::ERROR_STATUS);

        $data->order_id = "$data->id";
        $data->transaction = Transaction::where([
            'for_type' => 'pre-order',
            'for_id' => $data->id,
            'type' => 'pay'
        ])->whereNotNull('payed_at')->select('title', 'pay_id', 'payed_at', 'pay_gate')->first();
        $data->from = $agency;
        $data->to = (object)[
            'name' => $data->receiver_fullname,
            'phone' => $data->receiver_phone,
            'province_id' => $data->province_id,
            'county_id' => $data->province_id,
            'district_id' => $data->province_id,
            'postal_code' => $data->postal_code,
            'address' => $data->address,
        ];
        return Inertia::render('Panel/PreOrder/Factor', [
            'statuses' => Variable::STATUSES,
            'data' => $data,
            'error_message' => __('order_not_found'),
        ]);
    }
}
