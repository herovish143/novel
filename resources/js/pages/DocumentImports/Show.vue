<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm, router, Link } from '@inertiajs/vue3';

type ChapterCandidate = {
    id: number;
    document_import_id: number;
    sequence: number;
    detected_number: number | null;
    resolved_chapter_number: number | null;
    detected_title: string | null;
    title: string | null;
    start_page: number;
    end_page: number;
    word_count: number;
    confidence_score: number;
    confidence_level: string;
    status: string;
    source_text: string | null;
};

type DocumentImport = {
    id: number;
    novel_id: number;
    original_filename: string;
    storage_path: string;
    file_size: number;
    sha256: string;
    page_count: number;
    status: string;
    extraction_method: string;
    detected_chapters_count: number;
    approved_chapters_count: number;
    imported_chapters_count: number;
    skipped_chapters_count: number;
    average_confidence: number;
    created_at: string | null;
};

type Novel = {
    id: number;
    title: string;
};

const props = defineProps<{
    documentImport: DocumentImport;
    novel: Novel;
    candidates: ChapterCandidate[];
}>();

const activeFilter = ref<'ALL' | 'REVIEW_REQUIRED' | 'APPROVED' | 'SKIPPED'>('ALL');
const previewCandidate = ref<ChapterCandidate | null>(null);
const editingCandidate = ref<ChapterCandidate | null>(null);
const splittingCandidate = ref<ChapterCandidate | null>(null);

const filteredCandidates = computed(() => {
    if (activeFilter.value === 'ALL') return props.candidates;
    return props.candidates.filter((c) => c.status === activeFilter.value);
});

const editForm = useForm({
    title: '',
    resolved_chapter_number: 1,
});

const splitForm = useForm({
    split_page: 1,
});

const isImporting = ref(false);

const openEditModal = (c: ChapterCandidate) => {
    editingCandidate.value = c;
    editForm.title = c.title || `Chapter ${c.sequence}`;
    editForm.resolved_chapter_number = c.resolved_chapter_number || c.sequence;
};

const submitEdit = () => {
    if (!editingCandidate.value) return;
    editForm.patch(`/chapter-candidates/${editingCandidate.value.id}`, {
        onSuccess: () => {
            editingCandidate.value = null;
        },
    });
};

const openSplitModal = (c: ChapterCandidate) => {
    splittingCandidate.value = c;
    splitForm.split_page = Math.floor((c.start_page + c.end_page) / 2);
};

const submitSplit = () => {
    if (!splittingCandidate.value) return;
    splitForm.post(`/chapter-candidates/${splittingCandidate.value.id}/split`, {
        onSuccess: () => {
            splittingCandidate.value = null;
        },
    });
};

const approveCandidate = (candidateId: number) => {
    router.post(`/chapter-candidates/${candidateId}/approve`);
};

const skipCandidate = (candidateId: number) => {
    router.post(`/chapter-candidates/${candidateId}/skip`);
};

const mergeNext = (candidateId: number) => {
    router.post(`/chapter-candidates/${candidateId}/merge-next`);
};

const approveAllHighConfidence = () => {
    props.candidates.forEach((c) => {
        if (c.confidence_score >= 85 && c.status !== 'APPROVED') {
            approveCandidate(c.id);
        }
    });
};

const importApproved = () => {
    isImporting.value = true;
    router.post(`/document-imports/${props.documentImport.id}/import`, {}, {
        onFinish: () => {
            isImporting.value = false;
        },
    });
};
</script>

