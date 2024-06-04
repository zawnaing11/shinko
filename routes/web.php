<?php

use Illuminate\Support\Facades\Route;

Route::get('/health_check', function () {
    return response('OK', 200);
});
