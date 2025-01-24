<?php

namespace App\Http\Requests;

use App\Http\Helpers\Util;
use App\Http\Helpers\Variable;
use App\Models\Admin;
use App\Models\Agency;
use App\Models\Catalog;
use App\Models\City;
use App\Models\Order;
use App\Models\Pack;
use App\Models\Product;
use App\Models\Repository;
use App\Models\Setting;
use App\Models\ShippingMethod;
use App\Models\User;
use App\Models\Variation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Morilog\Jalali\Jalalian;

class PreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {

        $user = $this->user();
        $editMode = (bool)$this->id;
        $id = $this->id;
        $regexLocation = "/^[-+]?[0-9]{1,7}(\\.[0-9]+)?,[-+]?[0-9]{1,7}(\\.[0-9]+)?$/";
        $allowedAgencies = [];
        $tmp = [];
        if ($user instanceof Admin) {

            $allowedAgencies = $user->allowedAgencies(Agency::find($user->agency_id))->pluck('id');
            $this->merge(['allowed_agencies' => $allowedAgencies]);
            array_merge($tmp, [
                'agency_id' => ['required', Rule::in($allowedAgencies),],
            ]);
        }
        $counties = City::where('parent_id', $this->county_id)->pluck('id');
        if (!$editMode) {
            $tmp = array_merge($tmp, [
                'items' => ['required', 'array', 'min:1'],

                'receiver_fullname' => ['required', 'max:200',],
                'receiver_phone' => ['required', 'max:30',],
                'address' => ['required', 'max:2048',],
                'province_id' => ['required', 'numeric', Rule::in(City::where('level', 1)->pluck('id'))],
                'county_id' => ['required', 'numeric', Rule::in(City::where('level', 2)->pluck('id'))],
                'district_id' => [Rule::requiredIf(count($counties) > 0), Rule::in($counties)],
                'postal_code' => ['required', 'numeric', 'digits_between:0,20'],
                'location' => ['nullable', "regex:$regexLocation",],
            ]);

        } elseif (!$this->cmnd && $editMode && $user instanceof Admin) {

            $data = Order::with('items')->findOrNew($id);
            $data->repository = Repository::with('shippingMethods')->findOrNew($data->repo_id);
            if ($data->repository && $data->repository->allow_visit) {
                $methods = $data->repository->getRelation('shippingMethods');
                $methods->prepend(ShippingMethod::find(1));
                $data->repository->setRelation('shippingMethods', $methods);
            }
            $this->merge(['data' => $data]);


            $shippingMethods = ShippingMethod::where('repo_id', $data->repo_id)->get();
            $tmp = array_merge($tmp, [
                'shipping_method_id' => ['nullable', Rule::in($shippingMethods->pluck('id')->merge(1))],
                'products' => ['required', 'array', 'min:1'],

                'receiver_fullname' => ['required', 'max:200',],
                'receiver_phone' => ['required', 'max:30',],
                'address' => ['required', 'max:2048',],
                'province_id' => ['required', 'numeric', Rule::in(City::where('level', 1)->pluck('id'))],
                'county_id' => ['required', 'numeric', Rule::in(City::where('level', 2)->pluck('id'))],
                'district_id' => [Rule::requiredIf(count($counties) > 0), Rule::in($counties)],
                'postal_code' => ['required', 'numeric', 'digits_between:0,20'],
                'location' => ['nullable', "regex:$regexLocation",],
            ]);


            $products = Catalog::get()->pluck('id');
            $isAuction = Setting::getValue('is_auction');

            $totalPrice = 0;
            $totalWeight = 0;
            $totalItemsPrice = 0;
            $totalItemsDiscount = 0;
            $tmpProducts = [];
            foreach ($this->products ?? [] as $idx => $product) {


                $tmp = array_merge($tmp, [
                    "products.$idx.id" => ['required', Rule::in($products)],
                    "products.$idx.qty" => ['required', 'numeric', 'gt:0',],

                ]);

            }

            $tmp = array_merge($tmp, [
                'total_shipping_price' => ['nullable', 'numeric', 'min:0'],
                'change_price' => ['nullable', 'numeric',],

            ]);

//        if ($this->cmnd == 'status') {
//            $tmp = array_merge($tmp, [
//                'status' => ['required', Rule::in(collect(Variable::ORDER_STATUSES)->pluck('name'))],
//            ]);
//        }

        }
        return $tmp;

    }

    public function messages()
    {
        $tmp = [];

        $tmp = array_merge($tmp, [
            'items.required' => sprintf(__("validator.required"), __('product')),
            'items.min' => sprintf(__("validator.min_items"), __('product'), 1, count($this->items ?? [])),

            'products.required' => sprintf(__("validator.required"), __('product')),
            'products.min' => sprintf(__("validator.min_items"), __('product'), 1, count($this->products ?? [])),


            'address.required' => sprintf(__("validator.required"), __('address')),
            'address.max' => sprintf(__("validator.max_len"), __('address'), 2048, mb_strlen($this->address)),
            'province_id.required' => sprintf(__("validator.required"), __('province')),
            'county_id.required' => sprintf(__("validator.required"), __('county')),
            'district_id.required' => sprintf(__("validator.required"), __('district/city')),
            'district_id.in' => sprintf(__("validator.invalid"), __('district/city')),
            'postal_code.required' => sprintf(__("validator.required"), __('postal_code')),
            'postal_code.numeric' => sprintf(__("validator.numeric"), __('postal_code')),

            'receiver_fullname.required' => sprintf(__("validator.required"), __('receiver_fullname')),
            'receiver_fullname.max' => sprintf(__("validator.max_len"), __('receiver_fullname'), 100, mb_strlen($this->receiver_fullname)),
            'receiver_fullname.min' => sprintf(__("validator.min_len"), 3, mb_strlen($this->receiver_fullname)),

            'receiver_phone.required' => sprintf(__("validator.required"), __('receiver_phone')),
            'receiver_phone.numeric' => sprintf(__("validator.numeric"), __('receiver_phone')),
            'receiver_phone.digits' => sprintf(__("validator.digits"), __('receiver_phone'), 11),

            'from_location.regex' => sprintf(__("validator.invalid"), __('location')),

            'total_shipping_price.min' => sprintf(__("validator.min"), __('shipping_price'), 0),

            'total_discount.min' => sprintf(__("validator.min"), __('discount'), 0),
            'total_discount.max' => sprintf(__("validator.max_amount"), __('discount'), $this->total_price + $this->total_discount, $this->total_discount),

            'total_price.min' => sprintf(__("validator.min"), __('total_price'), 0),

        ]);
        foreach ((array)$this->items ?? [] as $idx => $product)
            $tmp = array_merge($tmp, [
                "items.$idx.id.required" => sprintf(__("validator.required"), __('product')),

                "items.$idx.qty.required" => sprintf(__("validator.required"), __('qty')),
                "items.$idx.qty.min" => sprintf(__('validator.min_items'), '', 1, $product['qty'] ?? 0),
                "items.$idx.qty.max" => sprintf(__('validator.max_items'), '', floatval($product['max_allowed'] ?? 0), $product['qty']),
                "items.$idx.qty.gt" => sprintf(__("validator.gt"), __('qty'), 0),

                "items.$idx.grade.required" => sprintf(__("validator.required"), __('grade')),

                "items.$idx.pack_id.required" => sprintf(__("validator.required"), __('pack')),
                "items.$idx.pack_id.in" => sprintf(__("validator.invalid"), __('pack')),

                "items.$idx.weight.required" => sprintf(__("validator.required"), __('weight')),
                "items.$idx.weight.gt" => sprintf(__("validator.gt"), __('weight'), 0),
                "items.$idx.weight.in" => sprintf(__('validator.must_be'), __('weight'), 1),

                "items.$idx.price.required" => sprintf(__("validator.required"), __('fee')),
                "items.$idx.price.gt" => sprintf(__("validator.gt"), __('fee'), 0),

            ]);
        return $tmp;
    }
}
