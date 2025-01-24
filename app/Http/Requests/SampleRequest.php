<?php

namespace App\Http\Requests;

use App\Models\Agency;
use App\Models\City;
use App\Models\Product;
use App\Models\Repository;
use App\Models\Variation;
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

class SampleRequest extends FormRequest
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
        $editMode = (bool)$this->id;
        $tmp = [];
        $admin = $this->user();

        if (!$this->cmnd) {
            $allowedRepositories = Repository::whereIntegerInRaw('agency_id', $admin->allowedAgencies(Agency::find($admin->agency_id))->pluck('id'))->pluck('id');

            $tmp = array_merge($tmp, [
                'repo_ids' => ['required', 'integer', 'min:1', Rule::in($allowedRepositories)],
//                'repo_ids.*' => [Rule::in($allowedRepositories)],
                "product_id" => ['required', Rule::in(Product::pluck('id'))],
                "batch_count" => ['required', 'numeric', 'gte:0'],
                "produced_at" => ['required', 'string', 'regex:/\d{4}\/\d{1,2}\/\d{1,2}/'],
                "guarantee_months" => ['nullable', 'numeric', 'gte:0'],
                "price" => ['required', 'numeric', 'gte:0'],

            ]);
        }
        if ($this->uploading)
            $tmp = array_merge($tmp, [
                'img' => ['required', 'base64_image_size:' . Variable::PRODUCT_IMAGE_LIMIT_MB * 1024, 'base64_image_mime:' . implode(",", Variable::PRODUCT_ALLOWED_MIMES)],

            ]);
        if ($this->cmnd)
            $tmp = array_merge($tmp, [
            ]);
        return $tmp;
    }

    public function messages()
    {

        return [

            'produced_at.required' => sprintf(__("validator.required"), __('produced_at')),
            'produced_at.regex' => sprintf(__("validator.invalid"), __('produced_at')),

            "guarantee_months.required" => sprintf(__("validator.required"), __('guarantee_months')),
            "guarantee_months.numeric" => sprintf(__("validator.numeric"), __('guarantee_months')),
            "guarantee_months.gte" => sprintf(__("validator.gt"), __('guarantee_months'), 0),

            "batch_count.required" => sprintf(__("validator.required"), __('count')),
            "batch_count.numeric" => sprintf(__("validator.numeric"), __('count')),
            "batch_count.gte" => sprintf(__("validator.gt"), __('count'), 0),

            'repo_ids.required' => sprintf(__("validator.required"), __('repository')),
            'repo_ids.*.in' => sprintf(__("validator.invalid"), __('repository')),

            "product_id.required" => sprintf(__("validator.required"), __('product')),
            "product_id.in" => sprintf(__("validator.invalid"), __('product')),

            'name.required' => sprintf(__("validator.required"), __('name')),
            'name.unique' => sprintf(__("validator.unique"), __('name')),
            'name.max' => sprintf(__("validator.max_len"), __('name'), 200, mb_strlen($this->name)),

            'tags.max' => sprintf(__("validator.max_len"), __('tags'), 1024, mb_strlen($this->tags)),

            'img.required' => sprintf(__("validator.required"), __('image')),
            'img.base64_image_size' => sprintf(__("validator.max_size"), __("image"), Variable::PRODUCT_IMAGE_LIMIT_MB),
            'img.base64_image_mime' => sprintf(__("validator.invalid_format"), __("image"), implode(",", Variable::PRODUCT_ALLOWED_MIMES)),

            'category_id.required' => sprintf(__("validator.required"), __('category')),
            'category_id.in' => sprintf(__("validator.invalid"), __('category')),

            "price.required" => sprintf(__("validator.required"), __('price')),
            "price.numeric" => sprintf(__("validator.numeric"), __('price')),
            "price.gte" => sprintf(__("validator.gt"), __('price'), 0),

            "in_repo.required" => sprintf(__("validator.required"), __('repository_count')),
            "in_repo.numeric" => sprintf(__("validator.numeric"), __('repository_count')),
            "in_repo.gte" => sprintf(__("validator.gt"), __('repository_count'), 0),

            "in_shop.required" => sprintf(__("validator.required"), __('shop_count')),
            "in_shop.numeric" => sprintf(__("validator.numeric"), __('shop_count')),
            "in_shop.gte" => sprintf(__("validator.gt"), __('shop_count'), 0),

        ];
    }
}
