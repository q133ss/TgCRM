@extends('layouts.app')
@section('title', 'Проект '.$project->title)
@section('meta')
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/typeahead-js/typeahead.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/jkanban/jkanban.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/flatpickr/flatpickr.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/quill/typography.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/quill/katex.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/quill/editor.css" />

    <!-- Page CSS -->

    <link rel="stylesheet" href="../../assets/vendor/css/pages/app-kanban.css" />
    <style>
        .delete-file-button {
            position: relative;
            left: -10px;
            top: -30px;
            margin-left: 5px; /* Отступ от текста ссылки */
            background-color: red;
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            cursor: pointer;
            font-size: 12px;
            line-height: 18px; /* Центрирование текста внутри кнопки */
        }

        .delete-file-button:hover {
            background-color: darkred;
        }
    </style>
@endsection
@section('content')
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="app-kanban">
            <!-- Add new board -->
            <div class="row">
                <div class="col-12">
                    <form class="kanban-add-new-board">
                        <label class="kanban-add-board-btn" for="kanban-add-board-input">
                            <i class="ri-add-line"></i>
                            <span class="align-middle">Добавить</span>
                        </label>
                        <input
                            type="text"
                            class="form-control w-px-250 kanban-add-board-input mb-4 d-none"
                            placeholder="Название доски"
                            id="kanban-add-board-input"
                            required />
                        <div class="mb-4 kanban-add-board-input d-none">
                            <button class="btn btn-primary btn-sm me-3">Добавить</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm kanban-add-board-cancel-btn">
                                Закрыть
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Kanban Wrapper -->
            <div class="kanban-wrapper"></div>

            <!-- Edit Task/Task & Activities -->
            <div class="offcanvas offcanvas-end kanban-update-item-sidebar">
                <div class="offcanvas-header border-bottom">
                    <h5 class="offcanvas-title">Изменить задачу</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body pt-2">
                    <div class="nav-align-top">
                        <ul class="nav nav-tabs mb-2">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-update">
                                    <i class="ri-edit-box-line me-2"></i>
                                    <span class="align-middle">Изменить</span>
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-activity">
                                    <i class="ri-pie-chart-line me-2"></i>
                                    <span class="align-middle">Активность</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content px-0 pb-0">
                        <!-- Update item/tasks -->
                        <div class="tab-pane fade show active" id="tab-update" role="tabpanel">
                            <form>
                                <div class="form-floating form-floating-outline mb-5">
                                    <input type="text" id="title" class="form-control" placeholder="Задача" />
                                    <label for="title">Задача</label>
                                </div>
                                <div class="form-floating form-floating-outline mb-5">
                                    <input type="text" id="due-date" class="form-control" placeholder="Укажите дату" />
                                    <label for="due-date">Дата</label>
                                </div>
                                <div class="mb-5">
                                    <label class="form-label">Ответственные</label>
                                    <div class="assigned d-flex flex-wrap"></div>
                                </div>
                                <div class="md-5">
                                    <select name="assigned" id="assignedSelect" multiple class="form-select">
                                    </select>
                                </div>
                                <div class="mb-5">
                                    <label class="form-label" for="attachments">Вложения</label>
                                    <div>
                                        <input type="file" class="form-control" multiple id="attachments" />
                                    </div>
                                    <div id="files"></div>
                                </div>
                                <div class="mb-5">
                                    <label class="form-label">Описание</label>
                                    <textarea name="description" class="form-control taskDescriptionTextArea" id="description" cols="15" rows="3"></textarea>
                                </div>
                                <div>
                                    <div class="d-flex flex-wrap">
                                        <button type="button" class="btn btn-primary me-4" data-id="" id="updateTask" data-bs-dismiss="offcanvas">
                                            Обновить
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" data-id="" id="deleteTask" data-bs-dismiss="offcanvas">
                                            Удалить
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- Activities -->
                        <div class="tab-pane fade text-heading" id="tab-activity" role="tabpanel">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->
@endsection
@section('scripts')
    <!-- Vendors JS -->
    <script src="/assets/vendor/libs/moment/moment.js"></script>
    <script src="/assets/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="/assets/vendor/libs/select2/select2.js"></script>
    <script src="/assets/vendor/libs/jkanban/jkanban.js"></script>
    <script src="/assets/vendor/libs/quill/katex.js"></script>
    <script src="/assets/vendor/libs/quill/quill.js"></script>

    <!-- Page JS -->
    <script src="/assets/js/app-kanban.js"></script>
@endsection
