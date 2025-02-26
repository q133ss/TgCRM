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

        #mobileSelect{
            display: none;
        }

        @media screen and (max-width: 768px) {
            .sidebar{
                display: none;
            }

            .column{
                display: none;
            }

            #mobileSelect{
                display: block;
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
                <div class="mb-3" id="mobileSelect">
                    <label for="columnSelect" class="form-label">Колонка</label>
                    <select name="responsible" class="form-select" id="columnSelect">
                        @foreach($project->columns as $column)
                            <option value="{{$column->id}}">{{$column->title}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="column-container" id="column-container">
                    @foreach($project->columns as $column)
                        @if ($loop->first)
                            <style>
                                #column-{{$column->id}}{
                                    display: block;
                                }
                            </style>
                        @endif
                        <div class="column" id="column-{{$column->id}}" data-column-id="{{$column->id}}" draggable="true">
                            <div class="column-title">{{$column->title}}</div>
                            <ul class="task-list" id="to-do">
                                @foreach($column->tasks as $task)
                                    <li class="task-item" id="task-item-{{$task->id}}" onclick="showTask('{{$task->id}}', '{{$column->id}}')" data-task-id="{{$task->id}}" draggable="true">{{$task->title}}</li>
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

                    <div id="taskFileList" class="d-flex flex-wrap gap-2 mb-3"></div>

                    <div class="mb-3">
                        <label for="taskFiles" class="form-label">Файлы</label>
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
                <button type="button" class="btn btn-primary" id="saveBtn">Сохранить изменения</button>
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
        fetch('/update/api/task/'+taskId+'?uid={{request()->uid}}', {
            method: 'POST',
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
    let currentColumnId = '';

    let oldFiles = []; // Массив для хранения старых файлов
    let newFiles = []; // Массив для хранения новых файлов

    // Функция для отображения файлов
    function renderFiles() {
        const fileListContainer = document.querySelector('#taskFileList');
        fileListContainer.innerHTML = ''; // Очищаем контейнер

        // Отображаем старые файлы
        oldFiles.forEach((file, index) => {
            const fileItem = createFileItem(file, index, true);
            fileListContainer.appendChild(fileItem);
        });

        // Отображаем новые файлы
        newFiles.forEach((file, index) => {
            const fileItem = createFileItem(file, index, false);
            fileListContainer.appendChild(fileItem);
        });
    }

    // Функция для создания элемента файла
    function createFileItem(file, index, isOldFile) {
        const fileItem = document.createElement('div');
        fileItem.className = 'position-relative d-inline-block';
        fileItem.style.width = '100px';
        fileItem.style.height = '100px';
        fileItem.style.background = '#f8f9fa';
        fileItem.style.border = '1px solid #dee2e6';
        fileItem.style.borderRadius = '5px';
        fileItem.style.overflow = 'hidden';

        // Крестик для удаления файла
        const removeButton = document.createElement('button');
        removeButton.className = 'position-absolute top-0 end-0 btn btn-danger btn-sm';
        removeButton.innerHTML = '&times;';
        removeButton.style.padding = '0.1rem 0.3rem';
        removeButton.style.fontSize = '0.8rem';
        removeButton.addEventListener('click', () => {
            if (isOldFile) {
                oldFiles.splice(index, 1); // Удаляем старый файл
            } else {
                newFiles.splice(index, 1); // Удаляем новый файл
            }
            renderFiles(); // Перерисовываем список
        });

        // Иконка или миниатюра файла
        const fileIcon = document.createElement('div');
        fileIcon.className = 'd-flex justify-content-center align-items-center h-100';
        if (file.src && file.src.match(/\.(jpeg|jpg|png|gif)$/i)) {
            const img = document.createElement('img');
            img.src = file.src; // Используем URL из старого файла
            img.style.maxWidth = '100%';
            img.style.maxHeight = '100%';
            fileIcon.appendChild(img);
        } else {
            fileIcon.innerHTML = `<i class="bi bi-file-earmark"></i>`;
        }

        // Добавляем элементы в контейнер
        fileItem.appendChild(removeButton);
        fileItem.appendChild(fileIcon);
        return fileItem;
    }

    // Обработчик изменения файлового input
    document.querySelector('#taskFiles').addEventListener('change', function () {
        const files = Array.from(this.files); // Получаем новые файлы
        files.forEach(file => {
            // Создаем временный URL для отображения превью
            file.preview = URL.createObjectURL(file);
            newFiles.push(file); // Добавляем файл в массив новых файлов
        });
        renderFiles(); // Отображаем все файлы
    });

    // Колонка для мобильных устройств
    document.querySelector('#columnSelect').addEventListener('change', function () {
        document.querySelectorAll('.column').forEach((item) => {
            item.classList.add('d-none');
        });
        document.querySelector('#column-'+this.value).classList.remove('d-none');
        document.querySelector('#column-'+this.value).style.display = 'block';
    });

    function showTask(taskId, columnId) {
        currentColumnId = columnId;
        document.querySelector('#deleteBtn').setAttribute('onclick', 'deleteTask('+taskId+')')
        document.querySelector('#saveBtn').setAttribute('onclick', 'saveTask('+taskId+')')
        document.querySelector('#exampleModalLabel').textContent = '';
        document.querySelector('#taskTitle').value = '';
        document.querySelector('#taskDescription').value = '';
        document.querySelector('#taskAssignees').value = '';
        document.getElementById('taskAssignees').innerHTML = '';
        document.querySelector('#taskDate').value = '';
        document.querySelector('#taskTime').value = '';
        document.querySelector('#taskReminder').value = '';

        oldFiles = []; // Очищаем старые файлы
        newFiles = []; // Очищаем новые файлы
        renderFiles(); // Очищаем список файлов

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

                // Загружаем старые файлы
                if (data.files && data.files.length > 0) {
                    oldFiles = data.files.map(file => ({
                        id: file.id, // ID файла
                        src: file.src, // Путь к файлу
                        name: file.src.split('/').pop() // Имя файла
                    }));
                    renderFiles(); // Отображаем старые файлы
                }

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
        fetch('/api/task?uid={{request()->uid}}', {
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

    function saveTask(taskId){
        const projectId = 1; // Замените на реальный ID проекта
        const columnId = 1; // Замените на реальный ID колонки
        const taskTitle = document.getElementById('taskTitle').value;
        const taskDescription = document.getElementById('taskDescription').value;
        const taskAssignees = Array.from(document.getElementById('taskAssignees').selectedOptions).map(option => option.value);
        const taskDate = document.getElementById('taskDate').value;
        const taskTime = document.getElementById('taskTime').value;
        const taskReminder = document.getElementById('taskReminder').value;

        // Создаем объект FormData для отправки данных
        const formData = new FormData();
        formData.append('project_id', projectId);
        formData.append('column_id', columnId);
        formData.append('title', taskTitle);
        formData.append('description', taskDescription);
        formData.append('date', taskDate);
        formData.append('time', taskTime);
        formData.append('reminder', taskReminder);

        // Добавляем ответственных (responsible)
        taskAssignees.forEach((assignee, index) => {
            formData.append(`responsible[${index}]`, assignee);
        });

        // Добавляем старые файлы (если они не были удалены)
        oldFiles.forEach((file, index) => {
            formData.append(`old_files[${index}]`, file.id); // Отправляем ID старых файлов
        });

        // Добавляем новые файлы
        newFiles.forEach((file, index) => {
            formData.append('files[]', file);
        });

        // Отправка данных через AJAX
        const xhr = new XMLHttpRequest();
        xhr.open('POST', `/api/update/task/${taskId}?uid={{$user->telegram_id}}`, true);

        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                console.log('Задача успешно обновлена:', xhr.responseText);
            } else {
                console.error('Ошибка при обновлении задачи:', xhr.statusText);
            }
        };

        xhr.onerror = function() {
            console.error('Ошибка сети при отправке запроса');
        };

        xhr.send(formData);
    }

    // Для задачи из ТГ
    @if(request()->has('task'))
    fetch('/api/task/' + {{request()->task}} + '?uid={{$user->telegram_id}}', {
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

            // Загружаем старые файлы
            if (data.files && data.files.length > 0) {
                oldFiles = data.files.map(file => ({
                    id: file.id, // ID файла
                    src: file.src, // Путь к файлу
                    name: file.src.split('/').pop() // Имя файла
                }));
                renderFiles(); // Отображаем старые файлы
            }

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
    @endif
</script>

</body>
</html>
