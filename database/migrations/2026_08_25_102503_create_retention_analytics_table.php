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
        Schema::create('retention_analytics', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('youtube_publication_id')->constrained('youtube_publications')->cascadeOnDelete();
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedInteger('average_view_duration_seconds')->default(0);
            $table->decimal('completion_percentage', 5, 2)->default(0.00);
            $table->decimal('ctr_percentage', 5, 2)->default(0.00);
            $table->decimal('revenue_usd', 8, 4)->default(0.0000);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retention_analytics');
    }
};
