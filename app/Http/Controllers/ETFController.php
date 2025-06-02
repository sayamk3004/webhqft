<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ETFController extends Controller
{
    protected $baseUrl;
    protected $fmpBaseUrl;
    protected $auxBaseUrl;
    protected $apiKey;
    protected $fmpApiKey;
    protected $auxApiKey;
    protected $cacheDuration;
    protected $defaultSymbols;

    public function __construct()
    {
        $this->baseUrl = config('services.finnhub.base_url');
        $this->fmpBaseUrl = config('services.fmpApi.base_url');
        $this->auxBaseUrl = config('services.marketaux.base_url');

        $this->apiKey = config('services.finnhub.key');
        $this->fmpApiKey = config('services.fmpApi.key');
        $this->auxApiKey = config('services.marketaux.key');

        $this->cacheDuration = now()->addMinutes(1500); // 30 minutes cache
        $this->defaultSymbols = ['SPY', 'IVV', 'VOO', 'QQQ', 'DIA', 'IWM', 'EFA', 'EEM', 'GLD', 'TLT'];
    }

    // /**
    //  * Dashboard with popular ETFs and news
    //  */
    // public function dashboard()
    // {
    //     $marketNews = $this->getMarketNews();
    //     $popularETFs = $this->getPopularETFs();

    //     return inertia('Dashboard', [
    //         'marketNews' => $marketNews,
    //         'popularETFs' => $popularETFs,
    //         // Add other dashboard data as needed
    //     ]);
    // }

    /**
     * Fetch popular ETFs from FMP etf/actives endpoint or fallback to default symbols
     */
    protected function getPopularETFs()
    {
        return Cache::remember('popular_etfs', $this->cacheDuration, function () {
            $response = Http::get("{$this->fmpBaseUrl}etf/actives?apikey={$this->fmpApiKey}");

            $activeETFs = $response->successful() ? $response->json()['mostActiveETF'] ?? [] : [];

            if (empty($activeETFs)) {
                // Fallback to default symbols list
                $activeETFs = array_map(fn($sym) => ['ticker' => $sym], $this->defaultSymbols);
            }

            $etfs = [];

            foreach ($activeETFs as $etf) {
                $symbol = $etf['ticker'] ?? null;
                if ($symbol) {
                    $etfData = $this->getBasicETFData($symbol);
                    if ($etfData) {
                        $etfs[] = $etfData;
                    }
                }
            }

            return $etfs;
        });
    }

    /**
     * List ETFs with search and pagination
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 20);
        $page = $request->input('page', 1);

        $allETFs = $this->getAllETFData();

        if ($search) {
            $allETFs = array_filter($allETFs, function ($item) use ($search) {
                return stripos($item['symbol'], $search) !== false
                    || stripos($item['description'], $search) !== false;
            });
        }

        $filteredETFs = array_values($allETFs);
        $paginatedETFs = array_slice($filteredETFs, ($page - 1) * $perPage, $perPage);

        return inertia('ETF/Index', [
            'etfs' => $paginatedETFs,
            'search' => $search,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => count($filteredETFs),
                'last_page' => ceil(count($filteredETFs) / $perPage),
            ],
        ]);
    }

    /**
     * Show single ETF detail view
     */
    public function show($symbol)
    {
        $cacheKey = "etf_{$symbol}_data";

        $data = Cache::remember($cacheKey, $this->cacheDuration, function () use ($symbol) {
            return [
                'profile' => $this->getETFProfile($symbol),
                'quote' => $this->getETFQuote($symbol),
                'financials' => $this->getFinancials($symbol),
                'earnings' => $this->getEarnings($symbol),
                'chartData' => $this->getChartData($symbol),
                'news' => $this->getNews($symbol),
                'peers' => $this->getPeers($symbol),
                'recommendations' => $this->getRecommendations($symbol),
                'lastUpdated' => now()->toDateTimeString(),
            ];
        });

        return inertia('ETF/Show', array_merge(['symbol' => $symbol], $data));
    }


    protected function getAllETFData()
    {

        // return Cache::remember('all_etfs_fmp_data', $this->cacheDuration, function () {
        //     $etfs = [];
        //     foreach ($this->defaultSymbols as $symbol) {
        //         $etfData = $this->getBasicETFData($symbol);
        //         if ($etfData) {
        //             $etfs[] = $etfData;
        //         }
        //     }
        //     return $etfs;
        // });
        $etfs = [];
        foreach ($this->defaultSymbols as $symbol) {
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
                'description' => $profile['longName'] ?? $profile['shortName'] ?? $symbol,
                'type' => 'ETF',
                'price' => $quote['price'] ?? null,
                'change' => $quote['change'] ?? null,
                'percentChange' => $quote['changesPercentage'] ?? null,
                'currency' => $profile['currency'] ?? 'USD',
                'exchange' => $profile['exchangeName'] ?? 'Unknown',
                'sector' => $profile['sector'] ?? null,
            ];
        }

        return null;
    }

    protected function getETFProfile($symbol)
    {


        return Cache::remember("etf_profile_fmp_{$symbol}", $this->cacheDuration, function () use ($symbol) {
            $response = Http::get("{$this->fmpBaseUrl}profile/{$symbol}?apikey={$this->fmpApiKey}");
            if ($response->successful()) {
                return $response->json()[0] ?? null;
            }
            return null;
        });
    }
    protected function getETFQuote($symbol)
    {
        return Cache::remember("etf_quote_fmp_{$symbol}", $this->cacheDuration, function () use ($symbol) {
            $response = Http::get("{$this->fmpBaseUrl}quote/{$symbol}?apikey={$this->fmpApiKey}");

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();

            // FMP returns an array of quotes even if one symbol is requested
            return is_array($data) && count($data) > 0 ? $data[0] : null;
        });
    }


    protected function getFinancials($symbol)
    {
        return Cache::remember("etf_financials_{$symbol}", $this->cacheDuration, function () use ($symbol) {
            $response = Http::get("{$this->fmpBaseUrl}financials/income-statement/{$symbol}?apikey={$this->fmpApiKey}&limit=1");
            $json = $response->json();
            return $response->successful() && isset($json['financials']) ? $json['financials'] : [];
        });
    }

    protected function getEarnings($symbol)
    {
        return Cache::remember("etf_earnings_{$symbol}", $this->cacheDuration, function () use ($symbol) {
            $response = Http::get("{$this->fmpBaseUrl}earning/{$symbol}?apikey={$this->fmpApiKey}&limit=5");
            return $response->successful() ? $response->json() : [];
        });
    }

    protected function getChartData($symbol)
    {
        $cacheKey = "yahoo_etf_chart_{$symbol}_1y";

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($symbol) {
            $response = Http::get("https://query1.finance.yahoo.com/v8/finance/chart/{$symbol}?interval=1d&range=1y");
            if (!$response->successful()) {
                return [];
            }

            $data = $response->json()['chart']['result'][0] ?? null;
            if (!$data) {
                return [];
            }

            $timestamps = $data['timestamp'];
            $prices = $data['indicators']['quote'][0];

            return collect($timestamps)->map(function ($time, $i) use ($prices) {
                return [
                    'timestamp' => Carbon::createFromTimestamp($time)->toDateString(),
                    'open' => $prices['open'][$i] ?? null,
                    'high' => $prices['high'][$i] ?? null,
                    'low' => $prices['low'][$i] ?? null,
                    'close' => $prices['close'][$i] ?? null,
                    'volume' => $prices['volume'][$i] ?? null,
                ];
            })->filter(fn($item) => $item['close'] !== null)->values();
        });
    }

    protected function getNews($symbol)
    {
        return Cache::remember("etf_news_{$symbol}", $this->cacheDuration, function () use ($symbol) {
            $response = Http::get("{$this->auxBaseUrl}news/all", [
                'limit' => 10,
                'symbols' => $symbol,
                'language' => 'en',
                'api_token' => $this->auxApiKey,
            ]);
            return $response->json()['data'] ?? [];
        });
    }

    protected function getPeers($symbol)
    {
        return Cache::remember("etf_peers_{$symbol}", $this->cacheDuration, function () use ($symbol) {
            $url = "https://query2.finance.yahoo.com/v6/finance/recommendationsbysymbol/{$symbol}";
            $response = Http::get($url);
            if ($response->successful()) {
                $json = $response->json();
                return $json['finance']['result'][0]['recommendedSymbols'] ?? [];
            }
            return [];
        });
    }

    protected function getRecommendations($symbol)
    {
        return Cache::remember("etf_recommendations_{$symbol}", $this->cacheDuration, function () use ($symbol) {
            $url = "{$this->baseUrl}stock/recommendation?symbol={$symbol}&token={$this->apiKey}";
            $response = Http::get($url);
            return $response->successful() ? $response->json() : [];
        });
    }
}
