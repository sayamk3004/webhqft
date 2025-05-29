<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ETFController;
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [StockController::class, 'dashboard'])->name('dashboard');

Route::prefix('stocks')->group(function () {
    Route::get('/', [StockController::class, 'index'])->name('stocks.index');
    Route::get('/{symbol}', [StockController::class, 'show'])->name('stocks.show');
    Route::get('/{symbol}/news', [DetailsController::class, 'news'])->name('stocks.news');
Route::get('/{symbol}/peers', [DetailsController::class, 'peers'])->name('stocks.peers');
Route::get('/{symbol}/analyst-recommendation', [DetailsController::class, 'analystRecommendation'])->name('stocks.analyst');

    
});
Route::get('/popular-stocks', function () {
    return response()->json(app(\App\Http\Controllers\StockController::class)->getPopularStocks());
});

Route::get('/market-news', function () {
    return response()->json(app(\App\Http\Controllers\StockController::class)->getMarketNews());
});

Route::get('/market-status', function () {
    return response()->json(app(\App\Http\Controllers\StockController::class)->getMarketStatus());
});

Route::prefix('etfs')->group(function () {
    Route::get('/', [ETFController::class, 'index'])->name('etfs.index');
    Route::get('/{symbol}', [ETFController::class, 'show'])->name('etfs.show');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
