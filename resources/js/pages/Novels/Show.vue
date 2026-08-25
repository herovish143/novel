<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { store as chapterStoreRoute, show as chapterShowRoute } from '@/routes/chapters';

type DocumentImport = {
    id: number;
    original_filename: string;
    page_count: number;
    status: string;
    extraction_method: string;
    detected_chapters_count: number;
    approved_chapters_count: number;
    imported_chapters_count: number;
    average_confidence: number;
    created_at: string | null;
};

type Character = {
    id: number;
    name: string;
    canonical_name: string;
    importance: string;
    gender: string | null;
    physical_description: string | null;
    personality: string | null;
    aliases: { id: number; alias: string }[];
};

type Chapter = {
    id: number;
    chapter_number: number;
    title: string;
    status: string;
    imported_at: string;
    latest_script?: { id: number; status: string } | null;
};

type Novel = {
    id: number;
    title: string;
    slug: string;
    original_language: string;
    output_language: string;
    visual_style: string;
    narration_style: string;
    description: string | null;
};

type PaginatedChapters = {
    data: Chapter[];
};

type PaginatedCharacters = {
    data: Character[];
};

const props = defineProps<{
    novel: Novel;
    chapters: Chapter[] | PaginatedChapters;
    characters: Character[] | PaginatedCharacters;
    documentImports?: DocumentImport[];
}>();

const chapterList = ref<Chapter[]>(Array.isArray(props.chapters) ? props.chapters : props.chapters?.data || []);
const characterList = ref<Character[]>(Array.isArray(props.characters) ? props.characters : props.characters?.data || []);
const importsList = ref<DocumentImport[]>(props.documentImports || []);

const activeTab = ref<'chapters' | 'characters' | 'imports'>('chapters');
const showImportModal = ref(false);
const showPdfModal = ref(false);

const importForm = useForm({
    chapter_number: chapterList.value.length + 1,
    title: '',
    source_text: '',
    source_url: '',
});

const pdfForm = useForm({
    pdf_file: null as File | null,
});

const submitImport = () => {
    importForm.post(chapterStoreRoute.url(props.novel.id), {
        onSuccess: () => {
            showImportModal.value = false;
            importForm.reset();
            importForm.chapter_number = chapterList.value.length + 2;
        },
    });
};

const submitPdf = () => {
    pdfForm.post(`/novels/${props.novel.id}/pdf/upload`, {
        onSuccess: () => {
            showPdfModal.value = false;
            pdfForm.reset();
        },
    });
};

