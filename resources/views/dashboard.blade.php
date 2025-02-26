<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление проектами</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #121212;
            color: #ffffff;
            font-family: 'Arial', sans-serif;
        }
        .navbar {
            background-color: #1e1e1e;
        }
        .sidebar {
            background-color: #181818;
            color: #fff;
            height: 100vh;
            overflow-y: auto;
        }
        .sidebar-header {
            padding: 1rem;
            text-align: center;
            border-bottom: 1px solid #333;
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
        }
        .sidebar-menu li a {
            display: block;
            padding: 10px 20px;
            text-decoration: none;
            color: #aaa;
            transition: background-color 0.3s;
        }
        .sidebar-menu li a:hover {
            background-color: #2c2c2c;
            color: #fff;
        }
        .task-list {
            list-style: none;
            padding: 0;
        }
        .task-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            margin-bottom: 5px;
            background-color: #222;
            border-radius: 5px;
            cursor: grab;
        }
        .task-item:hover {
            background-color: #2c2c2c;
        }
        .modal {
            --bs-modal-bg: #1e1e1e;
            --bs-modal-color: #fff;
        }
        .modal-content {
            background-color: #1e1e1e;
            color: #fff;
            border: none;
        }
        .modal-header, .modal-footer {
            border-color: #333;
        }
        .column {
            min-width: 250px;
            background-color: #1e1e1e;
            padding: 10px;
            border-radius: 5px;
            margin-right: 10px;
        }
        .column-title {
            font-size: 18px;
            margin-bottom: 10px;
        }
        .column-container {
            display: flex;
            gap: 10px;
        }

        @media screen and (max-width: 768px) {
            .sidebar{
                height: 100%;
            }
            .column-container{
                overflow-x: scroll;
            }
        }

        .add-task-form-btns{
            margin-top: 4px;
            display: grid;
            grid-template-columns: 5fr 1fr;
            grid-column-gap: 5px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar">
            <div class="sidebar-header">
                @if($user->avatar_url != null)
                    <img src="{{$user->avatar_url}}" width="100px" style="border-radius: 100%" alt="avatar">
                    <br><br>
                @endif
                <p>{{$user->first_name}} {{$user->last_name}}</p>
                <p><span>@</span>{{$user->username}}</p>
            </div>
            <ul class="sidebar-menu">
                @foreach($projects as $project)
                    <li><a href="{{route('project.show', $project->id)}}?uid={{request()->uid}}">{{$project->title}}</a></li>
                @endforeach
            </ul>
        </div>

        <!-- Main Content -->
        <div class="col-md-10">
            @php
                if(!isset($project)){
                    $project = $projects->first();
                }
            @endphp
            <div class="container mt-4">
                <div class="column-container" id="column-container">
                    @foreach($project->columns as $column)
                        <div class="column" id="column-{{$column->id}}" data-column-id="{{$column->id}}" draggable="true">
                            <div class="column-title">{{$column->title}}</div>
                            <ul class="task-list" id="to-do">
                                @foreach($column->tasks as $task)
                                    <li class="task-item" id="task-item-{{$task->id}}" onclick="showTask('{{$task->id}}')" data-task-id="{{$task->id}}" draggable="true">{{$task->title}}</li>
                                @endforeach
                            </ul>

                            <!-- Кнопка "Добавить задачу" -->
                            <div class="add-task">
                                <button class="btn btn-sm btn-outline-primary add-task-btn" id="add-task-btn-{{$column->id}}" onclick="toggleAddTask(this, '{{$column->id}}')">Добавить задачу</button>
                                <div class="add-task-form d-none" id="add-task-form-{{$column->id}}">
                                    <input type="text" class="form-control task-input" placeholder="Введите название задачи">
                                    <div class="add-task-form-btns">
                                        <button class="btn btn-sm btn-success" onclick="createTask(this, '{{$column->id}}')">Добавить</button>
                                        <button class="btn btn-sm btn-danger" onclick="cancelAddTask(this, '{{$column->id}}')">X</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="taskTitle" class="form-label">Название</label>
                        <input type="text" class="form-control" id="taskTitle" placeholder="Сдать отчет завтра 12:30">
                    </div>
                    <div class="mb-3">
                        <label for="taskDescription" class="form-label">Описание</label>
                        <textarea class="form-control" id="taskDescription" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="taskAssignees" class="form-label">Ответственные</label>
                        <select name="responsible" class="form-select" multiple id="taskAssignees">
                            <option value="1">@username</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="taskDate" class="form-label">Дата</label>
                        <input type="date" class="form-control" id="taskDate" placeholder="2025-02-12">
                    </div>

                    <div class="mb-3">
                        <label for="taskTime" class="form-label">Время</label>
                        <input type="time" class="form-control" id="taskTime" placeholder="12:00">
                    </div>

                    <div class="mb-3">
                        <label for="taskFiles" class="form-label">Время</label>
                        <input type="file" class="form-control" id="taskFiles" multiple>
                    </div>

                    <div class="mb-3">
                        <label for="taskReminder" class="form-label">Напомнить о задаче (За сколько времени до начала задачи)</label>
                        <input type="time" id="taskReminder" name="reminder" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="deleteBtn">Удалить</button>
                <button type="button" class="btn btn-primary">Сохранить изменения</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Drag and Drop functionality
    let draggedItem = null;

    document.addEventListener('dragstart', function (event) {
        draggedItem = event.target;
        setTimeout(() => {
            event.target.style.display = 'none';
        }, 0);
    });

    document.addEventListener('dragend', function (event) {
        setTimeout(() => {
            if (draggedItem !== null) {
                draggedItem.style.display = 'block';
                draggedItem = null;
            }

            // Если была перемещена колонка, отправляем AJAX-запрос
            if (event.target.classList.contains('column')) {
                updateColumnOrder();
            }
        }, 0);
    });

    function updateColumnOrder(){
        const columnContainer = document.getElementById('column-container');
        const columns = Array.from(columnContainer.children).filter(col => col.classList.contains('column'));

        // Extract column IDs in the new order
        const columnOrder = columns.map(column => column.dataset.columnId);

        // Send AJAX request to update column order on the server
        fetch('/api/column-order/{{$project->id}}?uid={{request()->uid}}', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                order: columnOrder,
            }),
        })
            .then(response => {
                console.log(response)
                if (!response.ok) {
                    throw new Error('Не удалось обновить порядок колонок.');
                }
                return response.json();
            })
            .then(data => {
                console.log('Порядок колонок успешно обновлен:', data);
            })
            .catch(error => {
                console.error('Ошибка при обновлении порядка колонок:', error);
                alert('Произошла ошибка при обновлении порядка колонок. Пожалуйста, попробуйте позже.');
            });
    }

    document.addEventListener('dragover', function (event) {
        event.preventDefault();
    });

    document.addEventListener('drop', function (event) {
        event.preventDefault();
        if (draggedItem === null) return;

        const dropzone = event.target.closest('.column, .task-list');
        if (!dropzone) return;

        // Check if the dragged item is a column or a task
        if (draggedItem.classList.contains('column')) {
            // Ensure columns are only placed as siblings, not inside each other
            if (dropzone.classList.contains('column') && dropzone !== draggedItem) {
                const columnContainer = document.getElementById('column-container');
                const dropIndex = Array.from(columnContainer.children).indexOf(dropzone);
                if (dropIndex !== -1) {
                    columnContainer.insertBefore(draggedItem, columnContainer.children[dropIndex]);
                } else {
                    columnContainer.appendChild(draggedItem);
                }
            }
        } else if (draggedItem.classList.contains('task-item')) {
            // Ensure tasks are only placed inside task lists
            let taskList = dropzone.closest('.task-list');
            if (!taskList && dropzone.classList.contains('column')) {
                taskList = dropzone.querySelector('.task-list');
            }
            if (taskList) {
                // Append the task to the task list
                taskList.appendChild(draggedItem);

                // Get the new column ID
                const newColumnId = taskList.closest('.column').dataset.columnId;

                // Get the task ID
                const taskId = draggedItem.dataset.taskId;
                const text = draggedItem.textContent;

                // Send AJAX request to update column_id for the task
                updateTaskColumn(taskId, newColumnId, text);
            }
        }
    });

    // Function to send AJAX request to update task's column_id
    function updateTaskColumn(taskId, newColumnId, text) {
        let route = '';
        @if(auth()->check())
        route = '/api/task/'+taskId;
        @else
        route = '/api/task/'+taskId+'?uid={{request()->uid}}'
        @endif
        fetch(route, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                column_id: newColumnId,
                project_id: '{{$project->id}}',
                title: text
            }),
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Не удалось обновить задачу.');
            }
            return response.json();
        })
        .then(data => {
            console.log('Задача успешно обновлена:', data);
        })
        .catch(error => {
            console.error('Ошибка при обновлении задачи:', error);
            alert('Произошла ошибка при обновлении задачи. Пожалуйста, попробуйте позже.');
        });
    }

    // Open modal on task click
    function showTask(taskId) {
        document.querySelector('#deleteBtn').setAttribute('onclick', 'deleteTask('+taskId+')')
        document.querySelector('#exampleModalLabel').textContent = '';
        document.querySelector('#taskTitle').value = '';
        document.querySelector('#taskDescription').value = '';
        document.querySelector('#taskAssignees').value = '';
        document.getElementById('taskAssignees').innerHTML = '';
        document.querySelector('#taskDate').value = '';
        document.querySelector('#taskTime').value = '';
        document.querySelector('#taskReminder').value = '';

        fetch('/api/task/' + taskId + '?uid={{$user->telegram_id}}', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(response => {
                return response.json();
            })
            .then(data => {
                document.querySelector('#exampleModalLabel').textContent = data.title;
                document.querySelector('#taskTitle').value = data.title;
                document.querySelector('#taskDescription').value = data.description;
                document.querySelector('#taskAssignees').value = data.description;
                document.querySelector('#taskDate').value = data.date;
                document.querySelector('#taskTime').value = data.time;
                // document.querySelector('#taskFiles').value = data.files; // выводим их в виде квадратов с крестиком вверху
                document.querySelector('#taskReminder').value = data.reminder;

                data.responsible.forEach(assignee => {
                    const option = document.createElement('option');
                    option.value = assignee.id; // Значение опции - ID пользователя
                    option.textContent = `@${assignee.username} | (${assignee.first_name} ${assignee.last_name})`; // Отображаемое имя
                    document.getElementById('taskAssignees').appendChild(option);
                });
                //  в select!

                const modal = new bootstrap.Modal(document.getElementById('taskModal'));
                modal.show();
            })
            .catch(error => {
                console.log(error)
                alert('Произошла ошибка. Пожалуйста, попробуйте позже.');
            });
    }

    // Функция для переключения формы добавления задачи
    function toggleAddTask(button, column) {
        document.querySelector('#add-task-btn-'+column).classList.toggle('d-none');
        document.querySelector('#add-task-form-'+column).classList.toggle('d-none');
    }

    // Функция для создания задачи
    function createTask(button, columnId) {
        const input = button.parentElement.previousElementSibling;
        const taskTitle = input.value.trim();

        if (taskTitle !== '') {
            // Очищаем поле ввода и скрываем форму
            input.value = '';
            document.querySelector('#add-task-btn-'+columnId).classList.toggle('d-none');
            document.querySelector('#add-task-form-'+columnId).classList.toggle('d-none');
            sendTaskToServer(taskTitle, columnId);
        }
    }

    function sendTaskToServer(taskTitle, columnId) {
        let route = @if(auth()->check())'/api/task'@else'/api/task?uid={{request()->uid}}'@endif;
        fetch(route, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                title: taskTitle,
                column_id: columnId,
                project_id: '{{$project->id}}'
            }),
        })
            .then(response => {
                return response.json();
            })
            .then(data => {
                // Создаем новую задачу
                const newTask = document.createElement('li');
                newTask.className = 'task-item';
                newTask.draggable = true;
                newTask.textContent = taskTitle;

                newTask.setAttribute('data-task-id', data.task.id);
                newTask.setAttribute('id', 'task-item-'+data.task.id);
                newTask.setAttribute('onclick', 'showTask('+data.task.id+')');

                // Добавляем задачу в список
                const taskList = document.querySelector('#column-'+columnId).querySelector('.task-list');
                taskList.appendChild(newTask);
            })
            .catch(error => {
                alert('Произошла ошибка. Пожалуйста, попробуйте позже.');
            });
    }

    // Функция для отмены добавления задачи
    function cancelAddTask(button, column) {
        const input = button.parentElement.previousElementSibling;
        input.value = ''; // Очищаем поле ввода
        document.querySelector('#add-task-btn-'+column).classList.toggle('d-none');
        document.querySelector('#add-task-form-'+column).classList.toggle('d-none');
    }

    function deleteTask(id){
        let conf = confirm('Вы уверенны, что хотите удалить задачу?')
        if(conf){
            fetch('/api/task/'+id+'?uid={{$user->telegram_id}}', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => {
                return response.json();
            })
            .then(data => {
                document.querySelector('#task-item-'+id).remove()
                const modal = bootstrap.Modal.getInstance(document.getElementById('taskModal'));
                if (modal) {
                    modal.hide();
                }
            })
            .catch(error => {
                alert('Произошла ошибка. Пожалуйста, попробуйте позже.');
            });
        }
    }
</script>

</body>
</html>
