<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

interface ServiceCost {
    service: string;
    total_cost: number;
    count: number;
}

interface ProviderCost {
    provider: string;
    total_cost: number;
}

interface NovelCost {
    id: number;
    title: string;
    max_cost_per_episode: number;
    chapters_count: number;
}

interface UsageItem {
    id: number;
    provider: string;
    service: string;
    model: string;
    estimated_cost: number;
    created_at: string;
}

interface Paginated<T> {
    data: T[];
    total: number;
}

const props = defineProps<{
    totalSpend: number;
    spendByService: ServiceCost[];
    spendByProvider: ProviderCost[];
    novelsCost: NovelCost[];
    recentUsages: Paginated<UsageItem>;
}>();
</script>

<template>
    <Head title="Cost Analytics & Provider Spend" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Cost Analytics & Provider Spend</h1>
                <p class="text-xs text-gray-400">Track LLM, ElevenLabs TTS, DALL-E 3 visual generation, and episode budget limits.</p>
            </div>
        </div>

        <!-- Total Spend Metric -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <span class="text-xs font-semibold text-gray-400">Total Accumulated Platform Spend</span>
            <p class="mt-2 text-3xl font-bold text-emerald-600 dark:text-emerald-400">${{ totalSpend.toFixed(4) }} USD</p>
        </div>

        <!-- Cost Breakdown Grids -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Spend by Service -->
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-base font-bold text-gray-900 dark:text-gray-100">Spend by Service Stage</h2>
                <div class="mt-4 space-y-3">
                    <div v-for="s in spendByService" :key="s.service" class="flex items-center justify-between border-b border-gray-100 pb-2 text-xs dark:border-gray-800">
                        <span class="font-bold text-gray-700 dark:text-gray-300 font-mono">{{ s.service }} ({{ s.count }} calls)</span>
                        <span class="font-bold text-indigo-600 dark:text-indigo-400">${{ s.total_cost.toFixed(4) }}</span>
                    </div>
                </div>
            </div>

            <!-- Spend by Provider -->
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-base font-bold text-gray-900 dark:text-gray-100">Spend by Provider</h2>
                <div class="mt-4 space-y-3">
                    <div v-for="p in spendByProvider" :key="p.provider" class="flex items-center justify-between border-b border-gray-100 pb-2 text-xs dark:border-gray-800">
                        <span class="font-bold text-gray-700 dark:text-gray-300 font-mono">{{ p.provider }}</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400">${{ p.total_cost.toFixed(4) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Usage Logs -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-gray-900 dark:text-gray-100">Recent External API Usage Calls</h2>
                <span class="text-xs text-gray-400 font-semibold">Total: {{ recentUsages.total }}</span>
            </div>

            <div class="mt-4 space-y-2">
                <div v-for="u in recentUsages.data" :key="u.id" class="flex items-center justify-between rounded-lg border border-gray-100 bg-gray-50 p-3 text-xs dark:border-gray-800 dark:bg-gray-950">
                    <div>
                        <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ u.provider }}</span>
                        <span class="ml-2 font-mono text-gray-700 dark:text-gray-300">{{ u.service }} ({{ u.model }})</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="font-bold text-gray-900 dark:text-gray-100">${{ u.estimated_cost.toFixed(4) }}</span>
                        <span class="text-[10px] text-gray-400">{{ u.created_at }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
