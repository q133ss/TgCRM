<?php

namespace App\Services;

use App\Jobs\SendReminderJob;
use App\Models\Column;
use App\Models\File;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TaskService
{
    private string $days;
    public function __construct()
    {
        $this->days = '/(понедельник|вторник|среда|четверг|пятница|суббота|воскресенье)/ui';
    }

    public function create(string $chatId, string $text, User $user, Project $project, array $files = [], bool $isGroup = false)
    {
        // Извлекаем дату и время из текста
        $parsedDate = $this->parseDate($text);
        $reminderTime = $this->parseReminderTime($text);
        $cleanText = $this->cleanTextFromDateTime($text, $parsedDate, $reminderTime);

        // Создаем задачу
        try {
            $task = Task::create([
                'title' => $cleanText,
                'column_id' => $project->columns?->sortBy('order')->pluck('id')->first(),
                'creator_id' => $user->id,
                'date' => $parsedDate['date'] ?? null,
                'time' => $parsedDate['time'] ?? null,
            ]);

            // Добавляем текущего пользователя как ответственного
            DB::table('task_responsibles')->insert([
                'task_id' => $task->id,
                'user_id' => $user->id,
            ]);

            // Сохраняем файлы, если они есть
            if (!empty($files)) {
                foreach ($files as $file) {
                    $filePath = $this->saveFile($file);
                    File::create([
                        'src' => $filePath,
                        'fileable_id' => $task->id,
                        'fileable_type' => Task::class,
                    ]);
                }
            }

            // Устанавливаем напоминание, если указано время
            if ($reminderTime) {
                $this->scheduleReminder($task, $reminderTime, $chatId);
            }

            // Форматируем ответ для пользователя
            if ($parsedDate['date']) {
                $formattedDate = Carbon::createFromFormat('Y-m-d', $parsedDate['date'])->format('d.m.Y');
                $cleanText .= ". Дата $formattedDate";
            }
            if ($parsedDate['time']) {
                $cleanText .= " Время {$parsedDate['time']}";
            }

            if($isGroup){
                $keyboard = [
                    [
                        [
                            'text' => 'Открыть',
                            'url' => config('app.url') . '/project/' . $project->id . '?uid=' . $user->telegram_id . '&task=' . $task->id
                        ]
                    ]
                ];
            }else{
                $keyboard = [
                    [
                        [
                            'text' => 'Открыть',
                            'web_app' => [
                                'url' => config('app.url') . '/project/' . $project->id . '?uid=' . $user->telegram_id . '&task=' . $task->id
                            ]
                        ]
                    ]
                ];
            }

            // Отправляем подтверждение пользователю
            (new TelegramService())->sendMessage($chatId, "Задача создана: $cleanText", $keyboard);
            return $task;
        }catch (\Exception $e){
            \Log::error($e);
            (new TelegramService())->sendMessage($chatId, "Произошла ошибка, попробуйте еще раз!");
        }
    }

    public function parseDate($text): array
    {
        $result = ['date' => null, 'time' => null];

        // Простые шаблоны для даты
        if (preg_match('/(\d{1,2})[-\/.](\d{1,2})[-\/.](\d{2,4})/', $text, $matches)) {
            $day = $matches[1];
            $month = $matches[2];
            $year = strlen($matches[3]) === 2 ? '20' . $matches[3] : $matches[3];
            $result['date'] = "$year-$month-$day";
        } elseif (strpos($text, 'завтра') !== false) {
            $tomorrow = now()->addDay();
            $result['date'] = $tomorrow->format('Y-m-d');
        } elseif (preg_match($this->days, $text, $matches)) {
            // Распознавание дня недели
            $dayOfWeek = $this->getCarbonDayOfWeek($matches[1]);
            $nextDay = now()->next($dayOfWeek);
            $result['date'] = $nextDay->format('Y-m-d');
        }

        // Простые шаблоны для времени
        if (preg_match('/(\d{1,2}):(\d{2})/', $text, $matches)) {
            $result['time'] = "{$matches[1]}:{$matches[2]}";
        }

        return $result;
    }

    /**
     * Возвращает константу Carbon для дня недели по названию.
     *
     * @param string $dayName Название дня недели (например, "понедельник")
     * @return int Константа Carbon::MONDAY, Carbon::TUESDAY и т.д.
     */
    private function getCarbonDayOfWeek(string $dayName): int
    {
        $daysOfWeek = [
            'понедельник' => Carbon::MONDAY,
            'вторник' => Carbon::TUESDAY,
            'среда' => Carbon::WEDNESDAY,
            'четверг' => Carbon::THURSDAY,
            'пятница' => Carbon::FRIDAY,
            'суббота' => Carbon::SATURDAY,
            'воскресенье' => Carbon::SUNDAY,
        ];

        return $daysOfWeek[strtolower($dayName)] ?? Carbon::TODAY;
    }

    public function parseReminderTime($text): ?int
    {
        if (preg_match('/:\s*(\d+)/', $text, $matches)) {
            return intval($matches[1]); // Возвращает количество минут
        }

        return null;
    }

    private function getFileUrl($fileId): ?string
    {
        $token = config('services.telegram.token');
        $url = "https://api.telegram.org/bot$token/getFile?file_id=$fileId";
        $response = json_decode(file_get_contents($url), true);

        if ($response['ok']) {
            return "https://api.telegram.org/file/bot$token/" . $response['result']['file_path'];
        }

        return null;
    }

    private function saveFile($file): string
    {
        $fileId = $file['file_id'];
        $filePath = $this->getFileUrl($fileId);

        // Скачиваем файл на сервер
        $response = file_get_contents($filePath);

        // Генерируем имя файла
        $fileName = basename($filePath);

        // Сохраняем файл в storage/app/files/
        $localPath = storage_path('app/files/' . $fileName);
        file_put_contents($localPath, $response);

        // Возвращаем относительный путь
        return '/storage/app/files/' . $fileName;
    }

    public function scheduleReminder(Task $task, $minutesBefore, string $chatId)
    {
        if($task->time != null) {
            $reminderTime = now()->subMinutes($minutesBefore)->setTimeFromTimeString($task->time);

            dispatch(new SendReminderJob($task, $chatId))->delay($reminderTime);

            (new TelegramService())->sendMessage(
                $chatId,
                "✅ Напоминание для задачи «{$task->title}» успешно установлено на {$reminderTime->format('H:i')}."
            );
        }

        (new TelegramService())->sendMessage($chatId, '⏰ Не удалось установить напоминание для задачи. Время выполнения не указано.');
    }

    public function checkCreateProject(string $chatId, User $user, bool $isGroup, $title)
    {
        $project = Project::where('chat_id', $chatId);

        if(!$project->exists()){
            try {
                DB::beginTransaction();
                $project = Project::create([
                    'chat_id' => $chatId,
                    'created_by' => $user->id,
                    'title' => $title,
                    'is_group' => $isGroup
                ]);

                (new Column())->createDefault($project->id);

                DB::table('project_users')->insert([
                    'project_id' => $project->id,
                    'user_id' => $user->id,
                    'role_id' => Role::where('slug', 'owner')->pluck('id')->first()
                ]);

                DB::commit();
                return $project;
            }catch (\Exception $e){
                DB::rollBack();
                \Log::error($e);
            }
        }

        return $project->first();
    }

    public function cleanTextFromDateTime($text, $parsedDate, $reminderTime): string
    {
        // Удаляем дату из текста
        if ($parsedDate['date']) {
            $text = preg_replace('/\b\d{1,2}[-\/.]\d{1,2}[-\/.]\d{2,4}\b/', '', $text);
        }

        // Удаляем день недели из текста
        $text = preg_replace($this->days, '', $text);

        // Удаляем время из текста
        if ($parsedDate['time']) {
            $text = preg_replace('/\b\d{1,2}:\d{2}\b/', '', $text);
        }

        // Удаляем напоминание (например, ":25")
        if ($reminderTime) {
            $text = preg_replace('/:\s*\d+/', '', $text);
        }

        // Удаляем лишние пробелы
        return trim(preg_replace('/\s+/', ' ', $text));
    }

    public function getAllByProject($project, $user){
        return Task::leftJoin('task_responsibles', 'task_id', 'tasks.id')
            ->where('task_responsibles.user_id', $user->id)
            ->leftJoin('columns', 'columns.id', 'tasks.column_id')
            ->where('columns.project_id', $project->id)
            ->select('tasks.*')
            ->get();
    }

    public function groupTasksByDate($tasks)
    {
        $groupedTasks = ['no_date' => []]; // Инициализируем группу без даты

        foreach ($tasks as $task) {
            if ($task->date) {
                $date = \Carbon\Carbon::parse($task->date)->format('Y-m-d'); // Нормализуем дату
                $groupedTasks[$date][] = $task;
            } else {
                $groupedTasks['no_date'][] = $task;
            }
        }

        return $groupedTasks;
    }

    public function make(string $chatId, string $text, User $user, Project $project, array $files = [], array $responsibles = [])
    {
        // Извлекаем дату и время из текста
        $parsedDate = $this->parseDate($text);
        $reminderTime = $this->parseReminderTime($text);
        $cleanText = $this->cleanTextFromDateTime($text, $parsedDate, $reminderTime);

        // Создаем задачу
        try {
            $task = Task::create([
                'title' => $cleanText,
                'column_id' => $project->columns?->sortBy('order')->pluck('id')->first(),
                'creator_id' => $user->id,
                'date' => $parsedDate['date'] ?? null,
                'time' => $parsedDate['time'] ?? null,
            ]);

            // Добавляем текущего пользователя как ответственного
            DB::table('task_responsibles')->insert([
                'task_id' => $task->id,
                'user_id' => $user->id,
            ]);

            foreach ($responsibles as $responsible){
                DB::table('task_responsibles')->insert([
                    'task_id' => $task->id,
                    'user_id' => $responsible
                ]);
            }

            // Сохраняем файлы, если они есть
            $originalFiles = [];
            foreach ($files as $fileGroup) {
                if (!is_array($fileGroup)) {
                    \Log::error('Invalid file group data:', ['data' => $fileGroup]);
                    continue; // Пропускаем невалидные данные
                }

                // Находим файл с максимальным разрешением (оригинал)
                $originalFile = array_reduce($fileGroup, function ($carry, $item) {
                    if (!is_array($item) || !isset($item['width']) || !isset($item['height'])) {
                        \Log::error('Invalid file item data:', ['data' => $item]);
                        return $carry; // Пропускаем невалидные данные
                    }

                    if (!$carry || $item['width'] > $carry['width']) {
                        return $item;
                    }
                    return $carry;
                }, null);

                if ($originalFile) {
                    $originalFiles[] = $originalFile;
                }
            }

            if (!empty($originalFiles)) {
                foreach ($originalFiles as $file) {
                    try {
                        $filePath = $this->saveFile($file);
                        File::create([
                            'src' => $filePath,
                            'fileable_id' => $task->id,
                            'fileable_type' => Task::class,
                        ]);
                    } catch (\Exception $e) {
                        \Log::error('Error saving file:', ['error' => $e->getMessage(), 'file' => $file]);
                    }
                }
            }

            // Устанавливаем напоминание, если указано время
            if ($reminderTime) {
                $this->scheduleReminder($task, $reminderTime, $chatId);
            }
            return $task;
        }catch (\Exception $e){
            (new TelegramService())->sendMessage($chatId, "Произошла ошибка, попробуйте еще раз!");
        }
    }
}