<template>
    <Head :title="`PDF Review - ${documentImport.original_filename}`" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <!-- Header Card -->
        <div class="flex flex-col justify-between gap-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm md:flex-row md:items-center dark:border-gray-800 dark:bg-gray-900">
            <div>
                <div class="flex items-center gap-3">
                    <Link :href="`/novels/${novel.id}`" class="text-xs font-semibold text-indigo-600 hover:underline dark:text-indigo-400">
                        ← {{ novel.title }}
                    </Link>
                    <span class="rounded bg-indigo-50 px-2 py-0.5 text-[10px] font-bold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                        {{ documentImport.status }}
                    </span>
                </div>
                <h1 class="mt-1 text-xl font-bold text-gray-900 dark:text-gray-100">
                    PDF Extraction Review: {{ documentImport.original_filename }}
                </h1>
                <p class="mt-1 text-xs text-gray-400 font-mono">
                    SHA-256: {{ documentImport.sha256.substring(0, 16) }}... • {{ documentImport.page_count }} Pages • Avg. Confidence: {{ documentImport.average_confidence }}%
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button
                    @click="approveAllHighConfidence"
                    class="rounded-lg border border-emerald-300 bg-emerald-50 px-3.5 py-2 text-xs font-semibold text-emerald-800 hover:bg-emerald-100 cursor-pointer dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-300"
                >
                    ⚡ Approve High Confidence (≥85%)
                </button>

                <button
                    @click="importApproved"
                    :disabled="isImporting || documentImport.approved_chapters_count === 0"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-bold text-white shadow hover:bg-indigo-500 disabled:opacity-50 cursor-pointer"
                >
                    {{ isImporting ? 'Importing...' : `🚀 Import Approved (${documentImport.approved_chapters_count})` }}
                </button>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="border-b border-gray-200 dark:border-gray-800 flex gap-4 text-xs font-semibold">
            <button
                @click="activeFilter = 'ALL'"
                :class="activeFilter === 'ALL' ? 'border-b-2 border-indigo-600 text-indigo-600 font-bold pb-2' : 'text-gray-500 pb-2 cursor-pointer'"
            >
                All Candidates ({{ candidates.length }})
            </button>
            <button
                @click="activeFilter = 'REVIEW_REQUIRED'"
                :class="activeFilter === 'REVIEW_REQUIRED' ? 'border-b-2 border-amber-600 text-amber-600 font-bold pb-2' : 'text-gray-500 pb-2 cursor-pointer'"
            >
                Needs Review
            </button>
            <button
                @click="activeFilter = 'APPROVED'"
                :class="activeFilter === 'APPROVED' ? 'border-b-2 border-emerald-600 text-emerald-600 font-bold pb-2' : 'text-gray-500 pb-2 cursor-pointer'"
            >
                Approved ({{ documentImport.approved_chapters_count }})
            </button>
            <button
                @click="activeFilter = 'SKIPPED'"
                :class="activeFilter === 'SKIPPED' ? 'border-b-2 border-gray-600 text-gray-400 font-bold pb-2' : 'text-gray-500 pb-2 cursor-pointer'"
            >
                Skipped ({{ documentImport.skipped_chapters_count }})
            </button>
        </div>

        <!-- Candidate Table -->
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-500 uppercase dark:bg-gray-800/50 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Seq</th>
                        <th class="px-4 py-3 font-semibold">Chapter Title</th>
                        <th class="px-4 py-3 font-semibold">Pages</th>
                        <th class="px-4 py-3 font-semibold">Words</th>
                        <th class="px-4 py-3 font-semibold">Confidence</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    <tr v-for="c in filteredCandidates" :key="c.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30">
                        <td class="px-4 py-3 font-mono font-bold text-indigo-600 dark:text-indigo-400">
                            #{{ c.resolved_chapter_number || c.sequence }}
                        </td>
                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100">
                            {{ c.title }}
                        </td>
                        <td class="px-4 py-3 font-mono text-gray-500">
                            p. {{ c.start_page }} – {{ c.end_page }}
                        </td>
                        <td class="px-4 py-3 font-mono text-gray-500">
                            {{ c.word_count.toLocaleString() }}
                        </td>
                        <td class="px-4 py-3">
                            <span
                                class="rounded px-2 py-0.5 text-[10px] font-bold"
                                :class="{
                                    'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300': c.confidence_score >= 85,
                                    'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300': c.confidence_score >= 60 && c.confidence_score < 85,
                                    'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300': c.confidence_score < 60,
                                }"
                            >
                                {{ c.confidence_score }}% ({{ c.confidence_level }})
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span
                                class="rounded px-2 py-0.5 text-[10px] font-bold"
                                :class="{
                                    'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300': c.status === 'APPROVED' || c.status === 'IMPORTED',
                                    'bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300': c.status === 'REVIEW_REQUIRED',
                                    'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400': c.status === 'SKIPPED',
                                }"
                            >
                                {{ c.status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <button @click="previewCandidate = c" class="text-indigo-600 hover:underline font-semibold cursor-pointer">
                                Preview
                            </button>
                            <button @click="openEditModal(c)" class="text-gray-600 hover:underline font-medium cursor-pointer">
                                Edit
                            </button>
                            <button @click="openSplitModal(c)" class="text-amber-600 hover:underline font-medium cursor-pointer">
                                Split
                            </button>
                            <button @click="mergeNext(c.id)" class="text-purple-600 hover:underline font-medium cursor-pointer">
                                Merge
                            </button>
                            <button @click="skipCandidate(c.id)" class="text-gray-400 hover:underline cursor-pointer">
                                Skip
                            </button>
                            <button
                                v-if="c.status !== 'APPROVED'"
                                @click="approveCandidate(c.id)"
                                class="rounded bg-emerald-600 px-2 py-1 text-[10px] font-bold text-white shadow hover:bg-emerald-500 cursor-pointer"
                            >
                                Approve
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Modal -->
    <div v-if="editingCandidate" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm">
        <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl dark:bg-gray-900 dark:text-gray-100">
            <h3 class="text-base font-bold">Edit Candidate Metadata</h3>
            <form @submit.prevent="submitEdit" class="mt-4 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Chapter #</label>
                    <input v-model="editForm.resolved_chapter_number" type="number" required class="mt-1 w-full rounded-md border p-2 text-xs dark:bg-gray-800" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Title</label>
                    <input v-model="editForm.title" type="text" required class="mt-1 w-full rounded-md border p-2 text-xs dark:bg-gray-800" />
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="editingCandidate = null" class="rounded border px-3 py-1.5 text-xs">Cancel</button>
                    <button type="submit" class="rounded bg-indigo-600 px-4 py-1.5 text-xs font-bold text-white">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Split Modal -->
    <div v-if="splittingCandidate" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm">
        <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl dark:bg-gray-900 dark:text-gray-100">
            <h3 class="text-base font-bold">Split Candidate Chapter</h3>
            <p class="text-xs text-gray-400 mt-1">Specify page number where second chapter starts (Pages {{ splittingCandidate.start_page }} – {{ splittingCandidate.end_page }})</p>
            <form @submit.prevent="submitSplit" class="mt-4 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Start Page for Part 2</label>
                    <input v-model="splitForm.split_page" type="number" :min="splittingCandidate.start_page + 1" :max="splittingCandidate.end_page" required class="mt-1 w-full rounded-md border p-2 text-xs dark:bg-gray-800" />
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="splittingCandidate = null" class="rounded border px-3 py-1.5 text-xs">Cancel</button>
                    <button type="submit" class="rounded bg-amber-600 px-4 py-1.5 text-xs font-bold text-white">Split Chapter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Preview Drawer -->
    <div v-if="previewCandidate" class="fixed inset-y-0 right-0 z-50 w-full max-w-lg bg-white p-6 shadow-2xl border-l dark:bg-gray-900 dark:border-gray-800 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between border-b pb-3 dark:border-gray-800">
                <div>
                    <h3 class="text-base font-bold">{{ previewCandidate.title }}</h3>
                    <p class="text-xs text-gray-400 font-mono">p. {{ previewCandidate.start_page }} – {{ previewCandidate.end_page }} • {{ previewCandidate.word_count }} words</p>
                </div>
                <button @click="previewCandidate = null" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>
            <div class="mt-4 max-h-[70vh] overflow-y-auto rounded bg-gray-50 p-4 font-mono text-xs text-gray-700 dark:bg-gray-950 dark:text-gray-300 whitespace-pre-wrap leading-relaxed">
                {{ previewCandidate.source_text || 'No preview text available.' }}
            </div>
        </div>

        <div class="border-t pt-4 flex justify-end gap-2 dark:border-gray-800">
            <button @click="previewCandidate = null" class="rounded border px-4 py-2 text-xs font-semibold">Close Preview</button>
        </div>
    </div>
</template>
