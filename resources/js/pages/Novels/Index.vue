<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { index as novelIndexRoute, store as novelStoreRoute, show as novelShowRoute } from '@/routes/novels';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Novels',
                href: novelIndexRoute.url(),
            },
        ],
    },
});

type Novel = {
    id: number;
    title: string;
    slug: string;
    original_language: string;
    output_language: string;
    source_url: string | null;
    description: string | null;
    visual_style: string;
    narration_style: string;
    max_cost_per_episode: number;
    status: string;
    chapters_count?: number;
    characters_count?: number;
};

type PaginatedNovels = {
    data: Novel[];
    total?: number;
};

const props = defineProps<{
    novels: Novel[] | PaginatedNovels;
}>();

const novelList = computed<Novel[]>(() => {
    if (Array.isArray(props.novels)) {
        return props.novels;
    }
    return props.novels?.data || [];
});

const showModal = ref(false);

const form = useForm({
    title: '',
    original_language: 'en',
    output_language: 'hi',
    source_url: null as string | null,
    description: '',
    visual_style: 'dark cinematic fantasy',
    narration_style: 'conversational Hindi explanation',
    max_cost_per_episode: 5.00,
});

const submitNovel = () => {
    form.transform((data) => ({
        ...data,
        source_url: data.source_url ? data.source_url : null,
    })).post(novelStoreRoute.url(), {
        onSuccess: () => {
            showModal.value = false;
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Web Novels" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Web Novels</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Manage your web novel catalog and automated Hindi recap pipelines.</p>
            </div>
            <button
                @click="showModal = true"
                class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500 transition cursor-pointer"
            >
                + Add New Novel
            </button>
        </div>

        <!-- Novel Grid -->
        <div v-if="novelList.length > 0" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="novel in novelList"
                :key="novel.id"
                class="flex flex-col justify-between rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900"
            >
                <div>
                    <div class="flex items-center justify-between">
                        <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-600 dark:bg-indigo-950 dark:text-indigo-300">
                            {{ (novel.original_language || 'en').toUpperCase() }} → {{ (novel.output_language || 'hi').toUpperCase() }}
                        </span>
                        <span class="text-xs font-medium text-gray-400">
                            Max ${{ novel.max_cost_per_episode }}/ep
                        </span>
                    </div>

                    <h2 class="mt-3 text-lg font-bold text-gray-900 dark:text-gray-100">
                        {{ novel.title }}
                    </h2>
                    <p class="mt-1 line-clamp-2 text-xs text-gray-500 dark:text-gray-400">
                        {{ novel.description || 'No description available.' }}
                    </p>

                    <div class="mt-4 flex flex-wrap gap-2 text-xs text-gray-600 dark:text-gray-300">
                        <span class="rounded bg-gray-100 px-2 py-0.5 dark:bg-gray-800">📖 {{ novel.chapters_count || 0 }} Chapters</span>
                        <span class="rounded bg-gray-100 px-2 py-0.5 dark:bg-gray-800">👤 {{ novel.characters_count || 0 }} Characters</span>
                    </div>
                </div>

                <div class="mt-6 border-t border-gray-100 pt-4 dark:border-gray-800 flex items-center justify-between">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                        {{ novel.visual_style }}
                    </span>
                    <Link
                        :href="novelShowRoute.url(novel.id)"
                        class="text-xs font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400"
                    >
                        Manage Chapters →
                    </Link>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="flex flex-col items-center justify-center rounded-xl border border-dashed border-gray-300 p-12 text-center dark:border-gray-800">
            <p class="text-base font-semibold text-gray-700 dark:text-gray-300">No Web Novels Found</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Add a novel to begin automated chapter import and script generation.</p>
            <button
                @click="showModal = true"
                class="mt-4 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500 cursor-pointer"
            >
                Add Novel
            </button>
        </div>
    </div>

    <!-- Add Novel Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm">
        <div class="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl dark:bg-gray-900 dark:text-gray-100">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3 dark:border-gray-800">
                <h3 class="text-lg font-bold">Add Web Novel</h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 font-bold">✕</button>
            </div>

            <form @submit.prevent="submitNovel" class="mt-4 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Novel Title *</label>
                    <input
                        v-model="form.title"
                        type="text"
                        required
                        class="mt-1 w-full rounded-md border border-gray-300 p-2 text-sm dark:border-gray-700 dark:bg-gray-800"
                        placeholder="Shadow Slave"
                    />
                    <p v-if="form.errors.title" class="mt-1 text-xs text-red-500">{{ form.errors.title }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Source Language *</label>
                        <input
                            v-model="form.original_language"
                            type="text"
                            required
                            class="mt-1 w-full rounded-md border border-gray-300 p-2 text-sm dark:border-gray-700 dark:bg-gray-800"
                        />
                        <p v-if="form.errors.original_language" class="mt-1 text-xs text-red-500">{{ form.errors.original_language }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Output Language *</label>
                        <input
                            v-model="form.output_language"
                            type="text"
                            required
                            class="mt-1 w-full rounded-md border border-gray-300 p-2 text-sm dark:border-gray-700 dark:bg-gray-800"
                        />
                        <p v-if="form.errors.output_language" class="mt-1 text-xs text-red-500">{{ form.errors.output_language }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Visual Style *</label>
                    <input
                        v-model="form.visual_style"
                        type="text"
                        required
                        class="mt-1 w-full rounded-md border border-gray-300 p-2 text-sm dark:border-gray-700 dark:bg-gray-800"
                    />
                    <p v-if="form.errors.visual_style" class="mt-1 text-xs text-red-500">{{ form.errors.visual_style }}</p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <textarea
                        v-model="form.description"
                        rows="2"
                        class="mt-1 w-full rounded-md border border-gray-300 p-2 text-sm dark:border-gray-700 dark:bg-gray-800"
                    ></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <button
                        type="button"
                        @click="showModal = false"
                        class="rounded-md border border-gray-300 px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 cursor-pointer"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-md bg-indigo-600 px-4 py-2 text-xs font-medium text-white shadow hover:bg-indigo-500 cursor-pointer flex items-center gap-2"
                    >
                        <span v-if="form.processing">Saving...</span>
                        <span v-else>Save Novel</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
