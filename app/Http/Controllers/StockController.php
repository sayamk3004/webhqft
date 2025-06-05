<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class StockController extends Controller
{
    protected $baseUrl;
    protected $fmpbaseUrl;
    protected $auxbaseUrl;
    protected $apiKey;
    protected $fmpApiKey;
    protected $auxApiKey;
    protected $cacheDuration;
    protected $defaultSymbols;


    public function __construct()
    {
        $this->baseUrl = config('services.finnhub.base_url');
        $this->fmpbaseUrl = config('services.fmpApi.base_url');
        $this->auxbaseUrl = config('services.marketaux.base_url');
        $this->apiKey = config('services.finnhub.key');
        $this->fmpApiKey = config('services.fmpApi.key');
        $this->auxApiKey = config('services.marketaux.key');
        $this->cacheDuration = now()->addMinutes(1500);
        $this->defaultSymbols = ['AAPL', 'MSFT', 'GOOGL', 'AMZN', 'TSLA', 'META', 'NVDA', 'BRK.B', 'JNJ', 'V', 'WMT', 'PG', 'MA', 'DIS', 'BAC'];
    }

    public function compareData(Request $request)
    {
        $symbols = $request->input('symbols', []);

        $results = [];

        foreach ($symbols as $symbol) {
            $quote = Cache::get("stock_quote_{$symbol}");
            $profile = Cache::get("stock_profile_{$symbol}");

            if ($quote && $profile) {
                $results[] = [
                    'symbol' => $symbol,
                    'price' => $quote['c'] ?? null,
                    'change' => $quote['d'] ?? null,
                    'percentChange' => $quote['dp'] ?? null,
                    'marketCap' => $profile['marketCapitalization'] ?? null,
                    'peRatio' => $profile['pe'] ?? null,
                    'industry' => $profile['finnhubIndustry'] ?? null,
                    'name' => $profile['name'] ?? $symbol,
                ];
            }
        }

        return response()->json($results);
    }
    public function showCompare($symbols)
    {
        $symbolList = explode(',', $symbols);

        $data = collect($symbolList)->mapWithKeys(function ($symbol) {
            return [$symbol => $this->getCompleteStockData($symbol)];
        });

        return inertia('Stocks/Compare', [
            'symbols' => $symbolList,
            'data' => $data,
        ]);
    }

    public function dashboard()
    {
        $marketnews = $this->getMarketNews();

        $stocks = $this->getPopularStocks();

        $indices = collect(Cache::remember('market_indices', $this->cacheDuration, function () {
            $response = Http::get("{$this->fmpbaseUrl}quotes/index?apikey={$this->fmpApiKey}");
            return $response->json() ?: [];
        }))->take(8);



        $gainers = collect(Cache::remember('top_gainers', $this->cacheDuration, function () {
            $response = Http::get("{$this->fmpbaseUrl}gainers?apikey={$this->fmpApiKey}");
            return $response->json() ?: [];
        }))->take(16);

        $losers = collect(Cache::remember('top_losers', $this->cacheDuration, function () {
            $response = Http::get("{$this->fmpbaseUrl}losers?apikey={$this->fmpApiKey}");
            return $response->json() ?: [];
        }))->take(16);

        $sectors = Cache::remember('sector_performance', $this->cacheDuration, function () {
            $response = Http::get("{$this->fmpbaseUrl}stock/sectors-performance?apikey={$this->fmpApiKey}");
            $data = $response->json();
            return $data['sectorPerformance'] ?? [];
        });
        $calendar = Cache::remember('economic_calendar', $this->cacheDuration, function () {
            $from = now()->format('Y-m-d');
            $to = now()->addDays(7)->format('Y-m-d');
            $response = Http::get("{$this->fmpbaseUrl}economic_calendar?from={$from}&to={$to}&apikey={$this->fmpApiKey}");
            return $response->json() ?: [];
        });

        $aiSummary = Cache::remember('ai_summary', $this->cacheDuration, function () {
            $response = Http::get("{$this->auxbaseUrl}news/all", [
                'limit' => 1,
                'language' => 'en',
                'filter_entities' => 'true',
                'api_token' => $this->auxApiKey
            ]);

            return $response->json()['data'][0] ?? null;
        });

        $marketGraph = (function () {
            $popularStocks = Cache::get('popular_stocks', []);
            $symbols = array_column($popularStocks, 'ticker');

            $graphData = [];

            foreach ($symbols as $symbol) {
                $cacheKey = "graph_datsa_{$symbol}";
                $graphData[$symbol] = Cache::remember($cacheKey, $this->cacheDuration, function () use ($symbol) {
                    $url = "https://query1.finance.yahoo.com/v8/finance/chart/{$symbol}?interval=1d&range=1mo";
                    $response = Http::get($url)->json();

                    if (!isset($response['chart']['result'][0])) return null;

                    $result = $response['chart']['result'][0];

                    $timestamps = $result['timestamp'] ?? [];
                    $quote = $result['indicators']['quote'][0];

                    $dataPoints = [];

                    foreach ($timestamps as $i => $timestamp) {
                        $dataPoints[] = [
                            'timestamp' => date('Y-m-d', $timestamp),
                            'open' => $quote['open'][$i] ?? null,
                            'high' => $quote['high'][$i] ?? null,
                            'low' => $quote['low'][$i] ?? null,
                            'close' => $quote['close'][$i] ?? null,
                            'volume' => $quote['volume'][$i] ?? null,
                        ];
                    }

                    return [
                        'symbol' => $symbol,
                        'data' => $dataPoints,
                    ];
                });
            }

            return $graphData;
        });






        return inertia('Dashboard', [
            'marketNews' => $marketnews,
            'popularStocks' => $stocks,
            'indices' => $indices,
            'gainers' => $gainers,
            'losers' => $losers,
            'sectors' => $sectors,
            'calendar' => $calendar,
            'aiSummary' => $aiSummary,
            'marketGraph' => $marketGraph,
        ]);
    }
    public function movers()
    {
        $gainers = Cache::get('top_gainers');
        $losers = Cache::get('top_losers');
        return inertia('Stocks/Movers', [
            'gainers' => $gainers,
            'losers' => $losers,
        ]);
    }
    public function news()
    {
        $marketnews = $this->getAllMarketNews();
        return inertia('Stocks/News', [
            'marketNews' => $marketnews,
        ]);
    }


    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 20);
        $page = $request->input('page', 1);

        $allStocks = $this->getAllStocksData();

        if ($search) {
            $allStocks = array_filter($allStocks, function ($item) use ($search) {
                return stripos($item['symbol'], $search) !== false ||
                    stripos($item['description'], $search) !== false;
            });
        }

        $filteredStocks = array_values(array_filter($allStocks, function ($item) {
            return $item['type'] === 'Common Stock';
        }));

        $paginatedStocks = array_slice($filteredStocks, ($page - 1) * $perPage, $perPage);

        return inertia('Stocks/Index', [
            'stocks' => $paginatedStocks,
            'search' => $search,
            'lastUpdated' => Cache::get('stocks_last_updated'),
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => count($filteredStocks),
                'last_page' => ceil(count($filteredStocks) / $perPage),
            ],
        ]);
    }

    /**
     * Single stock detail view
     */
    public function show($symbol)
    {
        $cacheKey = "stock_{$symbol}_data";

        $data = Cache::remember($cacheKey, $this->cacheDuration, function () use ($symbol) {
            return  [
                'profile' => $this->getStockProfile($symbol),
                'quote' => $this->getStockQuote($symbol),
                'financials' => $this->getFinancials($symbol),
                'earnings' => $this->getEarnings($symbol),
                'chartData' => $this->getChartData($symbol),
                'lastUpdated' => now()->toDateTimeString(),
                'news' => $this->getNews($symbol),
                'peers' => $this->getPeers($symbol),
                'recommendations' => $this->getRecommendations($symbol),
            ];
        });




        return inertia('Stocks/Show', array_merge(['symbol' => $symbol], $data));
    }

    protected function getNews($symbol)
    {
        return Cache::remember("stock_news_{$symbol}", $this->cacheDuration, function () use ($symbol) {
            $url = "https://query1.finance.yahoo.com/v1/finance/search?q={$symbol}";
            $response = Http::get($url);
            return $response->json()['news'] ?? [];
        });
    }

    protected function getPeers($symbol)
    {
        return Cache::remember("stock_peers_{$symbol}", $this->cacheDuration, function () use ($symbol) {
            $url = "https://query2.finance.yahoo.com/v6/finance/recommendationsbysymbol/{$symbol}";
            $response = Http::get($url);
            return $response->json()['finance']['result'][0]['recommendedSymbols'] ?? [];
        });
    }

    protected function getRecommendations($symbol)
    {
        return Cache::remember("stock_recommendations_{$symbol}", $this->cacheDuration, function () use ($symbol) {
            $url = "{$this->baseUrl}stock/recommendation?symbol={$symbol}&token={$this->apiKey}";
            $response = Http::get($url);
            return $response->json() ?? [];
        });
    }
    /**
     * API endpoint for SPA clients to get all stocks data
     */
    public function apiAllStocks()
    {
        $data = Cache::remember('api_all_stocks', $this->cacheDuration, function () {
            return [
                'stocks' => $this->getAllStocksData(),
                'lastUpdated' => now()->toDateTimeString()
            ];
        });

        return response()->json($data);
    }

    /**
     * API endpoint for SPA clients to get single stock data
     */
    public function apiSingleStock($symbol)
    {
        $data = Cache::remember("api_stock_{$symbol}", $this->cacheDuration, function () use ($symbol) {
            return [
                'stock' => $this->getCompleteStockData($symbol),
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
        $allStocks = $this->getAllStocksData();
        Cache::put('all_stocks_data', $allStocks, $this->cacheDuration);

        Cache::put('dashboard_data', [
            'popularStocks' => $this->getPopularStocks(),
            'marketNews' => $this->getMarketNews(),
            'marketStatus' => $this->getMarketStatus(),
            'lastUpdated' => now()->toDateTimeString()
        ], $this->cacheDuration);

        Cache::put('stocks_last_updated', now()->toDateTimeString(), $this->cacheDuration);

        return response()->json(['success' => true, 'message' => 'Cache refreshed']);
    }
    protected function getAllStocksData()
    {
        return Cache::remember('all_stocks_data', $this->cacheDuration, function () {
            $stocks = [];

            foreach ($this->defaultSymbols as $symbol) {
                $stockData = $this->getBasicStockData($symbol);
                if ($stockData) {
                    $stocks[] = $stockData;
                }
            }

            return $stocks;
        });
    }

    /**
     * Get basic stock data (profile + quote)
     */
    protected function getBasicStockData($symbol)
    {
        $profile = $this->getStockProfile($symbol);
        $quote = $this->getStockQuote($symbol);

        if ($profile && $quote) {
            return [
                'symbol' => $symbol,
                'description' => $profile['name'] ?? $symbol,
                'type' => 'Common Stock',
                'price' => $quote['c'] ?? null,
                'change' => $quote['d'] ?? null,
                'percentChange' => $quote['dp'] ?? null,
                'currency' => $profile['currency'] ?? 'USD',
                'exchange' => $profile['exchange'] ?? 'NASDAQ',
                'sector' => $profile['finnhubIndustry'] ?? null,
            ];
        }

        return null;
    }

    /**
     * Get complete data for a single stock
     */
    protected function getCompleteStockData($symbol)
    {
        $basicData = $this->getBasicStockData($symbol);

        if (!$basicData) {
            return null;
        }

        return array_merge($basicData, [
            'financials' => $this->getFinancials($symbol),
            'earnings' => $this->getEarnings($symbol),
            'chartData' => $this->getChartData($symbol),
        ]);
    }

    /**
     * Get popular stocks for dashboard
     */
    protected function getPopularStocks()
    {
        $popularStocks = Cache::remember('popular_stocks', $this->cacheDuration, function () {
            $response = Http::get("{$this->fmpbaseUrl}stock/actives?apikey={$this->fmpApiKey}");
            return $response->json()['mostActiveStock'] ?? [];
        });
        $stocks = [];

        foreach ($popularStocks as $symbol) {
            $stockData = $this->getBasicStockData($symbol['ticker']);
            if ($stockData) {
                $stocks[] = $stockData;
            }
        }

        return $stocks;
    }

    /**
     * API methods below with individual caching
     */
    protected function getMarketNews()
    {
        return Cache::remember('market_news', $this->cacheDuration, function () {
            $url = "{$this->fmpbaseUrl}stock_news?limit=20&apikey={$this->fmpApiKey}";
            $response = Http::get($url);
            $json = $response->json();

            return is_array($json) ? $json : [];
        });
    }
    protected function getAllMarketNews()
    {
        return Cache::remember('market_news_a;;', $this->cacheDuration, function () {
            $url = "{$this->fmpbaseUrl}stock_news?apikey={$this->fmpApiKey}";
            $response = Http::get($url);
            $json = $response->json();

            return is_array($json) ? $json : [];
        });
    }

    protected function getMarketStatus()
    {
        return Cache::remember('market_status', $this->cacheDuration, function () {
            $response = Http::get("{$this->baseUrl}stock/market-status?exchange=US&token={$this->apiKey}");
            return $response->json() ?? [];
        });
    }

    protected function getStockProfile($symbol)
    {
        return Cache::remember("stock_profile_{$symbol}", $this->cacheDuration, function () use ($symbol) {
            $response = Http::get("{$this->baseUrl}stock/profile2?symbol={$symbol}&token={$this->apiKey}");
            return $response->successful() ? $response->json() : null;
        });
    }

    protected function getStockQuote($symbol)
    {
        return Cache::remember("stock_quote_{$symbol}", $this->cacheDuration, function () use ($symbol) {
            $response = Http::get("{$this->baseUrl}quote?symbol={$symbol}&token={$this->apiKey}");
            return $response->successful() ? $response->json() : null;
        });
    }

    protected function getFinancials($symbol)
    {
        return Cache::remember("stock_financials_{$symbol}", $this->cacheDuration, function () use ($symbol) {
            $response = Http::get("{$this->baseUrl}stock/metric?symbol={$symbol}&metric=all&token={$this->apiKey}");
            return $response->successful() ? ($response->json()['metric'] ?? []) : [];
        });
    }

    protected function getEarnings($symbol)
    {
        return Cache::remember("stock_earnings_{$symbol}", $this->cacheDuration, function () use ($symbol) {
            $response = Http::get("{$this->baseUrl}stock/earnings?symbol={$symbol}&token={$this->apiKey}");
            return $response->successful() ? $response->json() : [];
        });
    }

    protected function getChartData($symbol, $resolution = 'D', $from = null, $to = null)
    {
        $cacheKey = "yahoo_chart_{$symbol}_1y";

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($symbol) {
            $url = "https://query1.finance.yahoo.com/v8/finance/chart/{$symbol}?interval=1d&range=1y";

            $response = Http::get($url);
            if (!$response->successful()) return [];

            $data = $response->json()['chart']['result'][0] ?? null;
            if (!$data) return [];

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
    public function showIndex($symbol)
    {
        $indices = Cache::get('market_indices', []);
        $index = collect($indices)->firstWhere('symbol', $symbol);

        if (!$index) {
            abort(404, 'Index not found.');
        }

        return inertia('Indices/Show', ['index' => $index]);
    }
}
