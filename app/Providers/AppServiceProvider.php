<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Domain\Publishing\Services\GoogleYouTubeService;
use Domain\Publishing\Services\YouTubeService;
use Domain\Shared\Services\Ai\LanguageModel;
use Domain\Shared\Services\Ai\OpenAiLanguageModel;
use Domain\Visual\Services\ImageGenerator;
use Domain\Visual\Services\OpenAiImageGenerator;
use Domain\Voice\Services\ElevenLabsSpeechGenerator;
use Domain\Voice\Services\SpeechGenerator;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LanguageModel::class, OpenAiLanguageModel::class);
        $this->app->bind(SpeechGenerator::class, ElevenLabsSpeechGenerator::class);
        $this->app->bind(ImageGenerator::class, OpenAiImageGenerator::class);
        $this->app->bind(YouTubeService::class, GoogleYouTubeService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
