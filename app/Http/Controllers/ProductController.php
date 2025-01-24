<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Telegram;
use App\Http\Helpers\Util;
use App\Http\Helpers\Variable;
use App\Http\Requests\ProductRequest;
use App\Models\Admin;
use App\Models\Agency;
use App\Models\Product;
use App\Models\Variation;
use App\Models\Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use stdClass;

class ProductController extends Controller
{
    protected
    function getTree(Request $request)
    {
//        $admin = $request->user();

        $search = $request->search;
        $page = $request->page ?: 1;
        $orderBy = $request->order_by ?: 'id';
        $dir = $request->dir ?: 'DESC';
        $paginate = $request->paginate ?: 24;
        $status = $request->status;
        $repoId = $request->repo_id;

        $data = Variation::join('products', function ($join) use ($repoId, $search) {
            $join->on('variations.product_id', '=', 'products.id')
                ->where('variations.repo_id', $repoId)
                ->where(function ($query) use ($search) {
                    if ($search)
                        $query->where('variations.name', 'like', "%$search%");
                });

        })->select('variations.id', 'variations.product_id',
            'products.id as parent_id',
            'variations.name as name',
            'products.name as parent_name',
            'variations.pack_id as pack_id',
            'variations.grade as grade',
            'variations.price as price',
            'variations.auction_price as auction_price',
            'variations.auction_price as auction_price',
            'variations.weight as weight',
            'variations.in_auction as in_auction',
            'variations.in_shop as in_shop',
            'variations.in_repo as in_repo',
            'variations.product_id as product_id',

        )
            ->get();
        //add parents to items as row
        foreach ($data as $item) {
            if (!$data->where('parent_id', 0)->where('id', $item->parent_id)->first())
                $data->push(['id' => $item->parent_id, 'name' => $item->parent_name, 'has_child' => true, 'parent_id' => 0]);
        }
        $data->sortBy('variations.name');
//            ->groupBy('parent_id')->map(function ($el, $idx) {
//
//                return ['id' => $idx, 'name' => $el[0]->parent_name, 'childs' => $el];
//            })->values();//

        return $data;
    }

    public function edit(Request $request, $id)
    {

        $data = Product::find($id);

        $this->authorize('edit', [Admin::class, $data]);
        if ($data) {
            $all = Product::getImages($data->id);
            $data->images = collect($all)->filter(fn($e) => !str_contains($e, 'thumb'))->all();
            $data->thumb_img = collect($all)->filter(fn($e) => str_contains($e, 'thumb'))->first();
        }
        return Inertia::render('Panel/Admin/Product/Edit', [
            'statuses' => Variable::STATUSES,
            'data' => $data,

        ]);
    }

    public function create(ProductRequest $request)
    {
        if (!$request->uploading) { //send again for uploading images
            return back()->with(['resume' => true]);
        }
        $request->merge([
            'status' => 'active',
            'tags' => $request->tags,
        ]);
        $data = Product::create($request->all());

        if ($data) {
            Util::createImage($request->img, Variable::IMAGE_FOLDERS[Product::class], $data->id);

            $data->img = url("storage/products/$data->id.jpg");
            $res = ['flash_status' => 'success', 'flash_message' => __('created_successfully')];
            Telegram::log(null, 'product_created', $data);
        } else    $res = ['flash_status' => 'danger', 'flash_message' => __('response_error')];
        return to_route('admin.panel.product.index')->with($res);

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

        $query = Product::query()->select();

        if ($search)
            $query = $query->where('name', 'like', "%$search%");
        if ($status)
            $query = $query->where('status', $status);
        return $query->orderBy($orderBy, $dir)->paginate($paginate, ['*'], 'page', $page);


    }

