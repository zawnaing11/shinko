<?php

namespace App\Http\Controllers\User\Api;

use App\Http\Controllers\Controller;
use App\Models\Store;

class StoreController extends Controller
{
    public function show(Store $store)
    {
        return $store;
    }
}
