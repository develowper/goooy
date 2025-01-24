<?php

namespace App\Http\Controllers;

use App\Http\Helpers\SMSHelper;
use App\Http\Helpers\Telegram;
use App\Http\Helpers\Util;
use App\Http\Helpers\Variable;
use App\Http\Requests\GuaranteeRequest;
use App\Models\Admin;
use App\Models\AdminFinancial;
use App\Models\Agency;
use App\Models\AgencyFinancial;
use App\Models\Car;
use App\Models\Product;
use App\Models\Sample;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Variation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Morilog\Jalali\Jalalian;

class GuaranteeController extends Controller
{


    //
    public function smsVerify(Request $request)
    {


        Telegram::log(null, 'sms_received', print_r($request->all(), true));
        $from = Util::makePhone($request->from);
        $text = $request->text ?? "";
        $text = explode(' ', $text);
        $date = Carbon::createFromTimestamp($request->date);

        if (count($text) != 2) return;
        $operator = Admin::where('phone', $from)->where('role', 'operator')->first();
        if (!$operator) return;
        $smsHelper = new SMSHelper();

        $barcode = Util::f2e(trim($text[0] ?? ""));
        $phone = Util::f2e(trim($text[1] ?? ""));

        $id = substr($barcode, 0, strlen($barcode) - 12);
        $sample = Sample::find($id);
        if (!$sample || !$sample->guarantee_months) {
            $smsHelper->send($from, __('guarantee') . '$' . sprintf(__('validator.invalid'), ''), 'item_status');
            return;
        }
        if (!$phone || strlen($phone) != 11 || !is_numeric($phone)) {
            $smsHelper->send($from, __('customer_phone') . '$' . sprintf(__('validator.invalid'), ''), 'item_status');
            return;
        }
        if ($sample->guarantee_expires_at) {
            $smsHelper->send($from, __('guarantee') . '$' . sprintf(__('validator.unique'), ''), 'item_status');
            return;
        }

        $customer = User::where('phone', $phone)->first();
        if (!$customer) {
            $customer = User::create([
                'phone' => $phone,
                'fullname' => "U$phone",
                'phone_verified' => true,
                'ref_id' => User::makeRefCode($phone),
            ]);

        }
        $agency = Agency::find($operator->agency_id);
        $guaranteeMonths = $sample->guarantee_months;
        $sample->guarantee_expires_at = Carbon::now()->addMonths($guaranteeMonths);
        $sample->operator_id = $operator->id;
        $sample->customer_id = $customer->id;
        $sample->save();
//        $sample->name = $variation->name;
        $sample->operator = $operator;
        $sample->agency = $agency;
        $sample->guarantee_expires_at_shamsi = Jalalian::fromCarbon($sample->guarantee_expires_at)->format('Y/m/d');
        Telegram::log(null, 'guarantee_created', $sample);
//        $product = Product::find($variation->product_id);

        //add operator percent

        if ($operator && $agency /*&& $variation*/) {
            $smsHelper = new SMSHelper();
            $smsHelper->send($phone, "$barcode\$$sample->guarantee_expires_at_shamsi", 'guarantee_started');
            SMSHelper::deleteCode($phone);
            $percent = Setting::getValue('operator_profit_percent') ?? 0;
            if ($percent > 0) {
                $t = Transaction::create([
                    'title' => sprintf(__('profit_operator_agency_*_*_*'), floor($percent), "$sample->id($sample->name)", "$operator->fullname($operator->id)"),
                    'type' => "profit",
                    'for_type' => 'operator',
                    'for_id' => $operator->id,
                    'from_type' => 'agency',
                    'from_id' => 1,
                    'to_type' => 'operator',
                    'to_id' => $operator->id,
                    'is_success' => true,
                    'info' => null,
                    'coupon' => null,
                    'payed_at' => Carbon::now(),
                    'amount' => floor($percent / 100 * $sample->price),
                    'pay_id' => null,
                ]);

                $agencyF = AdminFinancial::firstOrNew(['admin_id' => $operator->id]);
                $agencyF->wallet += $t->amount;
                $agencyF->save();

                $t->user = $operator;
                Telegram::log(null, 'transaction_created', $t);
            }
        }
    }

