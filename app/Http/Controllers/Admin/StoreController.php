<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $stores = Store::with(['companyAdminUserStore.user.company'])
            ->orderBy('update_date', 'DESC');

        if ($request->filled('company_id')) {
            $stores->whereHas('companyAdminUserStore.user.company', function($q) use ($request) {
                $q->where('id', $request->company_id);
            });
        }
        if ($request->filled('store_id')) {
            $stores->where('id', $request->store_id);
        }

        $stores = $stores->paginate(config('const.default_paginate_number'));

        return view('admin.stores.index', [
            'stores' => $stores,
            'stores_list' => Store::get(),
            'companies_list' => Company::get(),
        ]);
    }

}