    public function update(ProductRequest $request)
    {
        $response = ['message' => __('response_error')];
        $errorStatus = Variable::ERROR_STATUS;
        $successStatus = Variable::SUCCESS_STATUS;
        $id = $request->id;
        $cmnd = $request->cmnd;
        $data = Product::find($id);
        if (!starts_with($cmnd, 'bulk'))
            $this->authorize('edit', [Admin::class, $data]);

        if ($cmnd) {
            switch ($cmnd) {
                case 'inactive':
                    $data->status = 'inactive';
                    $data->save();
                    return response()->json(['message' => __('updated_successfully'), 'status' => $data->status,], $successStatus);

                case 'activate':
                    $data->status = 'active';
                    $data->save();
                    return response()->json(['message' => __('updated_successfully'), 'status' => $data->status,], $successStatus);

                case 'delete-img'   :
                    $type = Variable::IMAGE_FOLDERS[Product::class];
                    $path = Storage::path("public/$type/$id/" . basename($request->path));
//                    $allFiles = Storage::allFiles("public/$type/$id");
//                    if (count($allFiles) == 1)
//                        return response()->json(['errors' => [sprintf(__('validator.min_images'), 1)]], 422);
                    if (!File::exists($path))
                        return response()->json(['errors' => [__('file_not_exists')], 422]);
                    File::delete($path);
                    return response()->json(['message' => __('updated_successfully')], $successStatus);

                case  'upload-img' :

                    if (!$request->img) //  add extra image
                        return response()->json(['errors' => [__('file_not_exists')], 422]);

                    Util::createImage($request->img, Variable::IMAGE_FOLDERS[Product::class], $id);

                    $data = new stdClass;

                    $data->id = $request->id;
                    $data->name = $request->name;
                    $data->img = url("storage/products/$id.jpg") . "?rev=" . random_int(100, 999);
                    Telegram::log(null, 'image_updloaded', $data);

                    return response()->json(['message' => __('updated_successfully')], $successStatus);


                case  'upload-img' :
                    $limit = Variable::VARIATION_IMAGE_LIMIT;
                    $type = Variable::IMAGE_FOLDERS[Product::class];
                    $allFiles = Storage::allFiles("public/$type/$id");
                    if (!$request->path && count($allFiles) >= $limit + 1) //  add extra image
                        return response()->json(['errors' => [sprintf(__('validator.max_images'), $limit)], 422]);
                    if (!$request->img) //  add extra image
                        return response()->json(['errors' => [__('file_not_exists')], 422]);
                    $name = str_contains($request->name, '-') ? explode('-', $request->name)[1] : $request->name;
                    $path = Storage::path("public/$type/$id/$name.jpg");
                    if (File::exists($path)) File::delete($path);
                    Util::createImage($request->img, Variable::IMAGE_FOLDERS[Product::class], $name, $id, 500);
//                    if ($data) {
//                        $data->status = 'review';
//                        $data->save();
//                    }
                    $data->img = url("storage/products/$id/$name.jpg");
                    Telegram::log(null, 'image_updloaded', $data);
                    return response()->json(['message' => __('updated_successfully')], $successStatus);


            }
        } elseif ($data) {


            $request->merge([
//                'cities' => json_encode($request->cities ?? [])
                'tags' => $request->tags,

            ]);


            if ($data->update($request->all())) {

                $res = ['flash_status' => 'success', 'flash_message' => __('updated_successfully')];
//                dd($request->all());
                Telegram::log(null, 'product_edited', $data);
            } else    $res = ['flash_status' => 'danger', 'flash_message' => __('response_error')];
            return back()->with($res);
        }

        return response()->json($response, $errorStatus);
    }

    public
    function search(Request $request)
    {
        //disable ONLY_FULL_GROUP_BY
//        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
//        $user = auth()->user();

        $search = $request->search;
        $inShop = $request->in_shop;
        $parentIds = $request->parent_ids;
        $districtId = $request->district_id;
        $countyId = $request->county_id;
        $provinceId = $request->province_id;
        $page = $request->page ?: 1;
        $orderBy = $request->order_by ?? 'updated_at';
        $dir = $request->dir ?? 'DESC';
        $paginate = $request->paginate ?: 24;
        $grade = $request->grade;

        $query = Product::join('repositories', function ($join) use ($inShop, $parentIds, $countyId, $districtId, $provinceId) {
            $join->on('products.repo_id', '=', 'repositories.id')
                ->where('repositories.status', 'active')
                ->where('repositories.is_shop', true)
//                ->where('variations.agency_level', '3')
                ->where(function ($query) use ($inShop) {
                    if ($inShop)
                        $query->where('products.in_shop', '>', 0);
                })->where(function ($query) use ($parentIds) {
                    if ($parentIds && is_array($parentIds) && count($parentIds) > 0)
                        $query->whereIntegerInRaw('products.product_id', $parentIds);
                })
//                ->where(function ($query) use ($provinceId) {
//                    if ($provinceId === null)
//                        $query->where('repositories.id', 0);
//                    elseif ($provinceId)
//                        $query->where('repositories.province_id', $provinceId);
//                })->where(function ($query) use ($countyId, $districtId) {
//
//                    if ($countyId === null)
//                        $query->where('repositories.id', 0);
//                    elseif ($countyId && intval($districtId) === 0)
//                        $query->whereJsonContains('repositories.cities', intval($countyId));
//                })->where(function ($query) use ($districtId) {
//                    if ($districtId === null)
//                        $query->where('repositories.id', 0);
//                    elseif ($districtId)
//                        $query->whereJsonContains('repositories.cities', intval($districtId));
//                })
            ;

        })->select('products.id',
//            'products.product_id',
            'repositories.id as repo_id',
            'products.name as name',
            'repositories.name as repo_name',
//            'products.pack_id as pack_id',
//            'products.grade as grade',
            'products.price as price',
            'products.auction_price as auction_price',
            'products.auction_price as auction_price',
//            'products.weight as weight',
//            'products.unit as unit',
            'products.in_auction as in_auction',
            'products.in_shop as in_shop',
//            'products.product_id as parent_id',
            'products.updated_at as updated_at',
            'repositories.province_id as province_id',

        )
            ->orderBy("products.$orderBy", $dir)//
            //            ->orderByRaw("IF(articles.charge >= articles.view_fee, articles.view_fee, articles.id) DESC")
        ;

        if ($search)
            $query->where('products.name', 'like', "%$search%");
        if ($grade)
            $query = $query->where('products.grade', $grade);
        $res = $query->paginate($paginate, ['*'], 'page', $page)//            ->getCollection()->groupBy('parent_id')
        ;
        return $res;
    }

}