const handlePdfFileSelect = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        pdfForm.pdf_file = target.files[0];
    }
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

            <div class="flex flex-wrap items-center gap-3">
                <button
                    @click="showPdfModal = true"
                    class="rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-2 text-xs font-bold text-indigo-700 shadow-sm hover:bg-indigo-100 transition cursor-pointer dark:border-indigo-900 dark:bg-indigo-950 dark:text-indigo-300"
                >
                    📄 Upload PDF Book & Review Candidates
                </button>

                <button
                    @click="showImportModal = true"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow hover:bg-indigo-500 transition cursor-pointer"
                >
                    + Import Single Chapter
                </button>
            </div>
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
                        'border-b-2 py-2 text-sm transition cursor-pointer'
                    ]"
                >
                    📖 Chapters ({{ chapterList.length }})
                </button>
                <button
                    @click="activeTab = 'imports'"
                    :class="[
                        activeTab === 'imports'
                            ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 font-bold'
                            : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 font-medium',
                        'border-b-2 py-2 text-sm transition cursor-pointer'
                    ]"
                >
                    📄 PDF Imports ({{ importsList.length }})
                </button>
                <button
                    @click="activeTab = 'characters'"
                    :class="[
                        activeTab === 'characters'
                            ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 font-bold'
                            : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 font-medium',
                        'border-b-2 py-2 text-sm transition cursor-pointer'
                    ]"
                >
                    👤 Persistent Characters ({{ characterList.length }})
                </button>
            </nav>
        </div>

        <!-- Chapters List View -->
        <div v-if="activeTab === 'chapters'" class="space-y-4">
            <div v-if="chapterList.length > 0" class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
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
                        <tr v-for="ch in chapterList" :key="ch.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30">
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
                                    :href="chapterShowRoute.url(ch.id)"
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
                No chapters imported yet. Click "📄 Upload PDF Book & Review Candidates" or "+ Import Single Chapter" to get started.
            </div>
        </div>

        <!-- PDF Imports List View -->
        <div v-if="activeTab === 'imports'" class="space-y-4">
            <div v-if="importsList.length > 0" class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-500 uppercase dark:bg-gray-800/50 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3 font-semibold">PDF File</th>
                            <th class="px-6 py-3 font-semibold">Pages</th>
                            <th class="px-6 py-3 font-semibold">Detected</th>
                            <th class="px-6 py-3 font-semibold">Approved</th>
                            <th class="px-6 py-3 font-semibold">Imported</th>
                            <th class="px-6 py-3 font-semibold">Avg. Confidence</th>
                            <th class="px-6 py-3 font-semibold">Status</th>
                            <th class="px-6 py-3 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        <tr v-for="imp in importsList" :key="imp.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30">
                            <td class="px-6 py-4 font-semibold text-gray-900 dark:text-gray-100">
                                {{ imp.original_filename }}
                            </td>
                            <td class="px-6 py-4 font-mono text-gray-500">
                                {{ imp.page_count }}
                            </td>
                            <td class="px-6 py-4 font-mono text-gray-500">
                                {{ imp.detected_chapters_count }}
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                {{ imp.approved_chapters_count }}
                            </td>
                            <td class="px-6 py-4 font-mono text-indigo-600 dark:text-indigo-400">
                                {{ imp.imported_chapters_count }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="rounded bg-indigo-50 px-2 py-0.5 text-[10px] font-bold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                                    {{ imp.average_confidence }}%
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="rounded bg-gray-100 px-2 py-1 text-[10px] font-bold text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                    {{ imp.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <Link
                                    :href="`/document-imports/${imp.id}`"
                                    class="font-bold text-indigo-600 hover:underline dark:text-indigo-400"
                                >
                                    Review Candidates →
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else class="rounded-xl border border-dashed border-gray-300 p-8 text-center text-sm text-gray-500 dark:border-gray-800">
                No PDF e-books uploaded for candidate review yet. Click "📄 Upload PDF Book & Review Candidates" above.
            </div>
        </div>

        <!-- Characters List View -->
        <div v-if="activeTab === 'characters'" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="char in characterList"
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

                <div v-if="char.aliases && char.aliases.length > 0" class="mt-3 flex flex-wrap gap-1">
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

    <!-- PDF Extractor Modal -->
    <div v-if="showPdfModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm">
        <div class="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl dark:bg-gray-900 dark:text-gray-100">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3 dark:border-gray-800">
                <h3 class="text-lg font-bold">📄 PDF Book Chapter Detection</h3>
                <button @click="showPdfModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">✕</button>
            </div>

            <form @submit.prevent="submitPdf" class="mt-4 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Select Licensed PDF Book (.pdf)</label>
                    <input
                        type="file"
                        accept=".pdf,application/pdf"
                        required
                        @change="handlePdfFileSelect"
                        class="w-full text-xs text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-xs file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-950 dark:file:text-indigo-300"
                    />
                    <p class="mt-2 text-[11px] text-gray-400">
                        Uploads the PDF, extracts chapter candidates with confidence scores, and opens the candidate review workspace.
                    </p>
                    <p v-if="pdfForm.errors.pdf_file" class="mt-1 text-xs text-red-500">{{ pdfForm.errors.pdf_file }}</p>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <button
                        type="button"
                        @click="showPdfModal = false"
                        class="rounded-md border border-gray-300 px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 cursor-pointer"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="pdfForm.processing || !pdfForm.pdf_file"
                        class="rounded-md bg-indigo-600 px-4 py-2 text-xs font-medium text-white shadow hover:bg-indigo-500 cursor-pointer flex items-center gap-2 disabled:opacity-50"
                    >
                        <span v-if="pdfForm.processing">Extracting Candidates...</span>
                        <span v-else>Upload & Detect Candidates</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Import Chapter Modal -->
    <div v-if="showImportModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm">
        <div class="w-full max-w-xl rounded-xl bg-white p-6 shadow-xl dark:bg-gray-900 dark:text-gray-100">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3 dark:border-gray-800">
                <h3 class="text-lg font-bold">Import Source Chapter</h3>
                <button @click="showImportModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">✕</button>
            </div>

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
                        class="rounded-md border border-gray-300 px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 cursor-pointer"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="importForm.processing"
                        class="rounded-md bg-indigo-600 px-4 py-2 text-xs font-medium text-white shadow hover:bg-indigo-500 cursor-pointer"
                    >
                        Import Chapter
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
