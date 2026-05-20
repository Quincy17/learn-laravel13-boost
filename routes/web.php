<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AIController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/ai', [AIController::class, 'index'])->name('ai.index');
Route::post('/ai/generate', [AIController::class, 'generate'])->name('ai.generate');
