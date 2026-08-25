<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';

interface Novel {
    id: number;
    title: string;
    rights_status?: string;
}

interface ChapterSummary {
    summary: string;
    important_reveals: string[] | null;
    unresolved_questions: string[] | null;
}

interface StoryEvent {
    id: number;
    sequence: number;
    description: string;
    importance_score: number;
}

interface Script {
    id: number;
    version: number;
    status: string;
    word_count: number;
    character_count: number;
}

interface Subtitle {
    format: string;
    url: string;
}

interface ProductionStep {
    stage: string;
    status: string;
}

interface ProductionRun {
    id: number;
    status: string;
    current_stage: string;
    steps: ProductionStep[];
}

interface Publication {
    title: string;
    description: string;
    tags: string[];
    visibility: string;
    youtube_video_id: string;
    publish_status: string;
    thumbnail_url: string | null;
}

interface Budget {
    allowed: boolean;
    currentCost: number;
    limit: number;
    remaining: number;
}

interface SourceVersion {
    id: number;
    version: number;
    import_method: string;
    imported_by: string | null;
    content_hash: string;
    created_at: string;
}

interface Fact {
    id: number;
    fact_type: string;
    statement: string;
    confidence: number;
    is_verified: boolean;
}

interface MediaAssetItem {
    id: number;
    type: string;
    version: number;
    storage_disk: string;
    storage_path: string;
    mime_type: string | null;
    size: number;
    status: string;
}

interface Chapter {
    id: number;
    novel_id: number;
    chapter_number: number;
    title: string;
    status: string;
    source_text: string;
    source_hash: string;
    imported_at: string;
    analyzed_at: string | null;
    scripted_at: string | null;
}

const props = defineProps<{
    chapter: Chapter;
    novel: Novel;
    summary: ChapterSummary | null;
    events: StoryEvent[];
    script: Script | null;
    narrationUrl: string | null;
    videoUrl: string | null;
    subtitles: Subtitle[];
    productionRun: ProductionRun | null;
    publication: Publication | null;
    budget: Budget;
    sourceVersions?: SourceVersion[];
    facts?: Fact[];
    mediaAssets?: MediaAssetItem[];
}>();

const isAnalyzing = ref(false);
const isGeneratingScript = ref(false);
const isGeneratingAudio = ref(false);
const isPlanningScenes = ref(false);
const isRenderingVideo = ref(false);
const isLaunchingProduction = ref(false);
const isPublishingYouTube = ref(false);
const showSourceText = ref(false);

const runAnalysis = () => {
    isAnalyzing.value = true;
    router.post(`/chapters/${props.chapter.id}/analyze`, {}, {
        onFinish: () => {
            isAnalyzing.value = false;
        },
    });
};

const generateScript = () => {
    isGeneratingScript.value = true;
    router.post(`/chapters/${props.chapter.id}/script/generate`, {}, {
        onFinish: () => {
            isGeneratingScript.value = false;
        },
    });
};

const generateAudio = () => {
    if (!props.script) return;
    isGeneratingAudio.value = true;
    router.post(`/scripts/${props.script.id}/audio/generate`, {}, {
        onFinish: () => {
            isGeneratingAudio.value = false;
        },
    });
};

const planScenes = () => {
    isPlanningScenes.value = true;
    router.post(`/chapters/${props.chapter.id}/scenes/plan`, {}, {
        onFinish: () => {
            isPlanningScenes.value = false;
        },
    });
};

const renderVideo = () => {
    isRenderingVideo.value = true;
    router.post(`/chapters/${props.chapter.id}/video/render`, {}, {
        onFinish: () => {
            isRenderingVideo.value = false;
        },
    });
};

const startOneClickProduction = () => {
    isLaunchingProduction.value = true;
    router.post(`/chapters/${props.chapter.id}/production/start`, {}, {
        onFinish: () => {
            isLaunchingProduction.value = false;
        },
    });
};

const publishYouTube = () => {
    isPublishingYouTube.value = true;
    router.post(`/chapters/${props.chapter.id}/youtube/publish`, {}, {
        onFinish: () => {
            isPublishingYouTube.value = false;
        },
    });
};
</script>

