<?php

namespace App\Http\Controllers;

use App\Models\Watchlist;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WatchlistController extends Controller
{

    public function watchlistPage()
    {
        $watchlist = Watchlist::where('user_id', auth()->id())->get();

        return Inertia::render('Watchlist/List', [
            'watchlist' => $watchlist,
        ]);
    }
    public function index()
    {
        return Watchlist::where('user_id', auth()->id())->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'symbol' => 'required|string',
            'type' => 'required|in:stock,etf',
        ]);
        $exists = Watchlist::where('user_id', auth()->id())
            ->where('symbol', strtoupper($data['symbol']))
            ->where('type', $data['type'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Already in your watchlist'], 200);
        }

        Watchlist::firstOrCreate([
            'user_id' => auth()->id(),
            'symbol' => strtoupper($data['symbol']),
            'type' => $data['type'],
        ]);

        return response()->json(['message' => 'Added to watchlist']);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'symbol' => 'required|string',
            'type' => 'required|in:stock,etf',
        ]);

        Watchlist::where('user_id', auth()->id())
            ->where('symbol', strtoupper($request->symbol))
            ->where('type', $request->type)
            ->delete();

        return response()->json(['message' => 'Removed from watchlist']);
    }
}
