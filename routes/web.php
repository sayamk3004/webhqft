<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ETFController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\WatchlistController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [StockController::class, 'dashboard'])->name('dashboard');

    Route::prefix('stocks')->group(function () {
        Route::get('/', [StockController::class, 'index'])->name('stocks.index');
        Route::get('/{symbol}', [StockController::class, 'show'])->name('stocks.show');
        Route::post('/compare-data', [StockController::class, 'compareData'])->name('stocks.compare');
        Route::get('/compare/{symbols}', [StockController::class, 'showCompare'])->name('compare.show');
        Route::get('/market/movers', [StockController::class, 'movers'])->name('stocks.movers');
    });
    Route::get('/indices/{symbol}', [StockController::class, 'showIndex'])->name('indices.show');

    Route::get('/popular-stocks', function () {
        return response()->json(app(\App\Http\Controllers\StockController::class)->getPopularStocks());
    })->name('popular_stocks');
    Route::get('/market-graph', function () {
        return response()->json(app(\App\Http\Controllers\StockController::class)->getMarketGraph());
    })->name('market_graph');
    Route::get('/market-news', function () {
        return response()->json(app(\App\Http\Controllers\StockController::class)->getMarketNews());
    })->name('market_news');
    Route::get('/top-movers', function () {
        return response()->json(app(\App\Http\Controllers\StockController::class)->getTopMovers());
    })->name('top_movers');
    Route::get('/sector-performance', function () {
        return response()->json(app(\App\Http\Controllers\StockController::class)->getSectorPerformance());
    })->name('sectors');
    Route::get('/indices', function () {
        return response()->json(app(\App\Http\Controllers\StockController::class)->getIndices());
    })->name('indices');
    Route::get('/economic-calendar', function () {
        return response()->json(app(\App\Http\Controllers\StockController::class)->getEconomicCalendar());
    })->name('economic_calendar');
    Route::get('/aianalyst-summary', function () {
        return response()->json(app(\App\Http\Controllers\StockController::class)->getAIAnalystSummary());
    })->name('aianalyst-summary');

    Route::get('/market-status', function () {
        return response()->json(app(\App\Http\Controllers\StockController::class)->getMarketStatus());
    });

    Route::prefix('etfs')->group(function () {
        Route::get('/', [ETFController::class, 'index'])->name('etfs.index');
        Route::get('/{symbol}', [ETFController::class, 'show'])->name('etfs.show');
        Route::get('/{symbol}/tab-data/{tab}', [ETFController::class, 'loadTabData'])->name('etfs.loadTabData');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/my-watchlist', [WatchlistController::class, 'watchlistPage'])->name('watchlist.page');

    Route::get('/watchlist', [WatchlistController::class, 'index']);
    Route::post('/watchlist', [WatchlistController::class, 'store']);
    Route::delete('/watchlist', [WatchlistController::class, 'destroy']);
});

require __DIR__ . '/auth.php';