<template>
    <Head :title="`Chapter ${chapter.chapter_number}: ${chapter.title}`" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <!-- Header & Action Toolbar -->
        <div class="flex flex-col justify-between gap-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm md:flex-row md:items-center dark:border-gray-800 dark:bg-gray-900">
            <div>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                        Ch. {{ chapter.chapter_number }}: {{ chapter.title }}
                    </h1>
                    <span class="rounded-full bg-indigo-100 px-3 py-0.5 text-xs font-bold text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300">
                        {{ chapter.status }}
                    </span>
                    <span class="rounded-full bg-emerald-100 px-3 py-0.5 text-xs font-bold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                        Rights: {{ novel.rights_status || 'PERMISSION_GRANTED' }}
                    </span>
                </div>
                <p class="mt-1 text-xs text-gray-400 font-mono">SHA-256: {{ chapter.source_hash.substring(0, 16) }}...</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button
                    @click="startOneClickProduction"
                    :disabled="isLaunchingProduction"
                    class="rounded-lg bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow hover:bg-emerald-500 disabled:opacity-50"
                >
                    {{ isLaunchingProduction ? 'Launching Pipeline...' : '🚀 One-Click Start' }}
                </button>

                <button
                    @click="runAnalysis"
                    :disabled="isAnalyzing"
                    class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-semibold text-gray-700 shadow-sm hover:bg-gray-50 disabled:opacity-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200"
                >
                    {{ isAnalyzing ? 'Analyzing...' : '⚡ Facts' }}
                </button>

                <button
                    @click="generateScript"
                    :disabled="isGeneratingScript"
                    class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-semibold text-gray-700 shadow-sm hover:bg-gray-50 disabled:opacity-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200"
                >
                    {{ isGeneratingScript ? 'Drafting...' : '✍ Script' }}
                </button>

                <button
                    @click="generateAudio"
                    :disabled="!script || isGeneratingAudio"
                    class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-semibold text-gray-700 shadow-sm hover:bg-gray-50 disabled:opacity-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200"
                >
                    {{ isGeneratingAudio ? 'Synthesizing...' : '🎙 Audio' }}
                </button>

                <button
                    @click="planScenes"
                    :disabled="isPlanningScenes"
                    class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-semibold text-gray-700 shadow-sm hover:bg-gray-50 disabled:opacity-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200"
                >
                    {{ isPlanningScenes ? 'Planning...' : '🎨 Visuals' }}
                </button>

                <button
                    @click="renderVideo"
                    :disabled="isRenderingVideo"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-bold text-white shadow hover:bg-indigo-500 disabled:opacity-50"
                >
                    {{ isRenderingVideo ? 'Rendering...' : '🎬 Render 1080p' }}
                </button>
            </div>
        </div>

        <!-- Budget & Episode Cost Protection Progress Bar -->
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between text-xs font-bold text-gray-700 dark:text-gray-300">
                <span>Episode Cost Budget</span>
                <span>${{ budget.currentCost.toFixed(2) }} / ${{ budget.limit.toFixed(2) }}</span>
            </div>
            <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                <div
                    class="h-full rounded-full transition-all duration-300"
                    :class="budget.allowed ? 'bg-emerald-500' : 'bg-red-500'"
                    :style="{ width: Math.min((budget.currentCost / budget.limit) * 100, 100) + '%' }"
                ></div>
            </div>
        </div>

        <!-- Production Pipeline Run Stage Stepper -->
        <div v-if="productionRun" class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">Production Orchestrator Pipeline</h3>
                <span class="text-xs font-mono text-gray-400">Run #{{ productionRun.id }} - {{ productionRun.current_stage }}</span>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                <div
                    v-for="step in productionRun.steps"
                    :key="step.stage"
                    class="flex items-center gap-2 rounded-lg border px-3 py-1.5 text-xs font-semibold"
                    :class="{
                        'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-300': step.status === 'COMPLETE',
                        'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-300': step.status === 'RUNNING',
                        'border-gray-200 bg-gray-50 text-gray-500 dark:border-gray-800 dark:bg-gray-850 dark:text-gray-400': step.status === 'PENDING',
                        'border-red-200 bg-red-50 text-red-800 dark:border-red-900 dark:bg-red-950 dark:text-red-300': step.status === 'FAILED',
                    }"
                >
                    <span>{{ step.stage }}</span>
                    <span class="text-[10px] opacity-75">({{ step.status }})</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Main Column (Left/Center) -->
            <div class="space-y-6 lg:col-span-2">
                <!-- Source Content Card (Private) -->
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-bold text-gray-900 dark:text-gray-100">Source Chapter Text</h2>
                            <p class="text-xs text-gray-400">Private internal text used exclusively for fact extraction.</p>
                        </div>
                        <button
                            @click="showSourceText = !showSourceText"
                            class="text-xs font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400"
                        >
                            {{ showSourceText ? 'Hide Source' : 'View Source' }}
                        </button>
                    </div>

                    <div v-if="showSourceText" class="mt-4 max-h-96 overflow-y-auto rounded-lg bg-gray-50 p-4 font-mono text-xs text-gray-700 dark:bg-gray-950 dark:text-gray-300">
                        {{ chapter.source_text }}
                    </div>
                </div>

                <!-- Source Versions History -->
                <div v-if="sourceVersions && sourceVersions.length > 0" class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <h2 class="text-base font-bold text-gray-900 dark:text-gray-100">Immutable Source Version History</h2>
                    <div class="mt-3 space-y-2">
                        <div v-for="ver in sourceVersions" :key="ver.id" class="flex items-center justify-between rounded-lg border border-gray-100 bg-gray-50 p-3 text-xs dark:border-gray-800 dark:bg-gray-950">
                            <div>
                                <span class="font-bold text-indigo-600 dark:text-indigo-400">Version {{ ver.version }}</span>
                                <span class="ml-2 text-gray-400">Imported via {{ ver.import_method }} by {{ ver.imported_by }}</span>
                            </div>
                            <span class="font-mono text-gray-400">{{ ver.content_hash.substring(0, 12) }}...</span>
                        </div>
                    </div>
                </div>

                <!-- Structured Fact Extraction Card -->
                <div v-if="summary" class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <h2 class="text-base font-bold text-gray-900 dark:text-gray-100">Structured Chapter Analysis</h2>
                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ summary.summary }}</p>

                    <div v-if="summary.important_reveals?.length" class="mt-4">
                        <h3 class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">Key Plot Reveals</h3>
                        <ul class="mt-1 list-disc pl-5 text-xs text-gray-600 dark:text-gray-400">
                            <li v-for="(reveal, idx) in summary.important_reveals" :key="idx">{{ reveal }}</li>
                        </ul>
                    </div>
                </div>

                <!-- Media Asset Registry -->
                <div v-if="mediaAssets && mediaAssets.length > 0" class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <h2 class="text-base font-bold text-gray-900 dark:text-gray-100">Media Asset Registry</h2>
                    <div class="mt-3 space-y-2">
                        <div v-for="asset in mediaAssets" :key="asset.id" class="flex items-center justify-between rounded-lg border border-gray-100 bg-gray-50 p-3 text-xs dark:border-gray-800 dark:bg-gray-950">
                            <div>
                                <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ asset.type }}</span>
                                <span class="ml-2 font-mono text-gray-400">{{ asset.storage_path }}</span>
                            </div>
                            <span class="rounded bg-gray-200 px-2 py-0.5 text-[10px] font-bold text-gray-700 dark:bg-gray-800 dark:text-gray-300">{{ asset.status }}</span>
                        </div>
                    </div>
                </div>

                <!-- Video Preview & Subtitles Card -->
                <div v-if="videoUrl || narrationUrl" class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <h2 class="text-base font-bold text-gray-900 dark:text-gray-100">Rendered Video & Audio Player</h2>

                    <div v-if="videoUrl" class="mt-4">
                        <video :src="videoUrl" controls class="w-full rounded-lg shadow"></video>
                    </div>

                    <div v-else-if="narrationUrl" class="mt-4">
                        <p class="text-xs text-gray-400 mb-2">Master Hindi Narration Track</p>
                        <audio :src="narrationUrl" controls class="w-full"></audio>
                    </div>
                </div>
            </div>

            <!-- Sidebar (Right) -->
            <div class="space-y-6">
                <!-- YouTube Publication Card -->
                <div v-if="videoUrl" class="rounded-xl border border-indigo-200 bg-indigo-50/50 p-6 shadow-sm dark:border-indigo-900 dark:bg-indigo-950/40">
                    <h3 class="text-sm font-bold text-indigo-900 dark:text-indigo-200">YouTube Publishing</h3>
                    
                    <div v-if="publication" class="mt-3 space-y-2 text-xs">
                        <p class="font-bold text-gray-900 dark:text-gray-100">{{ publication.title }}</p>
                        <span class="inline-block rounded bg-amber-100 px-2 py-0.5 font-bold text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                            {{ publication.visibility }} ({{ publication.publish_status }})
                        </span>
                        <p class="font-mono text-gray-400">ID: {{ publication.youtube_video_id }}</p>
                    </div>

                    <button
                        @click="publishYouTube"
                        :disabled="isPublishingYouTube"
                        class="mt-4 w-full rounded-lg bg-red-600 px-4 py-2.5 text-xs font-bold text-white shadow hover:bg-red-500 disabled:opacity-50"
                    >
                        {{ isPublishingYouTube ? 'Uploading Draft...' : '📺 Publish to YouTube' }}
                    </button>
                </div>

                <!-- Script Review Shortcut -->
                <div v-if="script" class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">Hindi Script</h3>
                    <p class="mt-1 text-xs text-gray-400">Version {{ script.version }} • {{ script.word_count }} words</p>
                    
                    <Link
                        :href="`/scripts/${script.id}/review`"
                        class="mt-4 block w-full rounded-lg bg-indigo-50 px-4 py-2 text-center text-xs font-bold text-indigo-600 hover:bg-indigo-100 dark:bg-indigo-950 dark:text-indigo-300"
                    >
                        Open Side-by-Side Review
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
