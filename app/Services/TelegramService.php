<?php

namespace App\Services;

use App\Models\User;

class TelegramService
{
    public function processMessage($message, User $user)
    {
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';
        $entities = $message['entities'] ?? [];
        $voice = $message['voice'] ?? null;
        $document = $message['document'] ?? null;
        $photo = $message['photo'] ?? null;

        // Проверяем, является ли чат групповым
        $isGroupChat = $message['chat']['type'] === 'group' || $message['chat']['type'] === 'supergroup';

        // Проверяем, есть ли упоминание бота
        $botMentioned = false;
        $mentionedUsers = [];

        foreach ($entities as $entity) {
            if ($entity['type'] === 'mention') {
                $mentionedUsername = substr($text, $entity['offset'], $entity['length']);
                if ($mentionedUsername === config('services.telegram.username')) {
                    $botMentioned = true;
                } else {
                    $mentionedUsers[] = $mentionedUsername;
                }
            }
        }

        // Логика для личного диалога
        if (!$isGroupChat) {
            if ($voice) {
                // Обработка голосовых сообщений
                $this->recognizeSpeech($chatId, $voice);
            } elseif (($document || $photo) && !$text) {
                // Если есть файл, но нет текста
                $this->sendMessage($chatId, 'Пожалуйста, добавьте описание к файлу.');
            } elseif ($text) {
                // Обработка обычных текстовых сообщений
                $files = [];
                // Проверяем наличие файлов
                if (isset($message['document'])) {
                    $files[] = $message['document'];
                }
                if (isset($message['photo'])) {
                    $files[] = end($message['photo']); // Берем последнее фото (наибольшее разрешение)
                }
                // Создаем задачу
                $taskService = new TaskService();
                $project = $taskService->checkCreateProject($chatId, $user, false);
                $taskService->create($chatId, $text, $user, $project, $files);
            }
        }
        // Логика для группового чата
        elseif ($isGroupChat && $botMentioned) {
            if ($voice) {
                // Обработка голосовых сообщений с упоминанием бота
                $this->recognizeSpeech($chatId, $voice);
            } elseif (($document || $photo) && !$text) {
                // Если есть файл, но нет текста
                $this->sendMessage($chatId, 'Пожалуйста, добавьте описание к файлу.');
            } elseif (($document || $photo) && $text) {
                // Если есть файл и текст
                $this->createTaskFromGroupWithFiles($chatId, $text, $document ?? $photo);
            } elseif ($text) {
                // Обработка текстовых сообщений с упоминанием бота
                $this->createTaskFromGroup($chatId, $text, $mentionedUsers);
            }
        }
    }
    public function sendMessage($chatId, $text)
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

    # todo вынести в другие сервисы!
    private function recognizeSpeech($chatId, $voice)
    {
        $fileId = $voice['file_id'];
        $filePath = $this->getFileUrl($fileId);

        // Здесь можно вызвать API для распознавания речи
        // Например, используя Google Speech-to-Text
        $recognizedText = 'Привет! Это ваше голосовое сообщение.'; // Заглушка

        $this->sendMessage($chatId, "Вы сказали: $recognizedText");
    }

    private function getFileUrl($fileId)
    {
        $token = config('services.telegram.token');
        $url = "https://api.telegram.org/bot$token/getFile?file_id=$fileId";
        $response = json_decode(file_get_contents($url), true);

        if ($response['ok']) {
            return "https://api.telegram.org/file/bot$token/" . $response['result']['file_path'];
        }

        return null;
    }

    // Создание задачи из группы
    private function createTaskFromGroup($chatId, $text, $mentionedUsers)
    {
        $taskDescription = str_replace('@your_bot_username', '', $text); // Удаляем упоминание бота
        $mentionedUsernames = implode(', ', $mentionedUsers);

        $this->sendMessage($chatId, "Задача создана: $taskDescription\nОтветственные: $mentionedUsernames");
    }

    private function createTaskFromGroupWithFiles($chatId, $text, $file){
        $fileId = $file['file_id'];
        $fileUrl = $this->getFileUrl($fileId);
        $this->sendMessage("Задача с файлом \nФайл: [$fileUrl]($fileUrl)");
    }

    // Создание задачи с файлами

    private function createTaskWithFiles($chatId, $text, $file)
    {
        $fileId = $file['file_id'];
        $fileUrl = $this->getFileUrl($fileId);

        $this->sendMessage($chatId, "Задача создана: $text\nФайл: [$fileUrl]($fileUrl)");
    }
}
