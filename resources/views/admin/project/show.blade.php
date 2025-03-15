@extends('layouts.admin')
@section('title', 'Проект')
@section('content')
    <!-- Основной контент -->
    <div class="col-md-10 main-content">
        <h1 class="mt-3">Проект - {{$project->title}}</h1>
        <h3>Задачи</h3>
        <div class="column-container" id="column-container">
            @foreach($project->columns as $column)
                @if ($loop->first)
                    <style>
                        #column-{{$column->id}} {
                            display: block;
                        }
                    </style>
                @endif

                <div class="column" id="column-{{$column->id}}" data-column-id="{{$column->id}}" draggable="true">
                    <!-- Заголовок колонки -->
                    <div class="column-header">
                        <div class="column-title">{{$column->title}}</div>
                    </div>

                    <!-- Список задач -->
                    <ul class="task-list">
                        @foreach($column->tasks as $task)
                            <li class="task-item" id="task-item-{{$task->id}}" onclick="showTask('{{$task->id}}', '{{$column->id}}')" data-task-id="{{$task->id}}" draggable="true">
                                <!-- Название задачи -->
                                <div class="task-title">Задача: <span style="font-weight: bold">{{$task->title}}</span></div>

                                <!-- Дополнительная информация о задаче -->
                                <div class="task-details">
                                    <span class="task-date">Дата: {{$task->date}}</span> <br>
                                    <span class="task-time">Время: {{$task->time}}</span> <br>
                                    <span class="task-creator">Создал: {{$task->creator_id}}</span> <br>
                                    <span class="task-responsible">Ответственные:
                                        <br>
                                    @foreach($task->responsible as $resp)
                                        <span>@</span>{{$resp->username}} <br>
                                    @endforeach
                                    </span> <br>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
@endsection
