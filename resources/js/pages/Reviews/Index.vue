<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';

interface PendingScript {
    id: number;
    chapter_id: number;
    novel_title: string;
    chapter_title: string;
    version: number;
    word_count: number;
    status: string;
    created_at: string;
}

interface PendingAudio {
    id: number;
    provider: string;
    duration_ms: number;
    cost: number;
    status: string;
    created_at: string;
}

interface PendingVisual {
    id: number;
    scene_id: number;
    asset_type: string;
    storage_path: string;
    cost: number;
    status: string;
    created_at: string;
}

interface PendingVideo {
    id: number;
    chapter_id: number;
    novel_title: string;
    chapter_title: string;
    duration_ms: number;
    status: string;
    created_at: string;
}

interface Paginated<T> {
    data: T[];
    total: number;
}

const props = defineProps<{
    scripts: Paginated<PendingScript>;
    audio: Paginated<PendingAudio>;
    visuals: Paginated<PendingVisual>;
    videos: Paginated<PendingVideo>;
}>();

const activeTab = ref<'scripts' | 'audio' | 'visuals' | 'videos'>('scripts');
</script>

<template>
    <Head title="Centralized Review Queue" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Centralized Review Queue</h1>
                <p class="text-xs text-gray-400">Review pending Hindi scripts, narration audio, visual assets, and rendered 1080p videos.</p>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex border-b border-gray-200 dark:border-gray-800">
            <button
                @click="activeTab = 'scripts'"
                class="border-b-2 px-4 py-2.5 text-xs font-bold transition-colors"
                :class="activeTab === 'scripts' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'"
            >
                Scripts ({{ scripts.total }})
            </button>
            <button
                @click="activeTab = 'audio'"
                class="border-b-2 px-4 py-2.5 text-xs font-bold transition-colors"
                :class="activeTab === 'audio' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'"
            >
                Narration Audio ({{ audio.total }})
            </button>
            <button
                @click="activeTab = 'visuals'"
                class="border-b-2 px-4 py-2.5 text-xs font-bold transition-colors"
                :class="activeTab === 'visuals' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'"
            >
                Visual Scenes ({{ visuals.total }})
            </button>
            <button
                @click="activeTab = 'videos'"
                class="border-b-2 px-4 py-2.5 text-xs font-bold transition-colors"
                :class="activeTab === 'videos' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'"
            >
                Rendered Videos ({{ videos.total }})
            </button>
        </div>

        <!-- Tab 1: Scripts Queue -->
        <div v-if="activeTab === 'scripts'" class="space-y-3">
            <div v-if="scripts.data.length === 0" class="rounded-xl border border-gray-200 bg-white p-8 text-center text-xs text-gray-400 dark:border-gray-800 dark:bg-gray-900">
                No scripts currently awaiting review.
            </div>

            <div v-for="s in scripts.data" :key="s.id" class="flex items-center justify-between rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">{{ s.novel_title }}</span>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ s.chapter_title }}</h3>
                    <p class="mt-1 text-xs text-gray-400">Version {{ s.version }} • {{ s.word_count }} words • Generated {{ s.created_at }}</p>
                </div>

                <Link
                    :href="`/scripts/${s.id}/review`"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-bold text-white shadow hover:bg-indigo-500"
                >
                    Review Script
                </Link>
            </div>
        </div>

        <!-- Tab 2: Narration Audio Queue -->
        <div v-if="activeTab === 'audio'" class="space-y-3">
            <div v-if="audio.data.length === 0" class="rounded-xl border border-gray-200 bg-white p-8 text-center text-xs text-gray-400 dark:border-gray-800 dark:bg-gray-900">
                No narration segments awaiting review.
            </div>

            <div v-for="a in audio.data" :key="a.id" class="flex items-center justify-between rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div>
                    <span class="text-xs font-bold text-gray-900 dark:text-gray-100">Audio Segment #{{ a.id }}</span>
                    <p class="text-xs text-gray-400">Provider: {{ a.provider }} • {{ (a.duration_ms / 1000).toFixed(1) }}s • Cost: ${{ a.cost.toFixed(4) }}</p>
                </div>
                <span class="rounded bg-emerald-100 px-2 py-0.5 text-xs font-bold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">{{ a.status }}</span>
            </div>
        </div>

        <!-- Tab 3: Visual Scenes Queue -->
        <div v-if="activeTab === 'visuals'" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div v-if="visuals.data.length === 0" class="col-span-full rounded-xl border border-gray-200 bg-white p-8 text-center text-xs text-gray-400 dark:border-gray-800 dark:bg-gray-900">
                No scene visuals awaiting review.
            </div>

            <div v-for="v in visuals.data" :key="v.id" class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">Scene #{{ v.scene_id }}</span>
                <p class="mt-1 font-mono text-[10px] text-gray-400 truncate">{{ v.storage_path }}</p>
                <div class="mt-3 flex items-center justify-between text-xs">
                    <span class="font-bold text-gray-700 dark:text-gray-300">${{ v.cost.toFixed(3) }}</span>
                    <span class="rounded bg-gray-100 px-2 py-0.5 font-bold text-gray-600 dark:bg-gray-800 dark:text-gray-400">{{ v.status }}</span>
                </div>
            </div>
        </div>

        <!-- Tab 4: Rendered Videos Queue -->
        <div v-if="activeTab === 'videos'" class="space-y-3">
            <div v-if="videos.data.length === 0" class="rounded-xl border border-gray-200 bg-white p-8 text-center text-xs text-gray-400 dark:border-gray-800 dark:bg-gray-900">
                No rendered videos awaiting final QA approval.
            </div>

            <div v-for="v in videos.data" :key="v.id" class="flex items-center justify-between rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">{{ v.novel_title }}</span>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ v.chapter_title }}</h3>
                    <p class="mt-1 text-xs text-gray-400">Duration: {{ (v.duration_ms / 1000).toFixed(0) }}s • {{ v.created_at }}</p>
                </div>

                <Link
                    :href="`/chapters/${v.chapter_id}`"
                    class="rounded-lg bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow hover:bg-emerald-500"
                >
                    Final QA & Export
                </Link>
            </div>
        </div>
    </div>
</template>
