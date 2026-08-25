<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';

interface QueueItem {
    name: string;
    depth: number;
    status: string;
}

interface StepItem {
    stage: string;
    status: string;
    attempts: number;
    error: string | null;
}

interface RunItem {
    id: number;
    chapter_id: number;
    novel_title: string;
    chapter_title: string;
    status: string;
    current_stage: string;
    started_at: string;
    steps: StepItem[];
}

interface Paginated<T> {
    data: T[];
    total: number;
}

const props = defineProps<{
    runs: Paginated<RunItem>;
    activeRunsCount: number;
    failedStepsCount: number;
    queues: QueueItem[];
}>();

const resumeRun = (chapterId: number) => {
    router.post(`/chapters/${chapterId}/production/resume`);
};
</script>

<template>
    <Head title="Pipeline Operations Monitor" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Pipeline Operations Monitor</h1>
                <p class="text-xs text-gray-400">Real-time worker metrics, queue health, stage state machine, and error recovery.</p>
            </div>
        </div>

        <!-- Metrics Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <span class="text-xs font-semibold text-gray-400">Active Production Runs</span>
                <p class="mt-2 text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ activeRunsCount }}</p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <span class="text-xs font-semibold text-gray-400">Failed Steps</span>
                <p class="mt-2 text-2xl font-bold text-red-600 dark:text-red-400">{{ failedStepsCount }}</p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <span class="text-xs font-semibold text-gray-400">System Horizon Queues</span>
                <p class="mt-2 text-2xl font-bold text-emerald-600 dark:text-emerald-400">HEALTHY</p>
            </div>
        </div>

        <!-- Queue Status Matrix -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-base font-bold text-gray-900 dark:text-gray-100">Workload Queue Health</h2>

            <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
                <div v-for="q in queues" :key="q.name" class="rounded-lg border border-gray-100 bg-gray-50 p-3 dark:border-gray-800 dark:bg-gray-950">
                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300 font-mono">{{ q.name }}</span>
                    <div class="mt-1 flex items-center justify-between text-xs">
                        <span class="text-gray-400">Depth: {{ q.depth }}</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ q.status }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Production Runs & Steps History -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-gray-900 dark:text-gray-100">Recent Production Runs</h2>
                <span class="text-xs text-gray-400 font-semibold">Total: {{ runs.total }}</span>
            </div>

            <div class="mt-4 space-y-4">
                <div v-for="run in runs.data" :key="run.id" class="rounded-xl border border-gray-100 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-950">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400">{{ run.novel_title }}</span>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ run.chapter_title }}</h3>
                            <span class="text-xs text-gray-400">Run #{{ run.id }} • Stage: {{ run.current_stage }}</span>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="rounded bg-indigo-100 px-3 py-1 text-xs font-bold text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300">{{ run.status }}</span>
                            <button
                                v-if="run.status === 'FAILED' || run.status === 'PAUSED'"
                                @click="resumeRun(run.chapter_id)"
                                class="rounded-lg bg-amber-600 px-3 py-1.5 text-xs font-bold text-white shadow hover:bg-amber-500"
                            >
                                Resume Pipeline
                            </button>
                        </div>
                    </div>

                    <!-- Steps Timeline -->
                    <div class="mt-3 flex flex-wrap gap-2">
                        <div
                            v-for="step in run.steps"
                            :key="step.stage"
                            class="rounded border px-2.5 py-1 text-[11px] font-mono"
                            :class="{
                                'border-emerald-300 bg-emerald-100 text-emerald-900 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-300': step.status === 'COMPLETE',
                                'border-amber-300 bg-amber-100 text-amber-900 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-300': step.status === 'RUNNING',
                                'border-gray-200 bg-white text-gray-500 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400': step.status === 'PENDING',
                                'border-red-300 bg-red-100 text-red-900 dark:border-red-900 dark:bg-red-950 dark:text-red-300': step.status === 'FAILED',
                            }"
                        >
                            {{ step.stage }} ({{ step.status }})
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
