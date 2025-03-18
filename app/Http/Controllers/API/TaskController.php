<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\TaskController\StoreRequest;
use App\Http\Requests\TaskController\MoveRequest;
use App\Models\Column;
use App\Models\File;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Carbon\Carbon;
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
                abort(401);
            }
        }
    }

    public function index()
    {
        return $this->user?->tasks;
    }

    public function tasksForProject(string $id)
    {
        // Получаем колонки с присоединенными задачами
        $columns = Column::where('project_id', $id)
            ->leftJoin('tasks', 'columns.id', '=', 'tasks.column_id')
            ->select(
                'columns.id as column_id',
                'columns.title as column_title',
                'tasks.id as task_id',
                'tasks.title as task_title',
                'tasks.date',
                'tasks.time',
                'tasks.description'
            )
            ->get();

        // Группируем данные по column_id
        $groupedData = $columns->groupBy('column_id');

        // Формируем выходной массив
        $formattedData = [];
        foreach ($groupedData as $columnId => $columnData) {
            // Название колонки берем из первого элемента группы
            $columnTitle = $columnData->first()->column_title ?? 'No Title';

            // Формируем массив задач для текущей колонки
            $items = [];
            foreach ($columnData as $task) {
                if ($task->task_id) {
                    // Если задача существует, добавляем её в массив
                    $modelTask = Task::where('tasks.id', $task->task_id)->first();
                    $responsibleNames = $modelTask?->responsible?->pluck('first_name');
                    $allFiles = $modelTask->files;
                    $files = $allFiles?->pluck('src')->all();
                    $filesCount = $allFiles?->count();

                    $items[] = [
                        "id" => $task->task_id,
                        "title" => $task->task_title,
                        "comments" => "0",
                        "description" => $task->description,
                        "badge-text" => "Без категории",
                        "badge" => "primary",
                        "due-date" => $task->date ? Carbon::parse($task->date)->format('j F') : 'No Date',
                        "attachments" => $filesCount,
                        "assigned" => $responsibleNames,
                        "members" => $responsibleNames,
                        "files" => $files
                    ];
                }
            }

            // Добавляем колонку в выходной массив
            $formattedData[] = [
                "id" => "board-".$columnId,
                "title" => $columnTitle,
                "item" => $items
            ];
        }

        // Возвращаем данные в формате JSON
        return response()->json($formattedData);
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
        unset($data['files']);
        unset($data['old_files']);

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
            if ($request->has('files') || $request->has('old_files')) {
                // Шаг 1: Получаем старые файлы для задачи
                $oldFiles = File::where([
                    'fileable_id' => $task->id,
                    'fileable_type' => Task::class,
                ])->whereNotIn('id', $request->old_files);

                foreach ($oldFiles->get() as $oldFile) {
                    if ($oldFile->src && Storage::disk('public')->exists(str_replace('/storage/', '',$oldFile->src))) {
                        Storage::disk('public')->delete(str_replace('/storage/', '',$oldFile->src));
                    }
                }

                $oldFiles->delete();

                if($request->has('files')) {
                    foreach ($request->file('files') as $file) {
                        $filePath = '/storage/' . $file->store('files', 'public');
                        File::create([
                            'src' => $filePath,
                            'fileable_id' => $task->id,
                            'fileable_type' => Task::class,
                        ]);
                    }
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
            \Log::error($exception);
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

    public function show(string $id)
    {
        return Task::findOrFail($id);
    }

    public function delete(string $id)
    {
        return Task::findOrFail($id)->delete();
    }

    public function getColumnById(string $id)
    {
        $columnId = Task::findOrFail($id)->column_id;
        return response()->json([
            'id' => $columnId
        ]);
    }

    public function getFirstColumn(string $id)
    {
        $columnId = Project::findOrFail($id)->columns?->first()->id;
        return response()->json([
            'id' => $columnId
        ]);
    }

    public function move(MoveRequest $request, string $id)
    {
        $task = Task::find($id);
        $task->update([
            'column_id' => $request->column_id
        ]);
        return response()->json(['message' => 'true']);
    }

    public function updateForWeb(StoreRequest $request, string $id): \Illuminate\Http\JsonResponse
    {
        $data = $request->validated();
        $text = $data['title'];

        unset($data['title']);
        unset($data['responsible']);
        unset($data['reminder']);
        unset($data['files']);
        unset($data['old_files']);

        $reminder = $data['reminder'] ?? null;
        $project = Project::findOrFail($data['project_id']);
        unset($data['project_id']);

        $taskService = new TaskService();
        $parsedDate = $taskService->parseDate($text);
        $reminderTime = $taskService->parseReminderTime($text);
        $cleanText = $taskService->cleanTextFromDateTime($text, $parsedDate, $reminderTime);
        $data['title'] = $cleanText;

        $task = Task::findOrFail($id);
        \Log::info($data);
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
            foreach ($request->file('files') as $file) {
                $filePath = '/storage/' . $file->store('files', 'public');
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
    }
}
