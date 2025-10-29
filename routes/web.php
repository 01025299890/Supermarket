<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::get('/search-by-qr_index', [ProductController::class, 'searchByQR_index']);

require __DIR__.'/auth.php';
