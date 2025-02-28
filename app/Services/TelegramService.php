<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class TelegramService
{
    public function processMessage($message, User $user)
    {
        $taskService = new TaskService();
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';
        $caption = $message['caption'] ?? '';
        $entities = $message['entities'] ?? [];
        $voice = $message['voice'] ?? null;
        $document = $message['document'] ?? null;
        $photo = $message['photo'] ?? null;

        $project = $taskService->checkCreateProject($chatId, $user, false);

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

        // Обработка команд
        if (isset($entities[0]) && $entities[0]['type'] === 'bot_command') {
            $command = substr($text, 0, strpos($text, ' ') ?: strlen($text)); // Извлекаем команду

            switch ($command) {
                case '/start':
                    $this->handleStartCommand($chatId);
                    return;
                case '/tasks':
                    $this->handleTasksCommand($chatId, $user, $project);
                    return;
                case '/help':
                    $this->handleHelpCommand($chatId);
                    return;
            }
        }

        // Логика для личного диалога
        if (!$isGroupChat) {
            if ($voice) {
                // Обработка голосовых сообщений
                $this->voiceTask($chatId, $voice, $user, $project);
            } elseif ((isset($document) && !empty($document)) || (isset($photo) && !empty($photo))) {
                // Используем caption, если text пустой
                $description = !empty($text) ? $text : $caption;

                if (empty($description)) {
                    // Если есть файл, но нет текста или описания
                    $this->sendMessage($chatId, 'Пожалуйста, добавьте описание к файлу.');
                } else {
                    // Если есть файл и текст/описание
                    $this->createTaskWithFiles($chatId, $description, $user, $project, $document ?? end($photo));
                }
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
                $this->createTaskFromGroup($chatId, $text, $mentionedUsers, $user, $project);
            }
        }

        // Приветственное сообщение при добавлении бота в группу
        if ($isGroupChat && isset($message['new_chat_members'])) {
            foreach ($message['new_chat_members'] as $member) {
                if ($member['id'] == config('services.telegram.id')) { // Проверяем, что добавлен именно наш бот
                    $this->sendMessage($message['chat']['id'], 'Спасибо, что добавили меня в этот чат! Я помогу вам управлять задачами.');
                    return;
                }
            }
        }
    }
    public function sendMessage($chatId, $text, $keyboard = [])
    {
        $token = config('services.telegram.token');
        $url = "https://api.telegram.org/bot$token/sendMessage";

        // Создаем базовый массив данных для отправки
        $data = [
            'chat_id' => $chatId,
            'text' => $text,
        ];

        // Если передан массив $keyboard, добавляем его как InlineKeyboard
        if (!empty($keyboard)) {
            $data['reply_markup'] = json_encode([
                'inline_keyboard' => $keyboard,
            ]);
        }

        // Отправляем запрос к Telegram API
        file_get_contents($url, false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => json_encode($data),
            ],
        ]));
    }

    // Создание задачи из группы
    private function createTaskFromGroup($chatId, $text, $mentionedUsers, $user, $project)
    {
        $taskDescription = str_replace(config('services.telegram.username'), '', $text); // Удаляем упоминание бота
        $responsibles = [];
        if(!empty($mentionedUsers)){
            $users = User::whereIn('username', $mentionedUsers);
            $responsibles = $users->pluck('id')->all();

            $existingUsers = $users->pluck('username')->all();
            $nonExistingUsers = array_diff($mentionedUsers, $existingUsers);
            $mentionedUsernames = implode(', ', $mentionedUsers);
        }

        if(!empty($nonExistingUsers)) {
            $keyboard = [
                [
                    [
                        'text' => 'Войти в систему',
                        'url' => 'https://t.me/'.config('services.telegram.username').'?start=start_command'
                    ]
                ]
            ];
            $this->sendMessage($chatId, "Указанных пользователей нет в системе: ".$mentionedUsernames, $keyboard);
        }else{
            (new TaskService())->make($chatId, $text, $user, $project, [], $responsibles);
            $this->sendMessage($chatId, "Задача создана: $taskDescription\nОтветственные: $mentionedUsernames");
        }
    }

    // Создание задачи с файлами
    private function createTaskWithFiles($chatId, $text, $user, $project, $file)
    {
        (new TaskService())->create($chatId, $text, $user, $project,[$file]);
    }

    private function voiceTask($chatId, $voice, $user, $project)
    {
        $text = (new YandexService())->recognizeSpeech($chatId, $voice);
        if($text != null){
            (new TaskService())->create($chatId, $text, $user, $project);
        }
    }

    private function handleStartCommand($chatId){
        $botName = config('services.telegram.username');
        $this->sendMessage($chatId, "🤖 Привет, это « $botName » — ваш бот-ассистент и трекер задач для Telegram!

« $botName » поможет планировать задачи в командах прямо из чатов, а также управлять личными делами и календарём. ⏰

В следующих 10 сообщениях мы кратко расскажем о ключевых функциях.

📚 Подробные инструкции всегда доступны в документации на сайте или из команды /help в боте.

Если возникнут вопросы — пишите в чат поддержки, и мы с радостью поможем! 💬

Нажмите Далее ➡️, чтобы узнать, как ставить задачи в боте.");
    }

    private function handleTasksCommand($chatId, $user, $project)
    {
        // Получаем все задачи для проекта и пользователя
        $tasks = (new TaskService())->getAllByProject($project, $user);

        // Группируем задачи по дате
        $groupedTasks = (new TaskService())->groupTasksByDate($tasks);

        // Формируем сообщение
        $response = "Ваши текущие задачи:\n\n";

        // Задачи без даты
        if (!empty($groupedTasks['no_date'])) {
            $response .= "Без даты:\n";
            foreach ($groupedTasks['no_date'] as $task) {
                $response .= "* {$task->title}\n";
            }
            $response .= "\n"; // Добавляем пустую строку между группами
        }

        // Задачи с датой
        ksort($groupedTasks); // Сортируем группы по дате
        foreach ($groupedTasks as $date => $tasksForDate) {
            if ($date === 'no_date') {
                continue; // Пропускаем группу без даты (она уже обработана выше)
            }

            // Форматируем дату
            $formattedDate = $date; //Carbon::parse($date)->format('d.m.Y');
            $response .= "$formattedDate:\n";

            foreach ($tasksForDate as $task) {
                $title = $task->title;

                $response .= "* {$title}\n";
            }

            $response .= "\n"; // Добавляем пустую строку между группами
        }

        // Отправляем сообщение
        $this->sendMessage($chatId, $response);
    }
    private function handleHelpCommand($chatId){
        $botName = config('services.telegram.username');
        $this->sendMessage($chatId, "Инструкции по использованию « $botName » вы всегда можете найти на сайте.
📢 Подписывайтесь на наш канал, чтобы быть в курсе обновлений.

👥 Присоединяйтесь к нашему сообществу в чате $botName – там вы всегда сможете получить ответ на свой вопрос.");
    }
}
