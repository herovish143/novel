<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: '/dashboard',
            },
        ],
    },
});
</script>

<template>
    <Head title="Dashboard - Web Novel Automation" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <!-- Hero Card -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-900 via-indigo-800 to-purple-900 p-8 text-white shadow-xl">
            <div class="relative z-10 max-w-2xl">
                <span class="rounded-full bg-indigo-500/30 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-indigo-200 backdrop-blur">
                    AI Automation System
                </span>
                <h1 class="mt-4 text-3xl font-extrabold tracking-tight sm:text-4xl">
                    Web Novel → Hindi Video Engine
                </h1>
                <p class="mt-3 text-sm text-indigo-100/90 leading-relaxed">
                    Convert web novel chapters into structured story memory, natural Hindi explanations, ElevenLabs voice narration, DALL-E 3 cinematic scene visuals, and FFmpeg 1080p videos.
                </p>
                <div class="mt-6 flex flex-wrap gap-4">
                    <Link
                        href="/novels"
                        class="rounded-xl bg-white px-6 py-3 text-sm font-bold text-indigo-950 shadow hover:bg-indigo-50 transition"
                    >
                        🎬 Manage Web Novels →
                    </Link>
                </div>
            </div>

            <!-- Abstract Decorative Shapes -->
            <div class="absolute -bottom-10 -right-10 h-64 w-64 rounded-full bg-purple-500/20 blur-3xl"></div>
            <div class="absolute -top-10 right-20 h-48 w-48 rounded-full bg-indigo-400/20 blur-2xl"></div>
        </div>

        <!-- Quick Navigation Feature Cards -->
        <div class="grid gap-6 md:grid-cols-3">
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-400">
                    📚
                </div>
                <h3 class="mt-4 text-base font-bold text-gray-900 dark:text-gray-100">1. Novel & Story Memory</h3>
                <p class="mt-2 text-xs text-gray-500">Track characters, aliases, relationships, locations, and abilities across hundreds of chapters.</p>
                <Link href="/novels" class="mt-4 inline-block text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">View Novels →</Link>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-950 dark:text-amber-400">
                    🎙️
                </div>
                <h3 class="mt-4 text-base font-bold text-gray-900 dark:text-gray-100">2. Hindi Voice & Subtitles</h3>
                <p class="mt-2 text-xs text-gray-500">ElevenLabs TTS integration with pronunciation dictionary override and SRT/ASS subtitle alignment.</p>
                <Link href="/novels" class="mt-4 inline-block text-xs font-bold text-amber-600 dark:text-amber-400 hover:underline">View Chapters →</Link>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400">
                    🎬
                </div>
                <h3 class="mt-4 text-base font-bold text-gray-900 dark:text-gray-100">3. Video & YouTube Drafts</h3>
                <p class="mt-2 text-xs text-gray-500">FFmpeg 1080p rendering, DALL-E 3 visual asset reuse engine, and automated YouTube unlisted drafts.</p>
                <Link href="/novels" class="mt-4 inline-block text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">View Dashboard →</Link>
            </div>
        </div>
    </div>
</template>
