<?php

namespace App\Services;

use App\Jobs\SendReminderJob;
use App\Models\Column;
use App\Models\File;
use App\Models\Project;
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

    public function create(string $chatId, string $text, User $user, Project $project, array $files = [])
    {
        // Извлекаем дату и время из текста
        $parsedDate = $this->parseDate($text);
        $reminderTime = $this->parseReminderTime($text);
        $cleanText = $this->cleanTextFromDateTime($text, $parsedDate, $reminderTime);

        // Создаем задачу
        $task = Task::create([
            'title' => $cleanText,
            'column_id' => $project->columns?->sortByDesc('order')->pluck('id')->first(),
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

        // Отправляем подтверждение пользователю
        (new TelegramService())->sendMessage($chatId, "Задача создана: $cleanText");
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

    private function scheduleReminder(Task $task, $minutesBefore, string $chatId)
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

    public function checkCreateProject(string $chatId, User $user, bool $isGroup)
    {
        $project = Project::where('chat_id', $chatId);

        if(!$project->exists()){
            try {
                DB::beginTransaction();
                $project = Project::create([
                    'chat_id' => $chatId,
                    'created_by' => $user->id,
                    'title' => $user->first_name ?? 'Проект',
                    'is_group' => $isGroup
                ]);

                (new Column())->createDefault($project->id);
                DB::commit();
                return $project;
            }catch (\Exception $e){
                DB::rollBack();
                \Log::error($e);
            }
        }

        return $project->first();
    }

    private function cleanTextFromDateTime($text, $parsedDate, $reminderTime): string
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
}
