<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { dashboard, login, register } from '@/routes';
</script>

<template>
    <Head title="Web Novel Hindi Automation Engine">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div class="flex min-h-screen flex-col bg-gray-950 font-sans text-gray-100 antialiased">
        <!-- Navigation Header -->
        <header class="border-b border-gray-800 bg-gray-900/50 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center justify-between p-4 px-6">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 text-xl font-bold text-white shadow-lg">
                        📖
                    </span>
                    <span class="text-lg font-bold tracking-tight text-white">NovelStream AI</span>
                </div>

                <nav class="flex items-center gap-4">
                    <Link
                        v-if="$page.props.auth.user"
                        href="/novels"
                        class="rounded-lg bg-indigo-600 px-5 py-2 text-xs font-bold text-white shadow hover:bg-indigo-500 transition"
                    >
                        Go to Dashboard →
                    </Link>
                    <template v-else>
                        <Link
                            :href="login()"
                            class="rounded-lg px-4 py-2 text-xs font-semibold text-gray-300 hover:text-white"
                        >
                            Log in
                        </Link>
                        <Link
                            :href="register()"
                            class="rounded-lg bg-indigo-600 px-5 py-2 text-xs font-bold text-white shadow hover:bg-indigo-500 transition"
                        >
                            Get Started
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <!-- Main Hero Section -->
        <main class="flex-1">
            <div class="relative overflow-hidden px-6 py-24 sm:py-32 lg:px-8">
                <div class="mx-auto max-w-4xl text-center">
                    <span class="rounded-full bg-indigo-500/10 px-4 py-1.5 text-xs font-bold text-indigo-400 ring-1 ring-inset ring-indigo-500/20">
                        Production Automation Pipeline
                    </span>
                    <h1 class="mt-8 text-4xl font-extrabold tracking-tight text-white sm:text-6xl">
                        Web Novel → Hindi Video Engine
                    </h1>
                    <p class="mt-6 text-base text-gray-400 sm:text-lg leading-relaxed max-w-2xl mx-auto">
                        Automate web novel chapter analysis, persistent story memory, Hindi script generation, ElevenLabs voice narration, DALL-E 3 visual scenes, and 1080p FFmpeg video rendering.
                    </p>

                    <div class="mt-10 flex items-center justify-center gap-x-6">
                        <Link
                            v-if="$page.props.auth.user"
                            href="/novels"
                            class="rounded-xl bg-indigo-600 px-8 py-3.5 text-sm font-bold text-white shadow-lg hover:bg-indigo-500 transition"
                        >
                            Open Web Novels App →
                        </Link>
                        <Link
                            v-else
                            :href="register()"
                            class="rounded-xl bg-indigo-600 px-8 py-3.5 text-sm font-bold text-white shadow-lg hover:bg-indigo-500 transition"
                        >
                            Start Automated Production →
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Features Grid -->
            <div class="border-t border-gray-800 bg-gray-900/30 py-16">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="grid gap-8 md:grid-cols-3">
                        <div class="rounded-2xl border border-gray-800 bg-gray-900/50 p-6">
                            <span class="text-3xl">🧠</span>
                            <h3 class="mt-4 text-base font-bold text-white">Story Memory Architecture</h3>
                            <p class="mt-2 text-xs text-gray-400 leading-relaxed">Tracks character aliases, abilities, relationships, locations, and world-building continuity across thousands of chapters.</p>
                        </div>

                        <div class="rounded-2xl border border-gray-800 bg-gray-900/50 p-6">
                            <span class="text-3xl">🎙️</span>
                            <h3 class="mt-4 text-base font-bold text-white">Hindi Voice & Subtitles</h3>
                            <p class="mt-2 text-xs text-gray-400 leading-relaxed">ElevenLabs TTS integration with pronunciation dictionary overrides and ASS styled burned subtitles.</p>
                        </div>

                        <div class="rounded-2xl border border-gray-800 bg-gray-900/50 p-6">
                            <span class="text-3xl">🎬</span>
                            <h3 class="mt-4 text-base font-bold text-white">1080p FFmpeg Video</h3>
                            <p class="mt-2 text-xs text-gray-400 leading-relaxed">DALL-E 3 visual asset reuse engine ($0 matching cost), camera motion zoom/pan, and one-click YouTube unlisted drafts.</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
