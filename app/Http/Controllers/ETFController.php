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

        $this->cacheDuration = now()->addMinutes(60); // 30 minutes cache
        $this->defaultSymbols = ['SPY', 'IVV', 'VOO', 'QQQ', 'DIA', 'IWM', 'EFA', 'EEM', 'GLD', 'TLT'];
    }

    public function getEtfFromYahoo($start = 0, $count = 25)
    {
        $url = "https://query2.finance.yahoo.com/v1/finance/screener/predefined/saved";
        $params = [
            'scrIds' => 'etf',
            'count' => $count,
            'start' => $start,
        ];

        $response = Http::get($url, $params);
        return $response->json()['finance']['result'][0]['quotes'] ?? [];
    }

    public function index(Request $request)
    {
        $page = (int) $request->input('page', 1);
        $perPage = (int) $request->input('per_page', 25);
        $start = ($page - 1) * $perPage;

        $response = Http::get('https://query2.finance.yahoo.com/v1/finance/screener/predefined/saved', [
            'scrIds' => 'etf',
            'count' => $perPage,
            'start' => $start
        ]);

        $result = $response->json()['finance']['result'][0] ?? [];
        $etfs = $result['quotes'] ?? [];
        $total = $result['total'] ?? count($etfs); // Fallback in case total is missing

        return inertia('ETF/Index', [
            'etfs' => $etfs,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage),
            ],
        ]);
    }


    // public function show($symbol)
    // {
    //     $cacheKey = "etf_tm_{$symbol}_updated_finance_data";
    //     $data = Cache::remember($cacheKey, $this->cacheDuration, function () use ($symbol) {
    //         return [
    //             'profile' => $this->getETFProfile($symbol),
    //             'quote' => $this->getETFQuote($symbol),
    //             'financials' => $this->getFinancials($symbol),
    //             'distributions' => $this->getDistributions($symbol),
    //             'chartData' => $this->getChartData($symbol),
    //             'news' => $this->getNews($symbol),
    //             'peers' => $this->getPeers($symbol),
    //             'holdings' => $this->getETFHoldings($symbol),
    //             'performance' => $this->getETFPerformanceSummary($symbol),
    //             'lastUpdated' => now()->toDateTimeString(),
    //             'topMovers' => $this->getTopMovers(15)
    //         ];
    //     });

    //     return inertia('ETF/Show', array_merge(['symbol' => $symbol], $data));
    // }
    public function show($symbol)
    {
        $cacheKey = "etf_tm_{$symbol}_base";

        $baseData = Cache::remember($cacheKey, $this->cacheDuration, function () use ($symbol) {
            return [
                'profile' => $this->getETFProfile($symbol),
                'quote' => $this->getETFQuote($symbol),
                'chartData' => $this->getChartData($symbol),
                'topMovers' => $this->getTopMovers(15),
                'symbol' => $symbol
            ];
        });

        return inertia('ETF/Show', array_merge(['symbol' => $symbol], $baseData));
    }
    public function loadTabData($symbol, $tab)
    {

        switch ($tab) {
            case 'financials':
                return response()->json(['financials' => $this->getFinancials($symbol)]);
            case 'dividends':
                return response()->json(['distributions' => $this->getDistributions($symbol)]);
            case 'peers':
                return response()->json(['peers' => $this->getPeers($symbol)]);
            case 'holdings':
                return response()->json(['holdings' => $this->getETFHoldings($symbol)]);
            case 'performance':
                return response()->json(['performance' => $this->getETFPerformanceSummary($symbol)]);
            case 'news':
                return response()->json(['news' => $this->getNews($symbol)]);
            default:
                return response()->json([]);
        }
    }


    protected function getTopMovers($limit = 5)
    {
        $allETFs = $this->getAllETFData();
        usort($allETFs, function ($a, $b) {
            return abs($b['percentChange']) <=> abs($a['percentChange']);
        });

        return array_slice($allETFs, 0, $limit);
    }


    protected function getAllETFData()
    {
        return Cache::remember('all_etfs_yahoo_cached', $this->cacheDuration, function () {
            $etfs = [];

            $count = 100;
            $start = 0;

            $response = Http::get('https://query2.finance.yahoo.com/v1/finance/screener/predefined/saved', [
                'scrIds' => 'etf',
                'count' => $count,
                'start' => $start,
            ]);

            $quotes = $response->json()['finance']['result'][0]['quotes'] ?? [];

            foreach ($quotes as $quote) {
                if (!isset($quote['symbol'])) {
                    continue;
                }

                $etfs[] = [
                    'symbol' => $quote['symbol'],
                    'description' => $quote['shortName'] ?? $quote['longName'] ?? $quote['symbol'],
                    'type' => 'ETF',
                    'price' => $quote['regularMarketPrice'] ?? null,
                    'change' => $quote['regularMarketChange'] ?? null,
                    'percentChange' => $quote['regularMarketChangePercent'] ?? null,
                    'currency' => $quote['currency'] ?? 'USD',
                    'exchange' => $quote['fullExchangeName'] ?? 'Unknown',
                    'sector' => $quote['sector'] ?? null,
                ];
            }

            return $etfs;
        });
    }


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
            return is_array($data) && count($data) > 0 ? $data[0] : null;
        });
    }


    protected function getFinancials($symbol)
    {
        return Cache::remember("etf_financial_{$symbol}", $this->cacheDuration, function () use ($symbol) {
            $quote = $this->getETFQuote($symbol);

            if (!$quote) {
                return [];
            }

            return [
                'marketCapitalization' => $quote['marketCap'] ?? null,
                'peRatio' => $quote['pe'] ?? null,
                'eps' => $quote['eps'] ?? null,
                'priceAvg50' => $quote['priceAvg50'] ?? null,
                'priceAvg200' => $quote['priceAvg200'] ?? null,
                'yearHigh' => $quote['yearHigh'] ?? null,
                'yearLow' => $quote['yearLow'] ?? null,
                'volume' => $quote['volume'] ?? null,
                'avgVolume' => $quote['avgVolume'] ?? null,
            ];
        });
    }

    protected function getDistributions($symbol)
    {
        return Cache::remember("etf_distributions_{$symbol}", $this->cacheDuration, function () use ($symbol) {
            $response = Http::get("{$this->fmpBaseUrl}historical-price-full/stock_dividend/{$symbol}?apikey={$this->fmpApiKey}");

            $data = $response->json();
            return $data['historical'] ?? [];
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

    protected function getETFHoldings($symbol)
    {
        return Cache::remember("etf_holdings_fmp_limit_{$symbol}", $this->cacheDuration, function () use ($symbol) {
            $url = "{$this->fmpBaseUrl}etf-holder/{$symbol}?apikey={$this->fmpApiKey}&limit=15";
            $response = Http::get($url);

            return $response->successful() ? $response->json() : [];
        });
    }
    protected function getETFPerformanceSummary($symbol)
    {

        return Cache::remember("etf_new_perform_data_{$symbol}", $this->cacheDuration, function () use ($symbol) {
            $url = "{$this->fmpBaseUrl}historical-price-full/{$symbol}?apikey={$this->fmpApiKey}";
            $response = Http::get($url);
            return $response->successful() ? $response->json()['historical'] ?? [] : [];
        });
    }
}
