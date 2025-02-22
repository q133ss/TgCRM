<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YandexService
{
    public function recognizeSpeech($chatId, $voice): ?string
    {
        $voiceFileId = $voice['file_id'];
        $token = config('services.telegram.token');

        try {
            // Получаем информацию о файле голосового сообщения
            $fileResponse = Http::get("https://api.telegram.org/bot{$token}/getFile", [
                'file_id' => $voiceFileId
            ]);
            $fileInfo = json_decode($fileResponse->body(), true);
            $filePath = $fileInfo['result']['file_path'];

            // Скачиваем файл голосового сообщения
            $fileUrl = "https://api.telegram.org/file/bot{$token}/{$filePath}";
            $voiceFile = Http::get($fileUrl);

            // Сохраняем файл локально
            $tempFilePath = storage_path('app/temp/' . uniqid() . '.ogg');
            file_put_contents($tempFilePath, $voiceFile->body());

            // Отправляем файл в Yandex SpeechKit для распознавания
            $recognizedText = $this->recognizeSpeechWithYandex($tempFilePath);

            // Удаляем временный файл
            unlink($tempFilePath);

            // Если текст успешно распознан, возвращаем его
            if (!empty($recognizedText)) {
                return $recognizedText;
            } else {
                (new TelegramService())->sendMessage($chatId, "Не удалось распознать голосовое сообщение.");
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Ошибка при обработке голосового сообщения: ' . $e->getMessage());
            (new TelegramService())->sendMessage($chatId, "Произошла ошибка при обработке голосового сообщения.");
        }
        return null;
    }

    private function recognizeSpeechWithYandex($oggFilePath): string
    {
        $folderId = config('services.yandex.folder_id');
        $iamToken = config('services.yandex.iam_token');

        if (!$folderId || !$iamToken) {
            Log::error('Не указаны folderId или iamToken для Yandex SpeechKit.');
            return '';
        }

        // URL для распознавания речи
        $url = "https://stt.api.cloud.yandex.net/speech/v1/stt:recognize?folderId={$folderId}&lang=ru-RU";

        // Отправляем файл через cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($oggFilePath)); // Отправляем содержимое файла
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $iamToken,
            'Content-Type: audio/ogg', // Указываем тип контента
        ]);

        // Выполняем запрос
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            Log::error("Ошибка при распознавании речи (HTTP {$httpCode}): " . $response);
            return '';
        }

        // Парсим ответ
        $result = json_decode($response, true);
        return $result['result'] ?? ''; // Возвращаем распознанный текст
    }
}
