<?php

namespace App\Services;

class TelegramService
{
    private function processMessage($message)
    {
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';

        // Простой ответ на сообщение
        if ($text === '/start') {
            $this->sendMessage($chatId, 'Привет! Это мой первый бот.');
        } elseif ($text === '/help') {
            $this->sendMessage($chatId, 'Я могу помочь тебе с различными задачами!');
        } else {
            $this->sendMessage($chatId, "Ты написал: $text");
        }
    }
    private function sendMessage($chatId, $text)
    {
        $token = config('services.telegram.token');
        $url = "https://api.telegram.org/bot$token/sendMessage";
        $data = [
            'chat_id' => $chatId,
            'text' => $text,
        ];

        file_get_contents($url, false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => json_encode($data),
            ],
        ]));
    }
}
