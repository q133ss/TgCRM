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
                    <h5 class="offcanvas-title">Edit Task</h5>
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
                                    <label class="form-label">Assigned</label>
                                    <div class="assigned d-flex flex-wrap"></div>
                                </div>
                                <div class="mb-5">
                                    <label class="form-label" for="attachments">Вложения</label>
                                    <div>
                                        <input type="file" class="form-control" id="attachments" />
                                    </div>
                                </div>
                                <div class="mb-5">
                                    <label class="form-label">Описание</label>
                                    <textarea name="description" class="form-control" id="" cols="15" rows="3"></textarea>
                                </div>
                                <div>
                                    <div class="d-flex flex-wrap">
                                        <button type="button" class="btn btn-primary me-4" data-bs-dismiss="offcanvas">
                                            Обновить
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="offcanvas">
                                            Удалить
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- Activities -->
                        <div class="tab-pane fade text-heading" id="tab-activity" role="tabpanel">
                            <div class="media mb-4 d-flex align-items-center">
                                <div class="avatar me-3 flex-shrink-0">
                                    <span class="avatar-initial bg-label-success rounded-circle">HJ</span>
                                </div>
                                <div class="media-body ms-1">
                                    <p class="mb-0"><span>Jordan</span> Left the board.</p>
                                    <small class="text-muted">Today 11:00 AM</small>
                                </div>
                            </div>
                            <div class="media mb-4 d-flex align-items-center">
                                <div class="avatar me-3 flex-shrink-0">
                                    <img src="/assets/img/avatars/6.png" alt="Avatar" class="rounded-circle" />
                                </div>
                                <div class="media-body ms-1">
                                    <p class="mb-0">
                                        <span>Dianna</span> mentioned <span class="text-primary">@bruce</span> in a comment.
                                    </p>
                                    <small class="text-muted">Today 10:20 AM</small>
                                </div>
                            </div>
                            <div class="media mb-4 d-flex align-items-center">
                                <div class="avatar me-3 flex-shrink-0">
                                    <img src="/assets/img/avatars/2.png" alt="Avatar" class="rounded-circle" />
                                </div>
                                <div class="media-body ms-1">
                                    <p class="mb-0"><span>Martian</span> added moved Charts & Maps task to the done board.</p>
                                    <small class="text-muted">Today 10:00 AM</small>
                                </div>
                            </div>
                            <div class="media mb-4 d-flex align-items-center">
                                <div class="avatar me-3 flex-shrink-0">
                                    <img src="/assets/img/avatars/1.png" alt="Avatar" class="rounded-circle" />
                                </div>
                                <div class="media-body ms-1">
                                    <p class="mb-0"><span>Barry</span> Commented on App review task.</p>
                                    <small class="text-muted">Today 8:32 AM</small>
                                </div>
                            </div>
                            <div class="media mb-4 d-flex align-items-center">
                                <div class="avatar me-3 flex-shrink-0">
                                    <span class="avatar-initial bg-label-dark rounded-circle">BW</span>
                                </div>
                                <div class="media-body ms-1">
                                    <p class="mb-0"><span>Bruce</span> was assigned task of code review.</p>
                                    <small class="text-muted">Today 8:30 PM</small>
                                </div>
                            </div>
                            <div class="media mb-4 d-flex align-items-center">
                                <div class="avatar me-3 flex-shrink-0">
                                    <span class="avatar-initial bg-label-danger rounded-circle">CK</span>
                                </div>
                                <div class="media-body ms-1">
                                    <p class="mb-0">
                                        <span>Clark</span> assigned task UX Research to
                                        <span class="text-primary">@martian</span>
                                    </p>
                                    <small class="text-muted">Today 8:00 AM</small>
                                </div>
                            </div>
                            <div class="media mb-4 d-flex align-items-center">
                                <div class="avatar me-3 flex-shrink-0">
                                    <img src="/assets/img/avatars/4.png" alt="Avatar" class="rounded-circle" />
                                </div>
                                <div class="media-body ms-1">
                                    <p class="mb-0">
                                        <span>Ray</span> Added moved <span>Forms & Tables</span> task from in progress to done.
                                    </p>
                                    <small class="text-muted">Today 7:45 AM</small>
                                </div>
                            </div>
                            <div class="media mb-4 d-flex align-items-center">
                                <div class="avatar me-3 flex-shrink-0">
                                    <img src="/assets/img/avatars/1.png" alt="Avatar" class="rounded-circle" />
                                </div>
                                <div class="media-body ms-1">
                                    <p class="mb-0"><span>Barry</span> Complete all the tasks assigned to him.</p>
                                    <small class="text-muted">Today 7:17 AM</small>
                                </div>
                            </div>
                            <div class="media mb-4 d-flex align-items-center">
                                <div class="avatar me-3 flex-shrink-0">
                                    <span class="avatar-initial bg-label-success rounded-circle">HJ</span>
                                </div>
                                <div class="media-body ms-1">
                                    <p class="mb-0"><span>Jordan</span> added task to update new images.</p>
                                    <small class="text-muted">Today 7:00 AM</small>
                                </div>
                            </div>
                            <div class="media mb-4 d-flex align-items-center">
                                <div class="avatar me-3 flex-shrink-0">
                                    <img src="/assets/img/avatars/6.png" alt="Avatar" class="rounded-circle" />
                                </div>
                                <div class="media-body ms-1">
                                    <p class="mb-0">
                                        <span>Dianna</span> moved task <span>FAQ UX</span> from in progress to done board.
                                    </p>
                                    <small class="text-muted">Today 7:00 AM</small>
                                </div>
                            </div>
                            <div class="media mb-4 d-flex align-items-center">
                                <div class="avatar me-3 flex-shrink-0">
                                    <span class="avatar-initial bg-label-danger rounded-circle">CK</span>
                                </div>
                                <div class="media-body ms-1">
                                    <p class="mb-0"><span>Clark</span> added new board with name <span>Done</span>.</p>
                                    <small class="text-muted">Yesterday 3:00 PM</small>
                                </div>
                            </div>
                            <div class="media d-flex align-items-center">
                                <div class="avatar me-3 flex-shrink-0">
                                    <span class="avatar-initial bg-label-dark rounded-circle">BW</span>
                                </div>
                                <div class="media-body ms-1">
                                    <p class="mb-0"><span>Bruce</span> added new task in progress board.</p>
                                    <small class="text-muted">Yesterday 12:00 PM</small>
                                </div>
                            </div>
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

    <!-- Main JS -->
    <script src="/assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="/assets/js/app-kanban.js"></script>
@endsection
