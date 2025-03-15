<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TelegramService
{
    public function processMessage($update, User $user)
    {
        $taskService = new TaskService();
        $message = $update['message'] ?? $update['edited_message'];
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';
        $caption = $message['caption'] ?? '';
        $entities = $message['entities'] ?? [];
        $voice = $message['voice'] ?? null;
        $document = $message['document'] ?? null;
        $photo = $message['photo'] ?? null;

        $chatTitle = isset($message['chat']['title']) ? $message['chat']['title'] : $user->first_name;

        $project = $taskService->checkCreateProject($chatId, $user, false, $chatTitle);

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

        // Обработка фото для групп
        if ((isset($document) && !empty($document)) || (isset($photo) && !empty($photo))) {
            if ($isGroupChat) {
                // Используем caption, если text пустой
                $description = !empty($text) ? $text : $caption;

                if (empty($description)) {
                    // Если есть файл, но нет текста или описания
                    $this->sendMessage($chatId, 'Пожалуйста, добавьте описание к файлу.');
                } else {
                    // Преобразуем $photo в массив массивов
                    $files = [];
                    if (!empty($document)) {
                        $files[] = [$document]; // Добавляем документ как один файл
                    }
                    if (!empty($photo)) {
                        $files[] = $photo; // Добавляем фото как массив миниатюр
                    }

                    // Создаем задачу
                    $this->createTaskFromGroup($chatId, $description, $mentionedUsers, $user, $project, $files);
                }
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
        elseif($isGroupChat){
            // Приветственное сообщение при добавлении бота в группу
            if (isset($message['new_chat_members'])) {
                foreach ($message['new_chat_members'] as $member) {
                    if ($member['id'] == config('services.telegram.id')) { // Проверяем, что добавлен именно наш бот
                        $this->sendMessage($message['chat']['id'], 'Спасибо, что добавили меня в этот чат! Я помогу вам управлять задачами.');
                        return;
                    }
                }
            }

            if($botMentioned && $text) {
                // Обработка текстовых сообщений с упоминанием бота
                $this->sendMessage($chatId, 'ЗАДАЧА СОЗДАНА111');
                $this->createTaskFromGroup($chatId, $text, $mentionedUsers, $user, $project);
            }

            ///
            if (isset($update['edited_message'])) {
                $editedMessage = $update['edited_message'];
                $text = $editedMessage['caption'] ?? '';
                $voice = $editedMessage['voice'] ?? null;
                $entities = $editedMessage['caption_entities'] ?? [];

                // Проверяем, что это голосовое сообщение
                $isVoice = is_array($voice);
                $notExistsUsers = [];

                if ($isVoice) {
                    // Извлекаем все упоминания из текста
                    $mentions = [];
                    $isBotMentioned = false; // Флаг для проверки упоминания бота
                    foreach ($entities as $entity) {
                        if ($entity['type'] === 'mention') {
                            // Для упоминаний типа mention берем username
                            $offset = $entity['offset'];
                            $length = $entity['length'];
                            $username = substr($text, $offset, $length);

                            // Проверяем, упомянут ли бот
                            if ($username === config('services.telegram.username')) {
                                $isBotMentioned = true;
                            }

                            $mentions[] = $username;
                        } elseif ($entity['type'] === 'text_mention') {
                            // Для упоминаний типа text_mention берем user_id
                            $userId = $entity['user']['id'];

                            // Проверяем, упомянут ли бот (если ID бота известен)
                            if ($userId === config('services.telegram.bot_id')) {
                                $isBotMentioned = true;
                            }

                            $mentions[] = $userId;
                        }
                    }

                    // Если бот упомянут, создаем задачи для всех упомянутых пользователей
                    if ($isBotMentioned && !empty($mentions)) {
                        // Распознаем текст из голосового сообщения
                        $taskText = (new YandexService())->recognizeSpeech($chatId, $voice);

                        $task = (new TaskService())->create(
                            $chatId,
                            $taskText,
                            $user,
                            $project,
                            [],
                            true
                        );

                        foreach ($mentions as $mention) {
                            // Создаем задачу для каждого упомянутого пользователя
                            // Ставим ответсвенных! и отпрвляем сообщение
                            $mentionUser = User::where('username', str_replace('@', '', $mention))->first();
                            if($mentionUser) {
                                if($user->id != $mentionUser->id) {
                                    DB::table('task_responsibles')->insert([
                                        'task_id' => $task->id,
                                        'user_id' => $mentionUser->id
                                    ]);
                                }
                            }else{
                                if($mention != config('services.telegram.username')){
                                    $notExistsUsers[] = $mention;
                                }
                            }
                        }
                    } else {
                        \Log::info("Бот не был упомянут, задачи не создаются.");
                    }

                    if(!empty($notExistsUsers)){
                        $keyboard = [
                            [
                                [
                                    'text' => 'Войти в систему',
                                    'url' => 'https://t.me/' . config('services.telegram.username') . '?start=start_command',
                                ],
                            ],
                        ];
                        $usersText = implode(', ', $notExistsUsers);
                        $this->sendMessage(
                            $chatId,
                            "Пользователи $usersText не зарегистрированы в системе. Добавить их в задачу не удалось",
                            $keyboard
                        );
                    }

                }
            }
            ///
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
    private function createTaskFromGroup($chatId, $text, $mentionedUsers, $user, $project, array $files = [])
    {
        $mentionedUsers = array_map(function($username) {
            return ltrim($username, '@'); // Удаляем @ с начала строки
        }, $mentionedUsers);

        // Извлекаем описание задачи, исключая упоминание бота
        $botUsername = config('services.telegram.username');
        $taskDescription = str_replace($botUsername, '', $text);

        // Удаляем лишние пробелы
        $taskDescription = trim(preg_replace('/\s+/', ' ', $taskDescription));

        // Находим ответственных пользователей
        $responsibles = [];
        if (!empty($mentionedUsers)) {
            $users = User::whereIn('username', $mentionedUsers)->get();
            $responsibles = $users->pluck('id')->all();
            $existingUsers = $users->pluck('username')->all();
            $nonExistingUsers = array_diff($mentionedUsers, $existingUsers);
        }

        // Удаляем упоминания пользователей из описания задачи
        foreach ($mentionedUsers as $usr) {
            $taskDescription = str_replace($usr, '', $taskDescription);
        }
        $taskDescription = str_replace('@', '', $taskDescription);

        // Формируем список упомянутых пользователей
        $mentionedUsernamesWithAt = array_map(function($username) {
            return '@' . $username; // Добавляем @ перед каждым именем
        }, $mentionedUsers);
        $mentionedUsernames = implode(', ', array_map('trim', $mentionedUsernamesWithAt)) ?: 'Нет упоминаний';

        // Если есть пользователи, которых нет в системе
        if (!empty($nonExistingUsers)) {
            $keyboard = [
                [
                    [
                        'text' => 'Войти в систему',
                        'url' => 'https://t.me/' . config('services.telegram.username') . '?start=start_command',
                    ],
                ],
            ];

            $this->sendMessage(
                $chatId,
                "Не удалось создать задачу.\nУказанных пользователей нет в системе: " . implode(', ', array_map('trim', $nonExistingUsers)),
                $keyboard
            );
        } else {
            // Создаем задачу
            $task = (new TaskService())->make($chatId, $taskDescription, $user, $project, $files, $responsibles);

            // Отправляем подтверждение создания задачи
            $txt = "Задача создана: $taskDescription";
            if(!empty($mentionedUsers)) {
                $txt .= "\nОтветственные: $mentionedUsernames";
            }

            $keyboard = [
                [
                    [
                        'text' => 'Открыть',
                        'url' => config('app.url') . '/project/' . $project->id . '?uid=' . $user->telegram_id . '&task=' . $task->id
                    ]
                ]
            ];

            $this->sendMessage($chatId, $txt, $keyboard);
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
