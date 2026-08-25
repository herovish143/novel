<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';

type Segment = {
    id: number;
    sequence: number;
    type: string;
    text: string;
    status: string;
};

type Script = {
    id: number;
    chapter_id: number;
    version: number;
    language: string;
    status: string;
    hook: string;
    previous_recap: string;
    main_narration: string;
    analysis: string;
    ending_hook: string;
    full_script: string;
    word_count: number;
    character_count: number;
    segments?: Segment[];
};

type ChapterSummary = {
    summary: string;
    important_reveals: string[] | null;
};

type StoryEvent = {
    id: number;
    sequence: number;
    description: string;
};

type Chapter = {
    id: number;
    chapter_number: number;
    title: string;
    novel_id: number;
};

const props = defineProps<{
    script: Script;
    chapter: Chapter;
    summary: ChapterSummary | null;
    events: StoryEvent[];
}>();

const isVerifying = ref(false);
const verificationResult = ref<{ valid: boolean; issues: { section: string; severity: string; problem: string }[] } | null>(null);

const form = useForm({
    hook: props.script.hook || '',
    previous_recap: props.script.previous_recap || '',
    main_narration: props.script.main_narration || '',
    analysis: props.script.analysis || '',
    ending_hook: props.script.ending_hook || '',
});

const saveDraft = () => {
    form.put(`/scripts/${props.script.id}`, {
        preserveScroll: true,
    });
};

const runVerification = () => {
    isVerifying.value = true;
    router.post(`/scripts/${props.script.id}/verify`, {}, {
        onSuccess: (page) => {
            const flash = (page.props as any).flash;
            if (flash?.verification) {
                verificationResult.value = flash.verification;
            }
        },
        onFinish: () => {
            isVerifying.value = false;
        },
    });
};

const approveScript = () => {
    router.post(`/scripts/${props.script.id}/approve`);
};
</script>

<template>
    <Head :title="`Script Review - Ch. ${chapter.chapter_number}`" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <!-- Header Toolbar -->
        <div class="flex flex-col justify-between gap-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm md:flex-row md:items-center dark:border-gray-800 dark:bg-gray-900">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                        Script Review: Chapter {{ chapter.chapter_number }}
                    </h1>
                    <span class="rounded-full bg-indigo-50 px-3 py-0.5 text-xs font-bold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                        v{{ script.version }} • {{ script.status }}
                    </span>
                </div>
                <p class="mt-1 text-xs text-gray-400">
                    Side-by-side verification of extracted chapter facts vs Hindi explanation script.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button
                    @click="saveDraft"
                    :disabled="form.processing"
                    class="rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-semibold text-gray-700 shadow-sm hover:bg-gray-50 disabled:opacity-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 cursor-pointer"
                >
                    💾 Save Changes
                </button>

                <button
                    @click="runVerification"
                    :disabled="isVerifying"
                    class="rounded-lg bg-amber-600 px-3.5 py-2 text-xs font-semibold text-white shadow hover:bg-amber-500 disabled:opacity-50 cursor-pointer"
                >
                    {{ isVerifying ? 'Checking...' : '🔍 Fact-Check Verifier' }}
                </button>

                <button
                    @click="approveScript"
                    :disabled="script.status === 'APPROVED'"
                    class="rounded-lg bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow hover:bg-emerald-500 disabled:opacity-50 cursor-pointer"
                >
                    {{ script.status === 'APPROVED' ? '✓ Approved' : '✓ Approve & Unlock Audio/Visuals' }}
                </button>
            </div>
        </div>

        <!-- Verification Banner (if run) -->
        <div v-if="verificationResult" class="rounded-xl border p-4 shadow-sm" :class="verificationResult.valid ? 'border-emerald-200 bg-emerald-50 dark:border-emerald-900/50 dark:bg-emerald-950/20' : 'border-rose-200 bg-rose-50 dark:border-rose-900/50 dark:bg-rose-950/20'">
            <h3 class="text-sm font-bold" :class="verificationResult.valid ? 'text-emerald-900 dark:text-emerald-300' : 'text-rose-900 dark:text-rose-300'">
                {{ verificationResult.valid ? '✓ Fact-Check Passed: No contradictions found.' : '⚠️ Verification Warnings Identified' }}
            </h3>
            <ul v-if="verificationResult.issues.length > 0" class="mt-2 space-y-1 text-xs">
                <li v-for="(iss, idx) in verificationResult.issues" :key="idx" class="text-rose-800 dark:text-rose-300">
                    • [{{ iss.severity.toUpperCase() }}] Section '{{ iss.section }}': {{ iss.problem }}
                </li>
            </ul>
        </div>

        <!-- Side-by-Side Grid Layout -->
        <div class="grid gap-6 lg:grid-cols-2">
            <!-- Left Column: Source Facts & Memory -->
            <div class="space-y-6">
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">
                        1. Extracted Chapter Facts
                    </h2>
                    <p v-if="summary" class="mt-3 text-xs text-gray-700 dark:text-gray-300 leading-relaxed">
                        {{ summary.summary }}
                    </p>
                </div>

                <div v-if="events.length > 0" class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <h2 class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-3">2. Sequential Story Events</h2>
                    <div class="space-y-2">
                        <div v-for="ev in events" :key="ev.id" class="rounded border border-gray-100 bg-gray-50 p-2 text-xs dark:border-gray-800 dark:bg-gray-800/50">
                            <span class="font-bold text-indigo-600">#{{ ev.sequence }}</span>: {{ ev.description }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Hindi Script Editor -->
            <div class="space-y-6">
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900 space-y-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">
                        Hindi Narration Script Editor
                    </h2>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">Hook (एपिसोड शुरुआत)</label>
                        <textarea
                            v-model="form.hook"
                            rows="2"
                            class="mt-1 w-full rounded-md border border-gray-300 p-2.5 text-xs dark:border-gray-700 dark:bg-gray-800"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">Main Story Explanation (मुख्य कहानी)</label>
                        <textarea
                            v-model="form.main_narration"
                            rows="10"
                            class="mt-1 w-full rounded-md border border-gray-300 p-2.5 text-xs dark:border-gray-700 dark:bg-gray-800"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">Analysis & Commentary (व्याख्या और विश्लेषण)</label>
                        <textarea
                            v-model="form.analysis"
                            rows="4"
                            class="mt-1 w-full rounded-md border border-gray-300 p-2.5 text-xs dark:border-gray-700 dark:bg-gray-800"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">Ending Hook (अगला भाग सस्पेंस)</label>
                        <textarea
                            v-model="form.ending_hook"
                            rows="2"
                            class="mt-1 w-full rounded-md border border-gray-300 p-2.5 text-xs dark:border-gray-700 dark:bg-gray-800"
                        ></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
