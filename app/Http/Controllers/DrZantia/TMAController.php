<?php

namespace App\Http\Controllers\DrZantia;

use App\Helpers\Helper;
use App\Helpers\SmsHelper;
use App\Helpers\Telegram;
use App\Http\Controllers\Api\v2\PaymentController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Wallet\PayRequest;
use App\Models\Adv;
use App\Models\Catalog;
use App\Models\Plan;
use App\Models\PreUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class TMAController extends Controller
{

//    public function __construct()
//    {
//        Inertia::setRootView('tma.app');
//    }

    public function index(Request $request)
    {

        return Inertia::render('DZ/Index', [
            'catalog_count' => Catalog::count(),
            'tutorials' => [
                'آموزش دریافت ابلاغیه' => 'https://www.youtube.com/watch?v=BMhxPOjye9E',
                'آموزش فراموشی رمز ثنا' => 'https://www.youtube.com/watch?v=wqnQD-2fUrk',
            ],
            'links' => [
                'لینک دریافت ابلاغیه' => 'vip.eblagh',
                'لینک فراموشی رمز ثنا' => 'vip.sana',
            ],
        ]);

    }

    public function shop(Request $request)
    {

        return Inertia::render('Shop', [
            'prices' => Helper::$PRICES
        ]);

    }

    public function validation(Request $request)
    {
        $initData = $request->init_data;
        $data_check_arr = explode('&', rawurldecode($initData));
        $needle = 'hash=';
        $check_hash = FALSE;
        $telegram_id = null;
//        auth()->logout();
        $user = auth('sanctum')->user() ?? auth('api')->user();
        foreach ($data_check_arr as &$val) {

            if (substr($val, 0, strlen($needle)) === $needle) {
                $check_hash = substr_replace($val, '', 0, strlen($needle));
                $val = NULL;
            } elseif (substr($val, 0, strlen('user=')) === 'user=') {
                $u = json_decode(substr_replace($val, '', 0, strlen('user=')));
                $telegram_id = $u->id;

            }
        }

// if( $check_hash === FALSE ) return FALSE;
        $data_check_arr = array_filter($data_check_arr);
        sort($data_check_arr);

        $data_check_string = implode("\n", $data_check_arr);
        $secret_key = hash_hmac('sha256', env('TELEGRAM_BOT_DRZANTIA'), "WebAppData", TRUE);
        $hash = bin2hex(hash_hmac('sha256', $data_check_string, $secret_key, TRUE));
//        Telegram::sendMessage(Telegram::LOGS[0], $initData);
        $success = strcmp($hash, $check_hash) === 0;

        if ($success) {
            if ($user) {
                if (!$user->telegram_id) {
                    $user->telegram_id = $telegram_id;
                    $user->save();
                }
            }
            if (!$user) {
                $user = User::whereNotNull('telegram_id')->where('telegram_id', $telegram_id)->first();

                if ($user) {

                    auth()->loginUsingId($user->id);
                }
            }
        }

        session()->put('telegram_id', $telegram_id);
        return response()->json(['status' => $success ? 'success' : 'danger', 'user' => $user, 'result' => $success], $success ? 200 : 401);

    }

    public function loginForm(Request $request)
    {
        return Inertia::render('Auth/Login', [

        ]);
    }

    public function registerForm(Request $request)
    {
        return Inertia::render('Auth/Register', [

        ]);
    }

    public function logout(Request $request)
    {
        $user = auth()->user() ?? auth('api')->user();
        if ($user) {
            $user->telegram_id = null;
            $user->save();
            auth()->logout();
        }
        return response()->json(['status' => 'success',], Helper::STATUS_SUCCESS);

    }

    public function login(Request $request)
    {
        $telegram_id = $request->telegram_id;
        $user = auth()->user() ?? auth('api')->user();

        if ($telegram_id && !$request->exists('login')) { //login with telegram id
            if (!$user) {
                $user = User::whereNotNull('telegram_id')->where('telegram_id', $telegram_id)->first();

                if ($user) {
                    auth()->loginUsingId($user->id);
                }
            }
            if ($user)
                $user->plan_days = $user->getPlanDays();
            return response()->json(['status' => $user ? 'success' : 'danger', 'user' => $user], $user ? Helper::STATUS_SUCCESS : Helper::STATUS_ERROR);

        }


        $this->ensureIsNotRateLimited($request);

        $request->validate([

        ], [
            'login' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'max:100'],
            'telegram_id' => ['nullable', 'string', 'max:15'],
        ]);

        $loginCol = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';
        $errorMessage = __('error_auth');
        if (!Auth::guard('web')->attemptWhen(
            [
                $loginCol => $request->login,
                'password' => $request->password,
//                 fn (Builder $query) => $query->has('activeSubscription'),
            ], function ($user) use ($errorMessage) {
            if ($user->is_block) {
                $errorMessage = __('user_is_blocked');
                return false;
            }
            return true;
        }, $request->boolean('remember'))
        ) {
            RateLimiter::hit($this->throttleKey());

            $user = auth()->user() ?? auth('api')->user();


            if (!$user && PreUser::whereVerifyCode($request->password)
                    ->whereMobile($request->login)
                    ->exists()) {
                $user = User::where('mobile', $request->login)->first();
                if ($user)
                    auth()->loginUsingId($user->id);
                PreUser::whereVerifyCode($request->password)
                    ->whereMobile($request->login)
                    ->delete();
            } else {
                throw ValidationException::withMessages([
                    'login' => $errorMessage,
                ]);
            }
        }

        RateLimiter::clear($this->throttleKey());

        $request->session()->regenerate();
        $user = auth()->user() ?? auth('api')->user();
        if ($user) {
            if ($user->telegram_id != $telegram_id) {
                $user->telegram_id = $telegram_id;
                $user->save();
            }
            $user->plan_days = $user->getPlanDays();
        }
//        return redirect()->intended(route('tma'));
        return response()->json(['status' => $user ? 'success' : 'danger', 'user' => $user], $user ? Helper::STATUS_SUCCESS : Helper::STATUS_ERROR);

    }

    public function register(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string', 'max:100', 'regex:/^09[0-9]+$/', Rule::unique('users', 'mobile')],
            'code' => ['required', 'string', 'max:6'],
            'password' => ['required', 'min:5', 'max:20'],
            'fullname' => ['required', 'string', 'max:100'],
            'telegram_id' => ['nullable', 'string', 'max:15'],
        ], [
            'fullname.required' => sprintf(__("validator.required"), __('fullname')),
            'fullname.max' => sprintf(__("validator.max_len"), __('fullname'), 100, mb_strlen($request->fullname)),
            'fullname.unique' => sprintf(__("validator.unique"), __('fullname')),

            'phone.required' => sprintf(__("validator.required"), __('phone')),
            'phone.unique' => sprintf(__("validator.unique"), __('phone')),
            'phone_verify.required' => sprintf(__("validator.required"), __('phone_verify')),
            'phone_verify.exists' => sprintf(__("validator.invalid"), __('phone_verify')),
            'phone.numeric' => 'شماره تماس باید عدد باشد',
            'phone.digits' => 'شماره تماس  11 رقم و با 09 شروع شود',
            'phone.regex' => 'شماره تماس  11 رقم و با 09 شروع شود',

        ]);
        $preUser = PreUser::whereVerifyCode($request->get('code'))
            ->whereMobile($request->get('phone'))
            ->first();
        if ($preUser) {

            $user = new User;
            $user->mobile = $request->phone;
            $user->fullname = $request->fullname;
            $user->password = bcrypt($request->password);
            $user->telegram_id = $request->telegram_id;

            $user->market = 'telegram';
            $user->marketer_code = $request->marketer_code;

//            $user->push_id = $request->get('push_id');

            $user->email = '';
            $user->tel = '';
            $user->address = '';
            $user->lawyer_number = '';
            $user->lawyer_melicard = '';
            $user->cv = '';
            $user->city_id = null;

            $user->is_lawyer = 0;
            if ($request->has('is_lawyer')) {
                $user->is_lawyer = 1;
            }
            $user->app_version = '1.0.0';
            $user->is_verify = 1;
            $user->is_block = 0;
            $user->marketing_code = User::makeRefCode($user->mobile);
            $user->login_at = Carbon::now();
            $user->save();
            $user->plan_days = 0;
            $preUser->delete();

            auth()->loginUsingId($user->id);


            Telegram::log(null, 'user_created', $user);
            return response()->json(['status' => 'success', 'message' => 'ثبت نام با موفقیت انجام شد', 'user' => $user], Helper::STATUS_SUCCESS);

        } else {
            return response()->json(['status' => 'danger', 'message' => 'کد وارد شده صحیح نیست لطفا کد را صحیح وارد کنید',], Helper::STATUS_ERROR);
        }

    }

    public function manage(Request $request, $cmnd)
    {
        $user = auth()->user() ?: auth('api')->user();
        $hasPlan = !$user->getPlanDays() ?? false;
        switch ($cmnd) {
            case 'vip.eblagh':
                return response()->json(['status' => $hasPlan ? 'success' : 'danger', 'url' => "https://adliran.ir/Home/SelectLink/2", 'result' => $hasPlan ? 'success' : 'end_sub'], $hasPlan ? Helper::STATUS_SUCCESS : Helper::STATUS_ERROR);
                break;
            case 'vip.sana':
                return response()->json(['status' => $hasPlan ? 'success' : 'danger', 'url' => "https://sana.adliran.ir/Sana/Index#/Main", 'result' => $hasPlan ? 'success' : 'end_sub'], $hasPlan ? Helper::STATUS_SUCCESS : Helper::STATUS_ERROR);
                break;
        }

        foreach (Helper::$PRICES as $price) {
            if ($cmnd == $price['key']) {
                if (!isset($request->market) || $request->market == 'bank') {
                    $title = $price['name'];
                    $price = $price['price'];
                    $res = (new PaymentController())->getPayUrl(new PayRequest(['title' => $title, 'amount' => $price]));
                    $res = $res->getData() ?? (object)['url' => null, 'status' => 'danger', 'message' => __('check_network_and_retry')];
                    return response()->json(['status' => $res->url ? 'success' : 'danger', 'message' => $res->message, 'url' => $res->url], $res->url ? Helper::STATUS_SUCCESS : Helper::STATUS_ERROR);

                }

            }
        }
    }

    public function ensureIsNotRateLimited($request): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($request));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower(request()->input('login')) . '|' . request()->ip());
    }

    public function sendSMS(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric|digits:11|regex:/^09[0-9]+$/',
        ], [
            'phone.required' => 'شماره تماس نمی تواند خالی باشد',
            'phone.numeric' => 'شماره تماس باید عدد باشد',
            'phone.digits' => 'شماره تماس  11 رقم و با 09 شروع شود',
            'phone.regex' => 'شماره تماس  11 رقم و با 09 شروع شود',

        ]);
        $preUser = PreUser::where('mobile', $request->phone)->where('created_at', '>', Carbon::now()->subMinutes(5))->first();

        if ($preUser) {
            return response()->json(['status' => 'danger', 'message' => 'شما در 5 دقیقه اخیر یک پیامک دریافت کرده اید'], Helper::STATUS_ERROR);
        } else {
            PreUser::where('mobile', $request->phone)->delete();
            $code = Helper::generateRandomNumber(4);
            $preUser = new PreUser;
            $preUser->mobile = $request->phone;
            $preUser->verify_code = $code;
            $preUser->save();
            $res = (new SmsHelper())->SendFast($request->phone, $code);
            $message = 'پیامک برای شما ارسال شد';
            if ($request->type == 'forget') {
                $message = 'رمز موقت برای شما ارسال شد';
            }
            if ($res)
                return response()->json(['status' => 'success', 'message' => $message], Helper::STATUS_SUCCESS);
            return response()->json(['status' => 'danger', 'message' => 'ارسال پیامک ناموفق بود. در صورت تکرار مشکل با پشتیبانی تماس بگیرید'], Helper::STATUS_ERROR);

        }
    }
}
