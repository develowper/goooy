<?php

namespace App\Http\Helpers;


use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;


class SMSHelper
{
    private $senderType, $apiKey, $client, $server_number, $register_pattern, $forget_password_pattern;

    public function __construct()
    {
        $this->senderType = Variable::SMS_SENDER;
        $this->register_pattern = ''; //varta
        $this->forget_password_pattern = '';
        $this->server_number = '';
        $this->apiKey = env('SMS_API');

        $this->client = new \IPPanel\Client($this->apiKey);

    }


    public static function deleteCode(mixed $phone)
    {
        DB::table('sms_verify')->where('phone', $phone)->delete();
    }

    public static function addCode($phone, $code)
    {
        self::deleteCode($phone);
        DB::table('sms_verify')->insert(
            ['code' => $code, 'phone' => $phone]
        );
    }


    public static function checkRepeatedSMS($phone, $min)
    {
        return DB::table('sms_verify')->where('phone', $phone)->where('created_at', '>', Carbon::now()->subMinutes($min))->exists();
    }

    const URL = "https://ippanel.com/services.jspd";

    public static function createCode($phone)
    {
        $code = Util::generateRandomNumber(5);
        return DB::table('sms_verify')->insert(
            ['code' => $code, 'phone' => $phone]
        );
    }

    public function send($to, $msg, $cmnd = 'register')
    {
//        if ($to == "09018945844" || $to == "9018945844") return;

        if ($this->senderType == 'sms.ir') {
            return $this->smsIR($to, $msg, $cmnd);
        }

        $name = "ورتا شاپ";
        $pattern = $this->register_pattern;
        $code = null;

        $patternVariables = [
            "name" => "string",
            "code" => "integer",
        ];
        if ($cmnd == 'forget') {
            $pattern = $this->forget_password_pattern;
            $send = "رمز یکبار مصرف: " . "%code%" . PHP_EOL . "%name%";
        } else {

            $send = "خوش آمدید: کد تایید شما " . "%code%" . PHP_EOL . "%name%";
        }
//
//        try {
//            $code = $this->client->createPattern("$send", "otp $msg send to $to",
//                $patternVariables, '%', False);
//        } catch (\IPPanel\Errors\Error $e) {
//            echo $e->getMessage();
//        } catch (\IPPanel\Errors\HttpException $e) {
//            echo $e->getMessage();
//        }
//        echo $code;
        $patternValues = [
//            "name" => $name,
            "code" => "$msg",
        ];
//        if ($code) {
        $messageId = null;
        try {
            $messageId = $this->client->sendPattern(
                "$pattern",    // pattern code
                $this->server_number,      // originator
                "$to",  // recipient
                $patternValues  // pattern values
            );
        } catch (\IPPanel\Errors\Error $e) {
            Telegram::sendMessage(Telegram::LOGS[0], $e->getMessage());
        } catch (\IPPanel\Errors\HttpException $e) {
            Telegram::sendMessage(Telegram::LOGS[0], $e->getMessage());


        }
//        Telegram::sendMessage(Helper::$logs[0], $messageId);

//        echo $messageId;
//        }
        return (bool)$messageId;
    }


    public function getCredit()
    {
        if ($this->senderType == 'sms.ir')
            return (new SmsIR_UltraFastSend())->getCredit();
        return $this->client->getCredit();
    }

    /**
     * @param $messageId string returns from send
     * @return object
     */
    public function getMessageInfo($messageId)
    {
        try {
            $message = $this->client->getMessage($messageId);
        } catch (\IPPanel\Errors\Error $e) {

        } catch (\IPPanel\Errors\HttpException $e) {
        }
        // get message status
        // get message cost
        // get message payback
        return (object)['state' => $message->state, 'cost' => $message->cost, 'returnCost' => $message->returnCost];

    }

    public function getMessageStatus($messageId)
    {
        $statuses = [];
        $paginationInfo = (object)['total' => 0];
        try {
            list($statuses, $paginationInfo) = $this->client->fetchStatuses($messageId, 0, 10);
        } catch (\IPPanel\Errors\Error $e) {
        } catch (\IPPanel\Errors\HttpException $e) {
        }
        return ['total' => $paginationInfo->total, 'statuses' => $statuses];


//        foreach ($statuses as status)
//          $status->recipient, $status->status
//

    }

    public function smsIR($number, $code, $type = 'register')
    {
        switch ($type) {
            case 'forget':
            case 'register':
            case 'verification':
            case 'guarantee':
                $templateId = 209619;
                $params = [[
                    "Name" => "VerificationCode",
                    "Value" => "$code"
                ]];
                break;
            case 'guarantee_started':
                $templateId = 659877;
                $code = explode('$', $code);
                $params = [
                    [
                        "Name" => "Serial",
                        "Value" => $code[0]
                    ], [
                        "Name" => "Expire",
                        "Value" => $code[1]
                    ],
                ];
                break;
            case 'item_status':
                $templateId = 606885;
                $code = explode('$', $code);
                $params = [
                    [
                        "Name" => "item",
                        "Value" => $code[0]
                    ], [
                        "Name" => "status",
                        "Value" => $code[1]
                    ],
                ];
                break;
        }

        try {
            date_default_timezone_set("Asia/Tehran");


            // message data
            $data = array(
                "Parameters" => $params,
                "Mobile" => "$number",
                "TemplateId" => $templateId
            );
            $res = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'text/plain',
                'x-api-key' => env('SMS_API')])
                ->post('https://api.sms.ir/v1/send/verify', $data);
//            $SmsIR_UltraFastSend = new SmsIR_UltraFastSend(env('SMS_API'), $SecretKey);
//            $SmsIR_UltraFastSend->UltraFastSend($data);
            if ($res && $res->object()->status === 1)
                return true;
            return false;
        } catch (Exception $e) {
//            echo 'Error SendMessage : ' . $e->getMessage();
            Telegram::log(null, 'error', $e->getMessage());
            return false;
        }
    }
}
