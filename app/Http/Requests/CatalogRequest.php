<?php

namespace App\Http\Requests;

use App\Models\Agency;
use App\Models\City;
use Illuminate\Validation\Rules\File;
use App\Http\Helpers\Variable;
use App\Models\Business;
use App\Models\Category;
use App\Models\County;
use App\Models\Sport;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use stdClass;

class CatalogRequest extends FormRequest
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
        $editMode = (bool)$this->id;
        $tmp = [];

        if (!$this->cmnd) {
            $user = $this->user();

            $tmp = array_merge($tmp, [

                'name_fa' => ['required', 'string', 'max:200'],
                'name_en' => ['required', 'string', 'max:200'],
                'pn' => ['required', 'string', 'max:30'],
                'price' => ['required', 'numeric'   /*, Rule::unique('drivers', 'national_code')->ignore($this->id)*/],
                'image_url' => ['nullable', 'string', 'max:250'],
                'image_indicator' => ['nullable', 'numeric'],
                'in_shop' => ['required', 'numeric'],
                'in_repo' => ['required', 'numeric'],
                'img' => ['nullable', 'base64_image_size:' . Variable::DRIVER_IMAGE_LIMIT_MB * 1024, 'base64_image_mime:' . implode(",", Variable::DRIVER_ALLOWED_MIMES)],


            ]);
        }
        if ($this->uploading)
            $tmp = array_merge($tmp, [
                'img' => $this->img ? ['nullable', 'base64_image_size:' . Variable::DRIVER_IMAGE_LIMIT_MB * 1024, 'base64_image_mime:' . implode(",", Variable::DRIVER_ALLOWED_MIMES)] : [],

            ]);
        if ($this->cmnd)
            $tmp = array_merge($tmp, [
            ]);
        return $tmp;
    }

    public function messages()
    {

        return [


            'name_fa.required' => sprintf(__("validator.required"), __('name_fa')),
            'name_fa.unique' => sprintf(__("validator.unique"), __('name_fa')),
            'name_fa.max' => sprintf(__("validator.max_len"), __('name_fa'), 200, mb_strlen($this->name_fa)),
            'name_fa.string' => sprintf(__("validator.string"), __('name_fa')),

            'name_en.required' => sprintf(__("validator.required"), __('name_en')),
            'name_en.unique' => sprintf(__("validator.unique"), __('name_en')),
            'name_en.max' => sprintf(__("validator.max_len"), __('name_en'), 200, mb_strlen($this->name_en)),
            'name_en.string' => sprintf(__("validator.string"), __('name_en')),

            'pn.required' => sprintf(__("validator.required"), __('pn')),
            'pn.unique' => sprintf(__("validator.unique"), __('pn')),
            'pn.max' => sprintf(__("validator.max_len"), __('pn'), 30, mb_strlen($this->pn)),
            'pn.string' => sprintf(__("validator.string"), __('pn')),

            'image_url.required' => sprintf(__("validator.required"), __('image_url')),
            'image_url.unique' => sprintf(__("validator.unique"), __('image_url')),
            'image_url.max' => sprintf(__("validator.max_len"), __('image_url'), 250, mb_strlen($this->pn)),
            'image_url.string' => sprintf(__("validator.string"), __('image_url')),

            'price.required' => sprintf(__("validator.required"), __('price')),
            'price.integer' => sprintf(__("validator.numeric"), __('price')),
            'price.min' => sprintf(__("validator.min"), __('price'), 0),

            'in_shop.required' => sprintf(__("validator.required"), __('shop_count')),
            'in_shop.integer' => sprintf(__("validator.numeric"), __('shop_count')),
            'in_shop.min' => sprintf(__("validator.min"), __('shop_count'), 0),

            'in_repo.required' => sprintf(__("validator.required"), __('repository_count')),
            'in_repo.integer' => sprintf(__("validator.numeric"), __('repository_count')),
            'in_repo.min' => sprintf(__("validator.min"), __('repository_count'), 0),

            'image_indicator.required' => sprintf(__("validator.required"), __('image_indicator')),
            'image_indicator.integer' => sprintf(__("validator.numeric"), __('image_indicator')),
            'image_indicator.min' => sprintf(__("validator.min"), __('image_indicator'), 0),

            'img.required' => sprintf(__("validator.required"), __('image')),
            'img.base64_image_size' => sprintf(__("validator.max_size"), __("image"), Variable::PRODUCT_IMAGE_LIMIT_MB),
            'img.base64_image_mime' => sprintf(__("validator.invalid_format"), __("image"), implode(",", Variable::PRODUCT_ALLOWED_MIMES)),


            'national_code.required' => sprintf(__("validator.required"), __('national_code')),
            'national_code.digits' => sprintf(__("validator.digits"), __('national_code'), 16),
            'national_code.unique' => sprintf(__("validator.unique"), __('national_code')),
            'national_code.numeric' => sprintf(__("validator.numeric"), __('national_code')),


        ];
    }
}
