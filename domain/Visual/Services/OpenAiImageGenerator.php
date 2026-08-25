<?php

namespace Domain\Visual\Services;

use Domain\Visual\Data\ImageRequestData;
use Domain\Visual\Data\ImageResultData;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAiImageGenerator implements ImageGenerator
{
    public function __construct(
        protected ?string $apiKey = null,
        protected string $baseUrl = 'https://api.openai.com/v1'
    ) {
        $this->apiKey = $apiKey ?? config('services.openai.api_key', env('OPENAI_API_KEY'));
    }

    public function generate(ImageRequestData $request): ImageResultData
    {
        if (! $this->apiKey) {
            throw new RuntimeException('OpenAI API key is missing. Set OPENAI_API_KEY in your environment.');
        }

        $response = Http::withToken($this->apiKey)
            ->baseUrl($this->baseUrl)
            ->timeout(120)
            ->post('/images/generations', [
                'model' => $request->model,
                'prompt' => $request->prompt,
                'n' => 1,
                'size' => $request->size,
                'quality' => $request->quality,
                'response_format' => 'b64_json',
            ]);

        if ($response->failed()) {
            throw new RuntimeException('OpenAI DALL-E 3 API request failed: '.$response->body());
        }

        $data = $response->json();
        $b64Json = $data['data'][0]['b64_json'] ?? '';
        $binary = base64_decode($b64Json);

        [$w, $h] = explode('x', $request->size);
        $cost = $request->quality === 'hd' ? 0.080 : 0.040; // DALL-E 3 standard pricing $0.040/img

        return new ImageResultData(
            imageBinary: $binary,
            width: (int) $w,
            height: (int) $h,
            cost: $cost,
            provider: 'OpenAI',
            model: $request->model
        );
    }
}
