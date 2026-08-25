<script setup lang="ts">
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';

type SceneAsset = {
    id: number;
    asset_type: string;
    prompt: string;
    url: string | null;
    width: number;
    height: number;
    cost: number;
    provider: string;
};

type Scene = {
    id: number;
    sequence: number;
    start_ms: number;
    end_ms: number;
    description: string;
    image_prompt: string;
    camera_motion: string;
    status: string;
    assets: SceneAsset[];
};

type Chapter = {
    id: number;
    chapter_number: number;
    title: string;
};

const props = defineProps<{
    chapter: Chapter;
    scenes: Scene[];
}>();

const regeneratingSceneId = ref<number | null>(null);

const regenerateScene = (sceneId: number) => {
    regeneratingSceneId.value = sceneId;
    router.post(`/scenes/${sceneId}/regenerate`, {}, {
        onFinish: () => {
            regeneratingSceneId.value = null;
        },
    });
};
</script>

<template>
    <Head :title="`Scene Timeline Editor - Ch. ${chapter.chapter_number}`" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <!-- Header -->
        <div class="flex flex-col justify-between gap-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm md:flex-row md:items-center dark:border-gray-800 dark:bg-gray-900">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                    Scene Timeline Editor: Chapter {{ chapter.chapter_number }}
                </h1>
                <p class="text-xs text-gray-400">
                    Visual timeline of 25–40 cinematic widescreen scenes mapped to narration timestamps.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <span class="rounded bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                    Total Scenes: {{ scenes.length }}
                </span>
            </div>
        </div>

        <!-- Scene Grid / Timeline Cards -->
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="scene in scenes"
                :key="scene.id"
                class="flex flex-col justify-between overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900"
            >
                <!-- Widescreen Image Preview -->
                <div class="relative aspect-video bg-gray-950 overflow-hidden">
                    <img
                        v-if="scene.assets[0]?.url"
                        :src="scene.assets[0].url"
                        :alt="scene.description"
                        class="h-full w-full object-cover"
                    />
                    <div v-else class="flex h-full w-full items-center justify-center p-4 text-center text-xs text-gray-500 italic">
                        Generating Visual Asset...
                    </div>

                    <!-- Sequence & Timestamp Badge -->
                    <div class="absolute top-2 left-2 flex items-center gap-2">
                        <span class="rounded bg-indigo-600 px-2 py-0.5 text-[10px] font-bold text-white shadow">
                            #{{ scene.sequence }}
                        </span>
                        <span class="rounded bg-black/60 px-2 py-0.5 text-[10px] font-mono text-white backdrop-blur">
                            {{ (scene.start_ms / 1000).toFixed(1) }}s - {{ (scene.end_ms / 1000).toFixed(1) }}s
                        </span>
                    </div>

                    <!-- Motion Mode Badge -->
                    <div class="absolute bottom-2 right-2">
                        <span class="rounded bg-amber-500/80 px-2 py-0.5 text-[10px] font-semibold text-white backdrop-blur">
                            🎥 {{ scene.camera_motion }}
                        </span>
                    </div>
                </div>

                <!-- Scene Description & Prompt -->
                <div class="p-4 space-y-2 flex-1">
                    <p class="text-xs font-semibold text-gray-900 dark:text-gray-100 line-clamp-2">
                        {{ scene.description }}
                    </p>
                    <p class="text-[11px] font-mono text-gray-400 line-clamp-3 bg-gray-50 p-2 rounded dark:bg-gray-800">
                        Prompt: {{ scene.image_prompt }}
                    </p>
                </div>

                <!-- Action Footer -->
                <div class="border-t border-gray-100 p-3 bg-gray-50/50 dark:border-gray-800 dark:bg-gray-800/30 flex items-center justify-between">
                    <span class="text-[10px] font-mono text-gray-400">
                        Cost: ${{ (scene.assets[0]?.cost || 0).toFixed(4) }}
                    </span>
                    <button
                        @click="regenerateScene(scene.id)"
                        :disabled="regeneratingSceneId === scene.id"
                        class="rounded bg-indigo-600 px-3 py-1 text-[11px] font-semibold text-white shadow hover:bg-indigo-500 disabled:opacity-50 cursor-pointer"
                    >
                        {{ regeneratingSceneId === scene.id ? 'Regenerating...' : '🔄 Regenerate' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
