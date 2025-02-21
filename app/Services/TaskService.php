<?php

namespace App\Services;

use App\Jobs\SendReminderJob;
use App\Models\File;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TaskService
{
    public function create(string $chatId, string $text, User $user, array $files = [])
    {
        // Извлекаем дату и время из текста
        $parsedDate = $this->parseDate($text);
        $reminderTime = $this->parseReminderTime($text);

        // Создаем задачу
        $task = Task::create([
            'title' => $text,
            'creator_id' => $user->id,
            'date' => $parsedDate['date'] ?? null,
            'time' => $parsedDate['time'] ?? null,
        ]);

        // Добавляем текущего пользователя как ответственного
        DB::table('task_responsibles')::create([
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
            $this->scheduleReminder($task, $reminderTime);
        }

        // Отправляем подтверждение пользователю
        $this->sendMessage($chatId, "Задача создана: $text");
    }

    private function parseDate($text): array
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
        } elseif (preg_match('/(понедельник|вторник|среда|четверг|пятница|суббота|воскресенье)/ui', $text, $matches)) {
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

    private function parseReminderTime($text): ?int
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
        $localPath = storage_path('app/files/' . basename($filePath));

        file_put_contents($localPath, $response);

        return $localPath;
    }

    private function scheduleReminder(Task $task, $minutesBefore)
    {
        $reminderTime = now()->subMinutes($minutesBefore)->setTimeFromTimeString($task->time);

        dispatch(new SendReminderJob($task))->delay($reminderTime);
    }
}
