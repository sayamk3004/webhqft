<script setup>
defineProps({
    financials: Object,
});
</script>

<template>
    <div class="border border-gray-800 bg-default text-white shadow-sm rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Key Metrics</h2>
        
        <div class="space-y-4">
            <div>
                <h3 class="font-semibold text-gray-700">Valuation</h3>
                <div class="mt-2 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Market Cap</span>
                        <span>{{ financials.marketCapitalization ? `$${(financials.marketCapitalization / 1000000000).toFixed(2)}B` : 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">P/E Ratio</span>
                        <span>{{ financials.peNormalizedAnnual?.toFixed(2) || 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">P/S Ratio</span>
                        <span>{{ financials.psAnnual?.toFixed(2) || 'N/A' }}</span>
                    </div>
                </div>
            </div>
            
            <div>
                <h3 class="font-semibold text-gray-700">Profitability</h3>
                <div class="mt-2 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Profit Margin</span>
                        <span>{{ financials.netMarginAnnual?.toFixed(2) ? `${(financials.netMarginAnnual * 100).toFixed(2)}%` : 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">ROE</span>
                        <span>{{ financials.roeAnnual?.toFixed(2) ? `${(financials.roeAnnual * 100).toFixed(2)}%` : 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>