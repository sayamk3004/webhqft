<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}
</script>

<template>
  <Head title="Welcome to WebHQ Finance Terminal Platform" />

  <div class="relative bg-gray-900 text-white">
    <section class="relative flex items-center justify-center min-h-screen bg-cover bg-center" style="background-image: url('https://example.com/hero-bg.jpg');">
      <div class="absolute inset-0 bg-black opacity-50"></div>
      <div class="relative z-10 text-center text-white py-32 px-6">
        <h1 class="text-4xl sm:text-5xl font-bold mb-4">
          Unlock the Power of Stock, ETF, and Market Analysis
        </h1>
        <p class="text-lg mb-8">
          Compare Stocks, ETFs, and Get Detailed Insights to make smarter investment decisions.
        </p>
        <div class="flex justify-center space-x-4">
          <Link :href="route('login')" class="bg-[#FF2D20] text-white px-6 py-2 rounded-lg hover:bg-[#FF1E1D] transition duration-300">
            Log In
          </Link>
          <Link :href="route('register')" class="bg-transparent text-white border-2 border-white px-6 py-2 rounded-lg hover:bg-[#FF2D20] hover:text-white transition duration-300">
            Register
          </Link>
        </div>
      </div>
    </section>

    <section class="py-16 bg-gray-800 text-white">
      <div class="container mx-auto text-center">
        <h2 class="text-3xl sm:text-4xl font-semibold mb-8">Why Choose Our Platform?</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-12">
          <div class="bg-[#FF2D20] p-8 rounded-xl shadow-lg">
            <h3 class="text-2xl font-semibold mb-4">Comprehensive Stock & ETF Analysis</h3>
            <p class="text-lg">
              Dive deep into the analysis of stocks, ETFs, and various market indices for better investment decisions.
            </p>
          </div>
          <div class="bg-[#FF2D20] p-8 rounded-xl shadow-lg">
            <h3 class="text-2xl font-semibold mb-4">Real-time Market Data</h3>
            <p class="text-lg">
              Get live stock prices, ETF trends, and up-to-the-minute market data to stay ahead.
            </p>
          </div>
          <div class="bg-[#FF2D20] p-8 rounded-xl shadow-lg">
            <h3 class="text-2xl font-semibold mb-4">Advanced Comparisons</h3>
            <p class="text-lg">
              Easily compare multiple stocks, ETFs, and market trends side-by-side to make better choices.
            </p>
          </div>
        </div>
      </div>
    </section>
    <section class="py-16 bg-gray-900 text-white">
      <div class="container mx-auto text-center">
        <h2 class="text-3xl sm:text-4xl font-semibold mb-8">Market Overview & Insights</h2>
        <p class="text-lg mb-8">
          Our platform offers a clear overview of the market with in-depth analysis and visualizations for easy understanding.
        </p>
        <div class="flex justify-center space-x-8">
          <Link :href="route('stocks.index')" class="bg-[#FF2D20] text-white px-8 py-3 rounded-lg hover:bg-[#FF1E1D] transition duration-300">
            Explore Stocks
          </Link>
          <Link :href="route('etfs.index')" class="bg-[#FF2D20] text-white px-8 py-3 rounded-lg hover:bg-[#FF1E1D] transition duration-300">
            Explore ETFs
          </Link>
        </div>
      </div>
    </section>

    <footer class="bg-gray-800 text-white py-8">
      <div class="container mx-auto text-center">
        <p>&copy; 2025 WebHQ Finance Terminal Platform. All Rights Reserved.</p>
      </div>
    </footer>

  </div>
</template>
