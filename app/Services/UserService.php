<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;

class UserService
{
    private string $botToken;

    public function __construct()
    {
        $this->botToken = config('services.telegram.token');
    }

    public function checkAndCreate(array $message)
    {
        $from = $message['from'];
        $telegramId = $from['id'];
        $username = $from['username'] ?? null;
        $firstName = $from['first_name'] ?? null;
        $lastName = $from['last_name'] ?? null;
        $avatarUrl = $this->getUserAvatar($telegramId);

        // Проверяем, существует ли пользователь в базе данных
        $user = User::where('telegram_id', $telegramId)->first();

        if (!$user) {
            // Если пользователя нет, создаем нового
            return User::create([
                'telegram_id' => $telegramId,
                'username' => $username,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'avatar_url' => $avatarUrl,
            ]);
        }

        return $user;
    }

    public function getUserAvatar($telegramId): ?string
    {
        // Шаг 1: Получаем фотографии профиля
        $response = Http::get("https://api.telegram.org/bot{$this->botToken}/getUserProfilePhotos", [
            'user_id' => $telegramId,
            'limit' => 1, // Получаем только одну последнюю фотографию
        ]);

        $data = $response->json();

        // Проверяем, есть ли фотографии
        if (isset($data['result']['photos'][0])) {
            // Берем первую фотографию (самую последнюю)
            $photo = $data['result']['photos'][0];

            // Берем file_id самой большой версии фотографии (последний элемент в массиве)
            $fileId = end($photo)['file_id'];

            // Шаг 2: Получаем URL файла
            $fileResponse = Http::get("https://api.telegram.org/bot{$this->botToken}/getFile", [
                'file_id' => $fileId,
            ]);

            $fileData = $fileResponse->json();

            if (isset($fileData['result']['file_path'])) {
                // Формируем URL файла
                $fileUrl = "https://api.telegram.org/file/bot{$this->botToken}/" . $fileData['result']['file_path'];
                return $fileUrl;
            }
        }

        // Если фотографий нет, возвращаем null
        return null;
    }
}
