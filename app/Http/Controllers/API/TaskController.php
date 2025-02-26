<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\TaskController\StoreRequest;
use App\Models\Column;
use App\Models\File;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    private User $user;

    public function __construct()
    {
        if(isset(request()->uid)){
            $this->user = User::where('telegram_id', request()->uid)->firstOrFail();
        }else{
            if(auth()->check()) {
                $this->user = auth()->user();
            }else{
                abort(403);
            }
        }
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();

        $text = $data['title'];
        $project = Project::findOrFail($data['project_id']);
        unset($data['title']);
        unset($data['project_id']);
        $data['creator_id'] = $this->user?->id;

        $taskService = new TaskService();
        $parsedDate = $taskService->parseDate($text);
        $reminderTime = $taskService->parseReminderTime($text);
        $cleanText = $taskService->cleanTextFromDateTime($text, $parsedDate, $reminderTime);

        $data['title'] = $cleanText;

        // Устанавливаем дату и время
        if($parsedDate['date'] != null){
            $data['date'] = $parsedDate['date'];
        }

        if($parsedDate['time'] != null){
            $data['time'] = $parsedDate['time'];
        }

        try {
            $task = Task::create($data);
            DB::table('task_responsibles')->insert([
                'task_id' => $task->id,
                'user_id' => $this->user?->id,
            ]);

            // Устанавливаем напоминание, если указано время
            if ($reminderTime) {
                $taskService->scheduleReminder($task, $reminderTime, $project->chat_id);
            }
            return response()->json(['task' => $task], 201);
        }catch (\Exception $exception){
            return response()->json(['message' => 'Попробуйте еще раз'], 500);
        }
    }

    public function update(StoreRequest $request, string $id)
    {
        $data = $request->validated();

        $text = $data['title'];
        $reminder = $data['reminder'] ?? null;
        $project = Project::findOrFail($data['project_id']);

        unset($data['title']);
        unset($data['responsible']);
        unset($data['reminder']);
        unset($data['project_id']);

        $taskService = new TaskService();
        $parsedDate = $taskService->parseDate($text);
        $reminderTime = $taskService->parseReminderTime($text);
        $cleanText = $taskService->cleanTextFromDateTime($text, $parsedDate, $reminderTime);

        $data['title'] = $cleanText;

        // Устанавливаем дату и время
        if(isset($data['date']) && $parsedDate['date'] != null){
            $data['date'] = $parsedDate['date'];
        }

        if(isset($data['time']) && $parsedDate['time'] != null){
            $data['time'] = $parsedDate['time'];
        }

        try {
            $task = Task::findOrFail($id);
            $updated = $task->update($data);


            if(isset($data['responsible'])) {
                DB::table('task_responsibles')->where(['task_id' => $task->id])->delete();
                foreach ($data['responsible'] as $responsible) {
                    DB::table('task_responsibles')->insert([
                        'task_id' => $task->id,
                        'user_id' => $responsible,
                    ]);
                }
            }

            // Сохраняем файлы, если они есть
            if ($request->has('files')) {
                // Шаг 1: Получаем старые файлы для задачи
                $oldFiles = File::where([
                    'fileable_id' => $task->id,
                    'fileable_type' => Task::class,
                ]);

                foreach ($oldFiles->get() as $oldFile) {
                    if ($oldFile->src && Storage::disk('public')->exists(ltrim($oldFile->src, '/'))) {
                        Storage::disk('public')->delete(ltrim($oldFile->src, '/'));
                    }
                }

                $oldFiles->delete();

                foreach ($request->files as $file) {
                    $filePath = '/storage/'.$file->store('files', 'public');
                    File::create([
                        'src' => $filePath,
                        'fileable_id' => $task->id,
                        'fileable_type' => Task::class,
                    ]);
                }
            }

            // Устанавливаем напоминание, если указано время
            if ($reminderTime && $reminder == null) {
                $taskService->scheduleReminder($task, $reminderTime, $project->chat_id);
            }elseif(!$reminderTime && $reminder != null){
                $taskService->scheduleReminder($task, $reminder, $project->chat_id);
            }

            return response()->json(['task' => $task], 200);
        }catch (\Exception $exception){
            return response()->json(['message' => 'Попробуйте еще раз'], 500);
        }
    }
    public function columnSort(Request $request, string $project_id): \Illuminate\Http\JsonResponse
    {
        # todo тут надо добавить проверку на проект и доступы!
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:columns,id',
        ]);

        $order = $validated['order'];

        foreach ($order as $index => $columnId) {
            Column::where('id', $columnId)->update(['order' => $index + 1]);
        }

        return response()->json(['message' => 'Порядок колонок успешно обновлен'], 200);
    }
}
