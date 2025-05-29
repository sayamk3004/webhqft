<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ETFController extends Controller
{
    protected $baseUrl;
    protected $apiKey;
    protected $cacheDuration;
    protected $popularETFs;

    public function __construct()
    {
        $this->baseUrl = config('services.finnhub.base_url');
        $this->apiKey = config('services.finnhub.key');
        $this->cacheDuration = now()->addMinutes(30); // Cache for 30 minutes
        $this->popularETFs = ['SPY', 'IVV', 'VTI', 'VOO', 'QQQ', 'IEFA', 'VEA', 'VUG', 'VTV', 'IEMG'];
    }

    /**
     * Display ETF listings with search functionality
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 20);
        $page = $request->input('page', 1);

        if ($search) {
            $etfs = $this->searchETFs($search);
        } else {
            $etfs = Cache::remember('popular_etfs_list', $this->cacheDuration, function () {
                return $this->getPopularETFsData();
            });
        }

        // Paginate results
        $paginatedETFs = array_slice($etfs, ($page - 1) * $perPage, $perPage);

        return inertia('ETFs/ETFList', [
            'etfs' => $paginatedETFs,
            'search' => $search,
            'lastUpdated' => Cache::get('etfs_last_updated'),
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => count($etfs),
                'last_page' => ceil(count($etfs) / $perPage),
            ],
        ]);
    }

    /**
     * Display single ETF details
     */
    public function show($symbol)
    {
        $cacheKey = "etf_{$symbol}_data";
        
        $data = Cache::remember($cacheKey, $this->cacheDuration, function () use ($symbol) {
            return [
                'profile' => $this->getETFProfile($symbol),
                'quote' => $this->getETFQuote($symbol),
                'holdings' => $this->getETFHoldings($symbol),
                'chartData' => $this->getChartData($symbol),
                'lastUpdated' => now()->toDateTimeString()
            ];
        });

        return inertia('ETFs/Show', array_merge(['symbol' => $symbol], $data));
    }

    /**
     * API endpoint for all popular ETFs
     */
    public function apiAllETFs()
    {
        $data = Cache::remember('api_all_etfs', $this->cacheDuration, function () {
            return [
                'etfs' => $this->getPopularETFsData(),
                'lastUpdated' => now()->toDateTimeString()
            ];
        });

        return response()->json($data);
    }

    /**
     * API endpoint for single ETF
     */
    public function apiSingleETF($symbol)
    {
        $data = Cache::remember("api_etf_{$symbol}", $this->cacheDuration, function () use ($symbol) {
            return [
                'etf' => $this->getCompleteETFData($symbol),
                'lastUpdated' => now()->toDateTimeString()
            ];
        });

        return response()->json($data);
    }

    /**
     * Force refresh cache (can be called manually or via scheduler)
     */
    public function refreshCache()
    {
        // Refresh popular ETFs data
        $etfsData = $this->getPopularETFsData();
        Cache::put('popular_etfs_list', $etfsData, $this->cacheDuration);
        
        // Update last updated timestamp
        Cache::put('etfs_last_updated', now()->toDateTimeString(), $this->cacheDuration);

        return response()->json(['success' => true, 'message' => 'ETF cache refreshed']);
    }

    /**
     * Search ETFs by symbol or name
     */
    protected function searchETFs($query)
    {
        $cacheKey = 'etf_search_' . md5($query);
        
        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($query) {
            $response = Http::get("{$this->baseUrl}search?q={$query}&token={$this->apiKey}");
            $results = $response->json()['result'] ?? [];
            
            return array_filter($results, function($item) {
                return $item['type'] === 'ETF';
            });
        });
    }

    /**
     * Get data for popular ETFs
     */
    protected function getPopularETFsData()
    {
        $etfs = [];
        
        foreach ($this->popularETFs as $symbol) {
            $etfData = $this->getBasicETFData($symbol);
            if ($etfData) {
                $etfs[] = $etfData;
            }
        }
        
        return $etfs;
    }

    /**
     * Get basic ETF data (profile + quote)
     */
    protected function getBasicETFData($symbol)
    {
        $profile = $this->getETFProfile($symbol);
        $quote = $this->getETFQuote($symbol);
        
        if ($profile && $quote) {
            return [
                'symbol' => $symbol,
                'name' => $profile['name'] ?? $symbol,
                'type' => 'ETF',
                'price' => $quote['c'] ?? null,
                'change' => $quote['d'] ?? null,
                'percentChange' => $quote['dp'] ?? null,
                'assetsUnderManagement' => $profile['assetsUnderManagement'] ?? null,
                'inceptionDate' => $profile['inceptionDate'] ?? null,
            ];
        }
        
        return null;
    }

    /**
     * Get complete data for a single ETF
     */
    protected function getCompleteETFData($symbol)
    {
        $basicData = $this->getBasicETFData($symbol);
        
        if (!$basicData) {
            return null;
        }
        
        return array_merge($basicData, [
            'holdings' => $this->getETFHoldings($symbol),
            'chartData' => $this->getChartData($symbol),
            'profileDetails' => $this->getETFProfileDetails($symbol),
        ]);
    }

    /**
     * API methods below with individual caching
     */
    protected function getETFProfile($symbol)
    {
        return Cache::remember("etf_profile_{$symbol}", $this->cacheDuration, function () use ($symbol) {
            $response = Http::get("{$this->baseUrl}etf/profile?symbol={$symbol}&token={$this->apiKey}");
            return $response->successful() ? $response->json() : null;
        });
    }

    protected function getETFProfileDetails($symbol)
    {
        return Cache::remember("etf_profile_details_{$symbol}", $this->cacheDuration, function () use ($symbol) {
            $response = Http::get("{$this->baseUrl}etf/profile?symbol={$symbol}&token={$this->apiKey}");
            return $response->successful() ? $response->json() : null;
        });
    }

    protected function getETFQuote($symbol)
    {
        return Cache::remember("etf_quote_{$symbol}", $this->cacheDuration, function () use ($symbol) {
            $response = Http::get("{$this->baseUrl}quote?symbol={$symbol}&token={$this->apiKey}");
            return $response->successful() ? $response->json() : null;
        });
    }

    protected function getETFHoldings($symbol)
    {
        return Cache::remember("etf_holdings_{$symbol}", $this->cacheDuration, function () use ($symbol) {
            $response = Http::get("{$this->baseUrl}etf/holdings?symbol={$symbol}&token={$this->apiKey}");
            return $response->successful() ? $response->json() : [];
        });
    }

    protected function getChartData($symbol, $resolution = 'D', $from = null, $to = null)
    {
        $from = $from ?: now()->subYear()->timestamp;
        $to = $to ?: now()->timestamp;
        $cacheKey = "etf_chart_{$symbol}_{$resolution}_{$from}_{$to}";

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($symbol, $resolution, $from, $to) {
            $response = Http::get("{$this->baseUrl}stock/candle?symbol={$symbol}&resolution={$resolution}&from={$from}&to={$to}&token={$this->apiKey}");
            return $response->successful() ? $response->json() : [];
        });
    }
}