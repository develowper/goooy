<?php

namespace App\Http\Requests;

use App\Http\Helpers\Variable;
use App\Models\Admin;
use App\Models\Agency;
use App\Models\Sample;
use App\Models\User;
use App\Models\Variation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuaranteeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->myAgency = Agency::find($this->user()->agency_id);

        if (!$this->myAgency)
            abort(403, __("access_denied"));
        if ($this->myAgency->status != 'active')
            abort(403, __("your_agency_inactive"));
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $tmp = [];

        if (!$this->cmnd) {
            $user = $this->user();
            $operator = $this->operator_id ? Admin::find($this->operator_id) : null;
            $this->merge([
                'operator' => $this->myAgency->level > '1' ? $user : $operator,
                'operator_id' => $this->myAgency->level > '1' ? $user->id : $this->operator_id,
                'agency_id' => $this->myAgency->level > '1' ? $user->agency_id : ($operator->agency_id ?? null),
                'agency' => $this->myAgency->level > '1' ? $this->myAgency : Agency::find($user->agency_id),
            ]);
            $availableAgencies = $user->allowedAgencies($this->myAgency)->pluck('id');
            $tmp = array_merge($tmp, [
                'operator_id' => ['sometimes'],
                'agency_id' => ['required', Rule::in($availableAgencies)],
                'phone' => ['required', 'numeric', 'digits:11', 'regex:/^09[0-9]+$/'],
                'phone_verify' => ['required', Rule::exists('sms_verify', 'code')->where('phone', $this->phone)],
                'guarantee_code' => ['required', function ($attribute, $value, $fail) {

//                    dd(Variation::makeBarcode(1, '1403/06/2', 6));
//                    1140306020678
                    if (!Sample::validateBarcode($value))
                        return $fail(sprintf(__("validator.invalid"), __('guarantee_code')));

                }]

            ]);
        }
        return $tmp;
    }

    public function messages()
    {

        return [
            'guarantee_code.required' => sprintf(__("validator.required"), __('guarantee_code')),

            'agency_id.required' => sprintf(__("validator.required"), __('agency')),
            'agency_id.in' => sprintf(__("validator.invalid"), __('agency')),

            'operator_id.required' => sprintf(__("validator.required"), __('operator')),
            'operator_id.in' => sprintf(__("validator.invalid"), __('operator')),

            'phone.required' => sprintf(__("validator.required"), __('phone')),
            'phone.unique' => sprintf(__("validator.unique"), __('phone')),
            'phone.regex' => sprintf(__("validator.invalid"), __('phone')),
            'phone.numeric' => sprintf(__("validator.numeric"), __('phone')),
            'phone.digits' => sprintf(__("validator.digits"), __('phone'), 11),

            'phone_verify.required' => sprintf(__("validator.required"), __('phone_verify')),
            'phone_verify.exists' => sprintf(__("validator.invalid"), __('phone_verify')),
        ];
    }
}
