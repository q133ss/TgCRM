<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class YandexTokenUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yandex:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновляет токены Yandex';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $oauthToken = config('services.yandex.token');

        $response = Http::post('https://iam.api.cloud.yandex.net/iam/v1/tokens', [
            'yandexPassportOauthToken' => $oauthToken,
        ]);

        // Шаг 2: Обрабатываем ответ
        if ($response->successful()) {
            $data = $response->json(); // Получаем данные в формате JSON
            $iamToken = $data['iamToken'] ?? null; // Извлекаем IAM-токен

            if ($iamToken) {
                Cache::put('yandex_iam_token', $iamToken, now()->addHour());
            } else {
                \Log::error('IAM Token not found in response: ' . json_encode($data));
                throw new \Exception('IAM Token not found in response.');
            }
        } else {
            // Если запрос не успешен, логируем ошибку
            \Log::error('Error fetching Yandex IAM Token: ' . $response->body());
            throw new \Exception('Failed to fetch Yandex IAM Token: ' . $response->status());
        }
    }
}
