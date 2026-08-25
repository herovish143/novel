<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('chapters', function (Blueprint $table): void {
            $table->index(['novel_id', 'status']);
        });

        Schema::table('production_runs', function (Blueprint $table): void {
            $table->index(['status', 'current_stage']);
            $table->index(['chapter_id', 'status']);
        });

        Schema::table('ai_usage', function (Blueprint $table): void {
            $table->index(['service', 'provider']);
            $table->index(['created_at']);
        });

        Schema::table('chapter_facts', function (Blueprint $table): void {
            $table->index(['chapter_id', 'fact_type']);
        });

        Schema::table('media_assets', function (Blueprint $table): void {
            $table->index(['chapter_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chapters', function (Blueprint $table): void {
            $table->dropIndex(['novel_id', 'status']);
        });

        Schema::table('production_runs', function (Blueprint $table): void {
            $table->dropIndex(['status', 'current_stage']);
            $table->dropIndex(['chapter_id', 'status']);
        });

        Schema::table('ai_usage', function (Blueprint $table): void {
            $table->dropIndex(['service', 'provider']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('chapter_facts', function (Blueprint $table): void {
            $table->dropIndex(['chapter_id', 'fact_type']);
        });

        Schema::table('media_assets', function (Blueprint $table): void {
            $table->dropIndex(['chapter_id', 'type']);
        });
    }
};
