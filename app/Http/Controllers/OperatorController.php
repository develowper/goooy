<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Agency;
use Illuminate\Http\Request;

class OperatorController extends Controller
{
    public
    function searchPanel(Request $request)
    {
        $admin = $request->user();

        $search = $request->search;
        $page = $request->page ?: 1;
        $orderBy = $request->order_by && $request->order_by != 'agency' ? $request->order_by : 'agency_id';

        $dir = $request->dir ?: 'DESC';
        $paginate = $request->paginate ?: 24;
        $status = $request->status;
        $query = Admin::query()->select('*')->where('role', 'operator');

        $myAgency = Agency::find($admin->agency_id);

        $agencies = $admin->allowedAgencies($myAgency)->get();
        $agencyIds = $agencies->pluck('id');

        if ($search)
            $query = $query->where(function ($query) use ($search) {
                $query->orWhere('fullname', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%");
            });
        if ($status)
            $query = $query->where('status', $status);

        $query->whereIntegerInRaw('agency_id', $agencyIds);


        return tap($query->orderBy($orderBy, $dir)->paginate($paginate, ['*'], 'page', $page), function ($paginated) use ($agencies) {
            return $paginated->getCollection()->transform(
                function ($item) use ($agencies) {
                    $item->setRelation('agency', $agencies->where('id', $item->agency_id)->first());

                    return $item;
                }

            );
        });
    }
}
