<?php

namespace App\Jobs;

use App\Models\Task;
use App\Services\TelegramService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendReminderJob implements ShouldQueue
{
    use Queueable;
    private Task $task;
    private string $chatId;

    /**
     * Create a new job instance.
     */
    public function __construct(Task $task, string $chatId)
    {
        $this->task = $task;
        $this->chatId = $chatId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $title = $this->task?->title;
        $date = $this->task?->date?->foramt('d.m.Y');
        $status = $this->task?->column?->title;

        (new TelegramService())->sendMessage($this->chatId, "⏰ Напоминание о задаче: « $title » \n
📅 Срок выполнения: $date \n
📍 Текущий статус: $status");
    }
}
