<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { dashboard } from '@/routes';
import { index as novelIndex } from '@/routes/novels';
import { index as reviewIndex } from '@/routes/reviews';
import { index as pipelineIndex } from '@/routes/pipeline';
import { index as costIndex } from '@/routes/costs';

const isOpen = ref(false);
const searchQuery = ref('');

const commands = [
    { name: 'Go to Dashboard', href: dashboard(), category: 'Navigation' },
    { name: 'View All Web Novels', href: novelIndex.url(), category: 'Navigation' },
    { name: 'Open Review Queue', href: reviewIndex.url(), category: 'Operations' },
    { name: 'Pipeline Monitor & Queues', href: pipelineIndex.url(), category: 'Operations' },
    { name: 'Cost Analytics & Spend', href: costIndex.url(), category: 'Analytics' },
];

const toggle = () => {
    isOpen.value = !isOpen.value;
};

const handleKeyDown = (e: KeyboardEvent) => {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        toggle();
    } else if (e.key === 'Escape' && isOpen.value) {
        isOpen.value = false;
    }
};

const navigate = (href: string) => {
    isOpen.value = false;
    router.visit(href);
};

onMounted(() => {
    window.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeyDown);
});
</script>

<template>
    <div>
        <!-- Trigger Badge in Header -->
        <button
            @click="toggle"
            class="flex items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-1.5 text-xs text-gray-500 hover:bg-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 cursor-pointer"
        >
            <span>Search or type command...</span>
            <kbd class="rounded border border-gray-300 bg-white px-1.5 py-0.5 text-[10px] font-semibold text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                ⌘K
            </kbd>
        </button>

        <!-- Command Palette Modal -->
        <div v-if="isOpen" class="fixed inset-0 z-50 flex items-start justify-center pt-20">
            <div @click="isOpen = false" class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>

            <div class="relative w-full max-w-lg overflow-hidden rounded-xl border border-gray-200 bg-white shadow-2xl dark:border-gray-800 dark:bg-gray-900">
                <div class="border-b border-gray-200 p-4 dark:border-gray-800">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Type a command or navigate..."
                        class="w-full bg-transparent text-sm text-gray-900 outline-none placeholder:text-gray-400 dark:text-gray-100"
                        autofocus
                    />
                </div>

                <div class="max-h-72 overflow-y-auto p-2">
                    <div
                        v-for="cmd in commands.filter(c => c.name.toLowerCase().includes(searchQuery.toLowerCase()))"
                        :key="cmd.href"
                        @click="navigate(cmd.href)"
                        class="flex cursor-pointer items-center justify-between rounded-lg px-3 py-2.5 text-xs hover:bg-indigo-50 dark:hover:bg-indigo-950/50"
                    >
                        <span class="font-semibold text-gray-800 dark:text-gray-200">{{ cmd.name }}</span>
                        <span class="rounded bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-500 dark:bg-gray-800 dark:text-gray-400">{{ cmd.category }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
