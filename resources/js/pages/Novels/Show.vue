<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

interface Character {
    id: number;
    name: string;
    canonical_name: string;
    importance: string;
    gender: string | null;
    physical_description: string | null;
    personality: string | null;
    aliases: { id: number; alias: string }[];
}

interface Chapter {
    id: number;
    chapter_number: number;
    title: string;
    status: string;
    imported_at: string;
    latest_script?: { id: number; status: string } | null;
}

interface Novel {
    id: number;
    title: string;
    slug: string;
    original_language: string;
    output_language: string;
    visual_style: string;
    narration_style: string;
    description: string | null;
}

const props = defineProps<{
    novel: Novel;
    chapters: Chapter[];
    characters: Character[];
}>();

const activeTab = ref<'chapters' | 'characters'>('chapters');
const showImportModal = ref(false);

const importForm = useForm({
    chapter_number: props.chapters.length + 1,
    title: '',
    source_text: '',
    source_url: '',
});

const submitImport = () => {
    importForm.post(`/novels/${props.novel.id}/chapters`, {
        onSuccess: () => {
            showImportModal.value = false;
            importForm.reset();
            importForm.chapter_number = props.chapters.length + 2;
        },
    });
};
</script>

<template>
    <Head :title="`${novel.title} - Management`" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <!-- Header -->
        <div class="flex flex-col justify-between gap-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm md:flex-row md:items-center dark:border-gray-800 dark:bg-gray-900">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ novel.title }}</h1>
                    <span class="rounded-full bg-indigo-50 px-3 py-0.5 text-xs font-bold text-indigo-600 dark:bg-indigo-950 dark:text-indigo-300">
                        {{ novel.original_language.toUpperCase() }} → {{ novel.output_language.toUpperCase() }}
                    </span>
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ novel.description || 'No description added.' }} • Style: {{ novel.visual_style }}
                </p>
            </div>

            <button
                @click="showImportModal = true"
                class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500 transition"
            >
                + Import New Chapter
            </button>
        </div>

        <!-- Navigation Tabs -->
        <div class="border-b border-gray-200 dark:border-gray-800">
            <nav class="-mb-px flex gap-6">
                <button
                    @click="activeTab = 'chapters'"
                    :class="[
                        activeTab === 'chapters'
                            ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 font-bold'
                            : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 font-medium',
                        'border-b-2 py-2 text-sm transition'
                    ]"
                >
                    📖 Chapters ({{ chapters.length }})
                </button>
                <button
                    @click="activeTab = 'characters'"
                    :class="[
                        activeTab === 'characters'
                            ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 font-bold'
                            : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 font-medium',
                        'border-b-2 py-2 text-sm transition'
                    ]"
                >
                    👤 Persistent Characters ({{ characters.length }})
                </button>
            </nav>
        </div>

        <!-- Chapters List View -->
        <div v-if="activeTab === 'chapters'" class="space-y-4">
            <div v-if="chapters.length > 0" class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-500 uppercase dark:bg-gray-800/50 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3 font-semibold">Ch #</th>
                            <th class="px-6 py-3 font-semibold">Title</th>
                            <th class="px-6 py-3 font-semibold">Status</th>
                            <th class="px-6 py-3 font-semibold">Script</th>
                            <th class="px-6 py-3 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        <tr v-for="ch in chapters" :key="ch.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30">
                            <td class="px-6 py-4 font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                #{{ ch.chapter_number }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100">
                                {{ ch.title }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="rounded bg-indigo-50 px-2 py-1 text-[11px] font-semibold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                                    {{ ch.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ ch.latest_script ? ch.latest_script.status : 'Not Created' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <Link
                                    :href="`/chapters/${ch.id}`"
                                    class="font-bold text-indigo-600 hover:underline dark:text-indigo-400"
                                >
                                    Open Chapter Pipeline →
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else class="rounded-xl border border-dashed border-gray-300 p-8 text-center text-sm text-gray-500 dark:border-gray-800">
                No chapters imported yet. Click "+ Import New Chapter" to paste source text.
            </div>
        </div>

        <!-- Characters List View -->
        <div v-if="activeTab === 'characters'" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="char in characters"
                :key="char.id"
                class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900"
            >
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">
                        {{ char.canonical_name }}
                    </h3>
                    <span class="rounded bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-700 dark:bg-amber-950 dark:text-amber-300">
                        {{ char.importance }}
                    </span>
                </div>
                <p class="mt-1 text-xs text-gray-500 italic">
                    Original Name: {{ char.name }}
                </p>
                <p class="mt-2 text-xs text-gray-700 dark:text-gray-300 line-clamp-3">
                    {{ char.physical_description || char.personality || 'No description recorded.' }}
                </p>

                <div v-if="char.aliases.length > 0" class="mt-3 flex flex-wrap gap-1">
                    <span
                        v-for="alias in char.aliases"
                        :key="alias.id"
                        class="rounded bg-gray-100 px-1.5 py-0.5 text-[10px] text-gray-600 dark:bg-gray-800 dark:text-gray-300"
                    >
                        @{{ alias.alias }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Chapter Modal -->
    <div v-if="showImportModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-xl rounded-xl bg-white p-6 shadow-xl dark:bg-gray-900 dark:text-gray-100">
            <h3 class="text-lg font-bold">Import Source Chapter</h3>
            <form @submit.prevent="submitImport" class="mt-4 space-y-4">
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Chapter #</label>
                        <input
                            v-model="importForm.chapter_number"
                            type="number"
                            required
                            class="mt-1 w-full rounded-md border border-gray-300 p-2 text-sm dark:border-gray-700 dark:bg-gray-800"
                        />
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Chapter Title</label>
                        <input
                            v-model="importForm.title"
                            type="text"
                            required
                            class="mt-1 w-full rounded-md border border-gray-300 p-2 text-sm dark:border-gray-700 dark:bg-gray-800"
                            placeholder="The Awakening"
                        />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Raw Chapter Text (Source)</label>
                    <textarea
                        v-model="importForm.source_text"
                        rows="8"
                        required
                        class="mt-1 w-full rounded-md border border-gray-300 p-2 text-xs font-mono dark:border-gray-700 dark:bg-gray-800"
                        placeholder="Paste raw English chapter text here..."
                    ></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button
                        type="button"
                        @click="showImportModal = false"
                        class="rounded-md border border-gray-300 px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="importForm.processing"
                        class="rounded-md bg-indigo-600 px-4 py-2 text-xs font-medium text-white shadow hover:bg-indigo-500"
                    >
                        Import Chapter
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
