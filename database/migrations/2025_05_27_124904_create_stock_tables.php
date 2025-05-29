<?php
// database/migrations/2025_05_27_000000_create_stock_tables.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Stocks table
        Schema::create('stocks', function (Blueprint $table) {
            $table->string('symbol')->primary();
            $table->string('name');
            $table->string('exchange');
            $table->string('industry')->nullable();
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('country')->nullable();
            $table->timestamps();
        });

        // Stock Quotes table
        Schema::create('stock_quotes', function (Blueprint $table) {
            $table->id();
            $table->string('symbol');
            $table->decimal('price', 10, 2);
            $table->decimal('change', 10, 2);
            $table->decimal('percent_change', 10, 4);
            $table->timestamp('quote_time')->nullable();
            $table->timestamps();
            
            $table->foreign('symbol')->references('symbol')->on('stocks')->onDelete('cascade');
            $table->index('symbol');
        });

        // Stock Financials table
        Schema::create('stock_financials', function (Blueprint $table) {
            $table->string('symbol')->primary();
            $table->decimal('52_week_high', 10, 2)->nullable();
            $table->decimal('52_week_low', 10, 2)->nullable();
            $table->date('52_week_high_date')->nullable();
            $table->date('52_week_low_date')->nullable();
            $table->decimal('beta', 10, 6)->nullable();
            $table->decimal('dividend_yield', 10, 6)->nullable();
            $table->decimal('pe_ratio', 10, 6)->nullable();
            $table->decimal('pb_ratio', 10, 6)->nullable();
            $table->decimal('market_cap', 20, 2)->nullable();
            $table->json('metrics')->nullable(); // For storing all other financial metrics
            $table->timestamps();
            
            $table->foreign('symbol')->references('symbol')->on('stocks')->onDelete('cascade');
        });

        // Stock Earnings table
        Schema::create('stock_earnings', function (Blueprint $table) {
            $table->id();
            $table->string('symbol');
            $table->date('period');
            $table->integer('quarter')->nullable();
            $table->integer('year')->nullable();
            $table->decimal('actual', 10, 4)->nullable();
            $table->decimal('estimate', 10, 4)->nullable();
            $table->decimal('surprise', 10, 4)->nullable();
            $table->decimal('surprise_percent', 10, 4)->nullable();
            $table->timestamps();
            
            $table->foreign('symbol')->references('symbol')->on('stocks')->onDelete('cascade');
            $table->index(['symbol', 'period']);
        });

        // Market News table
        Schema::create('market_news', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->unsignedBigInteger('finnhub_id')->unique();
            $table->timestamp('datetime');
            $table->string('headline');
            $table->text('summary');
            $table->string('source');
            $table->string('url');
            $table->string('image')->nullable();
            $table->string('related')->nullable();
            $table->timestamps();
            
            $table->index('datetime');
        });
    }

    public function down()
    {
        Schema::dropIfExists('market_news');
        Schema::dropIfExists('stock_earnings');
        Schema::dropIfExists('stock_financials');
        Schema::dropIfExists('stock_quotes');
        Schema::dropIfExists('stocks');
    }
};