    public function create(GuaranteeRequest $request)
    {


        $agency = $request->agency;
        $phone = $request->phone;
        $operator = $request->operator;
        $operatorId = $request->operator_id;
        $barcode = $request->guarantee_code;
        $id = substr($barcode, 0, strlen($barcode) - 12);
        $sample = Sample::find($id);

        if (!$sample)
            return back()->withErrors(['message' => [sprintf(__('*_not_found'), __('sample'))]]);
        if (!$sample->guarantee_months)
            return back()->withErrors(['message' => [sprintf(__('*_not_found'), __('guarantee_months'))]]);
        if (!$sample->price)
            return back()->withErrors(['message' => [sprintf(__('*_not_found'), __('price'))]]);
//        $variation = Variation::find($sample->variation_id);
//        if (!$variation)
//            return back()->withErrors(['message' => [sprintf(__('*_not_found'), __('product'))]]);
        if ($sample->guarantee_expires_at)
            return back()->withErrors(['message' => [__('guarantee_registered_before')]]);


        $customer = User::where('phone', $phone)->first();
        if (!$customer) {
            $customer = User::create([
                'phone' => $phone,
                'fullname' => "U$phone",
                'phone_verified' => true,
                'ref_id' => User::makeRefCode($phone),
            ]);

        }
        $guaranteeMonths = $sample->guarantee_months;
        $sample->guarantee_expires_at = Carbon::now()->addMonths($guaranteeMonths);
        $sample->operator_id = $operatorId;
        $sample->customer_id = $customer->id;
        $sample->save();
//        $sample->name = $variation->name;
        $sample->operator = $operator;
        $sample->agency = $agency;
        $sample->guarantee_expires_at_shamsi = Jalalian::fromCarbon($sample->guarantee_expires_at)->format('Y/m/d');
        Telegram::log(null, 'guarantee_created', $sample);
//        $product = Product::find($variation->product_id);

        //add operator percent

        if ($operator && $agency /*&& $variation*/) {
            $smsHelper = new SMSHelper();
            $smsHelper->send($phone, "$barcode\$$sample->guarantee_expires_at_shamsi", 'guarantee_started');
            SMSHelper::deleteCode($phone);
            $percent = Setting::getValue('operator_profit_percent') ?? 0;
            if ($percent > 0) {
                $t = Transaction::create([
                    'title' => sprintf(__('profit_operator_agency_*_*_*'), floor($percent), "$sample->id($sample->name)", "$operator->fullname($operator->id)"),
                    'type' => "profit",
                    'for_type' => 'operator',
                    'for_id' => $operatorId,
                    'from_type' => 'agency',
                    'from_id' => 1,
                    'to_type' => 'operator',
                    'to_id' => $operatorId,
                    'is_success' => true,
                    'info' => null,
                    'coupon' => null,
                    'payed_at' => Carbon::now(),
                    'amount' => floor($percent / 100 * $sample->price),
                    'pay_id' => null,
                ]);

                $agencyF = AdminFinancial::firstOrNew(['admin_id' => $operatorId]);
                $agencyF->wallet += $t->amount;
                $agencyF->save();

                $t->user = $operator;
                Telegram::log(null, 'transaction_created', $t);
            }
        }
//        Telegram::log(null, 'guarantee_created', $variation);
        $res = ['flash_status' => 'success', 'flash_message' => __('done_successfully')];
        return to_route('admin.panel.guarantee.index')->with($res);
    }

    protected
    function searchPanel(Request $request)
    {
        $admin = $request->user();

        $search = $request->search;
        $page = $request->page ?: 1;
        $orderBy = $request->order_by ?: 'id';
        $dir = $request->dir ?: 'DESC';
        $paginate = $request->paginate ?: 24;
        $status = $request->status;
        $grade = $request->grade;

        $query = Sample::query()->select();
        $query->whereIntegerInRaw('agency_id', $admin->allowedAgencies(Agency::find($admin->agency_id))->pluck('id'));
        if ($search)
            $query = $query->where('name', 'like', "%$search%");
        if ($status)
            $query = $query->where('status', $status);
        if ($grade)
            $query = $query->where('grade', $grade);

        return $query->orderBy($orderBy, $dir)->paginate($paginate, ['*'], 'page', $page);


        $data = Sample::join('variations', function ($join) use ($admin, $search, $status) {
            $join->on('variations.id', '=', 'samples.variation_id')
                ->whereNotNull('samples.guarantee_expires_at')
                ->whereIntegerInRaw('variations.agency_id', $admin->allowedAgencies(Agency::find($admin->agency_id))->pluck('id'));

            if ($search)
                $join->where('name', 'like', "%$search%");
            if ($status)
                $join->where('status', $status);


        })->select('samples.id as id',
            'variations.product_id',
            'variations.repo_id as repo_id',
            'variations.name as name',
            'variations.pack_id as pack_id',
            'variations.grade as grade',
            'variations.price as price',
            'variations.auction_price as auction_price',
            'variations.auction_price as auction_price',
            'variations.weight as weight',
            'variations.in_auction as in_auction',
            'variations.in_shop as in_shop',
            'variations.product_id as parent_id',
            'variations.updated_at as updated_at',
            'samples.admin_id as admin_id',
            'samples.customer_id as customer_id',
            'samples.produced_at as produced_at',
            'samples.guarantee_months as guarantee_months',
            'samples.barcode as barcode',
            'samples.guarantee_expires_at as guarantee_expires_at',

        );


        return $data->orderBy($orderBy, $dir)->paginate($paginate, ['*'], 'page', $page);


    }
}
