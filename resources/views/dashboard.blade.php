<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Management</title>
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
                        <div class="column" draggable="true">
                            <div class="column-title">{{$column->title}}</div>
                            <ul class="task-list" id="to-do">
                                @foreach($column->tasks as $task)
                                    <li class="task-item" draggable="true">{{$task->title}}</li>
                                @endforeach
                            </ul>
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
                <h5 class="modal-title" id="exampleModalLabel">Купить хлеб</h5>
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
                        <label for="taskAssignees" class="form-label">Ответственные (username через запятую)</label>
                        <textarea class="form-control" placeholder="@username1, @username2, @username3" id="taskAssignees" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
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
        }, 0);
    });

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
                // Find the parent container of the dropzone
                const columnContainer = document.getElementById('column-container');
                const dropIndex = Array.from(columnContainer.children).indexOf(dropzone);

                // Insert the dragged column before the dropzone or append it at the end
                if (dropIndex !== -1) {
                    columnContainer.insertBefore(draggedItem, columnContainer.children[dropIndex]);
                } else {
                    columnContainer.appendChild(draggedItem);
                }
            }
        } else if (draggedItem.classList.contains('task-item')) {
            // Ensure tasks are only placed inside task lists
            let taskList = dropzone.closest('.task-list');

            // If the dropzone is a column but not a task-list, find its task-list
            if (!taskList && dropzone.classList.contains('column')) {
                taskList = dropzone.querySelector('.task-list');
            }

            if (taskList) {
                // Append the task to the task list
                taskList.appendChild(draggedItem);
            }
        }
    });

    // Open modal on task click
    document.querySelectorAll('.task-item').forEach(task => {
        task.addEventListener('click', () => {
            const modal = new bootstrap.Modal(document.getElementById('taskModal'));
            modal.show();
        });
    });
</script>

</body>
</html>
