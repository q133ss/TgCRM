@extends('layouts.app')
@section('title', 'Календарь')
@section('meta')
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="/assets/vendor/libs/typeahead-js/typeahead.css" />
    <link rel="stylesheet" href="/assets/vendor/libs/fullcalendar/fullcalendar.css" />
    <link rel="stylesheet" href="/assets/vendor/libs/flatpickr/flatpickr.css" />
    <link rel="stylesheet" href="/assets/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="/assets/vendor/libs/quill/editor.css" />
    <link rel="stylesheet" href="/assets/vendor/libs/@form-validation/form-validation.css" />
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card app-calendar-wrapper">
            <div class="row g-0">
                <!-- Calendar Sidebar -->
                <div class="col-3 app-calendar-sidebar border-end" id="app-calendar-sidebar">
                    <div class="p-5 my-sm-0 mb-4 border-bottom">
                        <button
                            class="btn btn-primary btn-toggle-sidebar w-100"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#addEventSidebar"
                            aria-controls="addEventSidebar">
                            <i class="ri-add-line ri-16px me-1_5"></i>
                            <span class="align-middle">Добавить задачу</span>
                        </button>
                    </div>
                    <div class="px-4">
                        <!-- inline calendar (flatpicker) -->
                        <div class="inline-calendar"></div>

                        <hr class="mb-5 mx-n4 mt-3" />
                        <!-- Filter -->
{{--                        <div class="mb-4 ms-1">--}}
{{--                            <h5>Event Filters</h5>--}}
{{--                        </div>--}}

{{--                        <div class="form-check form-check-secondary mb-5 ms-3">--}}
{{--                            <input--}}
{{--                                class="form-check-input select-all"--}}
{{--                                type="checkbox"--}}
{{--                                id="selectAll"--}}
{{--                                data-value="all"--}}
{{--                                checked />--}}
{{--                            <label class="form-check-label" for="selectAll">View All</label>--}}
{{--                        </div>--}}

{{--                        <div class="app-calendar-events-filter text-heading">--}}
{{--                            <div class="form-check form-check-danger mb-5 ms-3">--}}
{{--                                <input--}}
{{--                                    class="form-check-input input-filter"--}}
{{--                                    type="checkbox"--}}
{{--                                    id="select-personal"--}}
{{--                                    data-value="personal"--}}
{{--                                    checked />--}}
{{--                                <label class="form-check-label" for="select-personal">Personal</label>--}}
{{--                            </div>--}}
{{--                            <div class="form-check mb-5 ms-3">--}}
{{--                                <input--}}
{{--                                    class="form-check-input input-filter"--}}
{{--                                    type="checkbox"--}}
{{--                                    id="select-business"--}}
{{--                                    data-value="business"--}}
{{--                                    checked />--}}
{{--                                <label class="form-check-label" for="select-business">Business</label>--}}
{{--                            </div>--}}
{{--                            <div class="form-check form-check-warning mb-5 ms-3">--}}
{{--                                <input--}}
{{--                                    class="form-check-input input-filter"--}}
{{--                                    type="checkbox"--}}
{{--                                    id="select-family"--}}
{{--                                    data-value="family"--}}
{{--                                    checked />--}}
{{--                                <label class="form-check-label" for="select-family">Family</label>--}}
{{--                            </div>--}}
{{--                            <div class="form-check form-check-success mb-5 ms-3">--}}
{{--                                <input--}}
{{--                                    class="form-check-input input-filter"--}}
{{--                                    type="checkbox"--}}
{{--                                    id="select-holiday"--}}
{{--                                    data-value="holiday"--}}
{{--                                    checked />--}}
{{--                                <label class="form-check-label" for="select-holiday">Holiday</label>--}}
{{--                            </div>--}}
{{--                            <div class="form-check form-check-info ms-3">--}}
{{--                                <input--}}
{{--                                    class="form-check-input input-filter"--}}
{{--                                    type="checkbox"--}}
{{--                                    id="select-etc"--}}
{{--                                    data-value="etc"--}}
{{--                                    checked />--}}
{{--                                <label class="form-check-label" for="select-etc">ETC</label>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                    </div>
                </div>
                <!-- /Calendar Sidebar -->

                <!-- Calendar & Modal -->
                <div class="col app-calendar-content">
                    <div class="card shadow-none border-0">
                        <div class="card-body pb-0">
                            <!-- FullCalendar -->
                            <div id="calendar"></div>
                        </div>
                    </div>
                    <div class="app-overlay"></div>
                    <!-- FullCalendar Offcanvas -->
                    <div
                        class="offcanvas offcanvas-end event-sidebar"
                        tabindex="-1"
                        id="addEventSidebar"
                        aria-labelledby="addEventSidebarLabel">
                        <div class="offcanvas-header border-bottom">
                            <h5 class="offcanvas-title" id="addEventSidebarLabel">Add Event</h5>
                            <button
                                type="button"
                                class="btn-close text-reset"
                                data-bs-dismiss="offcanvas"
                                aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body">
                            <form class="event-form pt-0" id="eventForm" onsubmit="return false">
                                <div class="form-floating form-floating-outline mb-5">
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="eventTitle"
                                        name="title"
                                        placeholder="Задача" />
                                    <label for="eventTitle">Задача</label>
                                </div>
                                <div class="form-floating form-floating-outline mb-5">
                                    <select class="select2 select-event-label form-select" id="projectId" name="project_id">
                                        @foreach($user->projects as $project)
                                            <option data-label="primary" value="{{$project->id}}" selected>{{$project->title}}</option>
                                        @endforeach
                                    </select>
                                    <label for="projectId">Проект</label>
                                </div>

                                <div class="form-floating form-floating-outline mb-5">
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="eventStartDate"
                                        name="date"
                                        placeholder="Дата" />
                                    <label for="eventStartDate">Дата</label>
                                </div>

                                <div class="form-floating form-floating-outline mb-5">
                                    <input
                                        type="time"
                                        class="form-control"
                                        id="eventTime"
                                        name="time"
                                        placeholder="Время" />
                                    <label for="eventStartDate">Время</label>
                                </div>
                                <div class="form-floating form-floating-outline mb-5">
                                    <textarea class="form-control" name="description" id="eventDescription"></textarea>
                                    <label for="eventDescription">Описание</label>
                                </div>
                                <div class="mb-5 d-flex justify-content-sm-between justify-content-start my-6 gap-2">
                                    <div class="d-flex">
                                        <button type="submit" id="addEventBtn" class="btn btn-primary btn-add-event me-4">
                                            Добавить
                                        </button>
                                        <button
                                            type="reset"
                                            class="btn btn-outline-secondary btn-cancel me-sm-0 me-1"
                                            data-bs-dismiss="offcanvas">
                                            Закрыть
                                        </button>
                                    </div>
                                    <button class="btn btn-outline-danger btn-delete-event d-none">Удалить</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- /Calendar & Modal -->
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="/assets/vendor/libs/fullcalendar/fullcalendar.js"></script>
    <script src="/assets/vendor/libs/@form-validation/popular.js"></script>
    <script src="/assets/vendor/libs/@form-validation/bootstrap5.js"></script>
    <script src="/assets/vendor/libs/@form-validation/auto-focus.js"></script>
    <script src="/assets/vendor/libs/select2/select2.js"></script>
    <script src="/assets/vendor/libs/moment/moment.js"></script>
    <script src="/assets/vendor/libs/flatpickr/flatpickr.js"></script>

    <script src="/assets/js/app-calendar-events.js"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/ru.js"></script>
    <script src="/assets/js/app-calendar.js"></script>
@endsection
