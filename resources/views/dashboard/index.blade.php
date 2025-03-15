@extends('layouts.app')
@section('title', 'Главная')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6">
            <!-- Gamification Card -->
            <div class="col-md-12 col-xxl-12">
                <div class="card">
                    <div class="d-flex align-items-end row">
                        <div class="col-md-6 order-2 order-md-1">
                            <div class="card-body">
                                <h4 class="card-title mb-4">Добро пожаловать <span class="fw-bold">{{$user->first_name}}!</span> 🎉</h4>
                                <p class="mb-0">Рады видеть тебя снова! У тебя {{$user->getCountForFront('projects')}} и {{$user->getCountForFront('tasks')}} в работе. Пора начинать день! 💪</p>
                                <a href="javascript:;" class="btn btn-primary">Список задач</a>
                            </div>
                        </div>
                        <div class="col-md-6 text-center text-md-end order-1 order-md-2">
                            <div class="card-body pb-0 px-0 pt-2">
                                <img
                                    src="/assets/img/illustrations/illustration-john-light.png"
                                    height="186"
                                    class="scaleX-n1-rtl"
                                    alt="View Profile"
                                    data-app-light-img="illustrations/illustration-john-light.png"
                                    data-app-dark-img="illustrations/illustration-john-dark.png" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Gamification Card -->

            <!-- Top Referral Source  -->
            <div class="col-12 col-xxl-8">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between">
                        <div>
                            <h5 class="card-title mb-1">Мои проекты</h5>
                            <p class="card-subtitle mb-0">Ваши активные проекты</p>
                        </div>
                    </div>
                    <div class="tab-content p-0">
                        <div class="tab-pane fade show active" id="navs-orders-id" role="tabpanel">
                            <div class="table-responsive text-nowrap">
                                <table class="table border-top">
                                    <thead>
                                    <tr>
                                        <th class="bg-transparent border-bottom">Название</th>
                                        <th class="bg-transparent border-bottom">Кол-во участников</th>
                                        <th class="text-end bg-transparent border-bottom">Дата создания</th>
                                    </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                    @foreach($user->projects as $project)
                                        <tr>
                                            <td><a href="{{route('dashboard.projects.show', $project->id)}}">{{$project->title}}</a></td>
                                            <td>{{$project->members?->count()}}</td>
                                            <td class="text-end fw-medium">{{$project->created_at?->format('d.m.Y H:i')}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="navs-sales-id" role="tabpanel">
                            <div class="table-responsive text-nowrap">
                                <table class="table border-top">
                                    <thead>
                                    <tr>
                                        <th class="bg-transparent border-bottom">Product Name</th>
                                        <th class="bg-transparent border-bottom">STATUS</th>
                                        <th class="text-end bg-transparent border-bottom">Profit</th>
                                        <th class="text-end bg-transparent border-bottom">REVENUE</th>
                                    </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                    <tr>
                                        <td>facebook Adsense</td>
                                        <td>
                                            <div class="badge bg-label-info rounded-pill">In Draft</div>
                                        </td>
                                        <td class="text-success fw-medium text-end">+5%</td>
                                        <td class="text-end fw-medium">$5</td>
                                    </tr>
                                    <tr>
                                        <td>Affiliation Program</td>
                                        <td>
                                            <div class="badge bg-label-primary rounded-pill">Active</div>
                                        </td>
                                        <td class="text-danger fw-medium text-end">-24%</td>
                                        <td class="text-end fw-medium">$5,576</td>
                                    </tr>
                                    <tr>
                                        <td>Email Marketing Campaign</td>
                                        <td>
                                            <div class="badge bg-label-warning rounded-pill">warning</div>
                                        </td>
                                        <td class="text-success fw-medium text-end">+5%</td>
                                        <td class="text-end fw-medium">$2,857</td>
                                    </tr>
                                    <tr>
                                        <td>facebook Workspace</td>
                                        <td>
                                            <div class="badge bg-label-success rounded-pill">Completed</div>
                                        </td>
                                        <td class="text-danger fw-medium text-end">-12%</td>
                                        <td class="text-end fw-medium">$850</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="navs-profit-id" role="tabpanel">
                            <div class="table-responsive text-nowrap">
                                <table class="table border-top">
                                    <thead>
                                    <tr>
                                        <th class="bg-transparent border-bottom">Product Name</th>
                                        <th class="bg-transparent border-bottom">STATUS</th>
                                        <th class="text-end bg-transparent border-bottom">Profit</th>
                                        <th class="text-end bg-transparent border-bottom">REVENUE</th>
                                    </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                    <tr>
                                        <td>Affiliation Program</td>
                                        <td>
                                            <div class="badge bg-label-primary rounded-pill">Active</div>
                                        </td>
                                        <td class="text-danger fw-medium text-end">-24%</td>
                                        <td class="text-end fw-medium">$5,576</td>
                                    </tr>
                                    <tr>
                                        <td>instagram Adsense</td>
                                        <td>
                                            <div class="badge bg-label-info rounded-pill">In Draft</div>
                                        </td>
                                        <td class="text-success fw-medium text-end">+5%</td>
                                        <td class="text-end fw-medium">$5</td>
                                    </tr>
                                    <tr>
                                        <td>instagram Workspace</td>
                                        <td>
                                            <div class="badge bg-label-success rounded-pill">Completed</div>
                                        </td>
                                        <td class="text-danger fw-medium text-end">-12%</td>
                                        <td class="text-end fw-medium">$850</td>
                                    </tr>
                                    <tr>
                                        <td>Email Marketing Campaign</td>
                                        <td>
                                            <div class="badge bg-label-danger rounded-pill">warning</div>
                                        </td>
                                        <td class="text-danger fw-medium text-end">-5%</td>
                                        <td class="text-end fw-medium">$857</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="navs-income-id" role="tabpanel">
                            <div class="table-responsive text-nowrap">
                                <table class="table border-top">
                                    <thead>
                                    <tr>
                                        <th class="bg-transparent border-bottom">Product Name</th>
                                        <th class="bg-transparent border-bottom">STATUS</th>
                                        <th class="text-end bg-transparent border-bottom">Profit</th>
                                        <th class="text-end bg-transparent border-bottom">REVENUE</th>
                                    </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                    <tr>
                                        <td>reddit Workspace</td>
                                        <td>
                                            <div class="badge bg-label-warning rounded-pill">process</div>
                                        </td>
                                        <td class="text-danger fw-medium text-end">-12%</td>
                                        <td class="text-end fw-medium">$850</td>
                                    </tr>
                                    <tr>
                                        <td>Affiliation Program</td>
                                        <td>
                                            <div class="badge bg-label-primary rounded-pill">Active</div>
                                        </td>
                                        <td class="text-danger fw-medium text-end">-24%</td>
                                        <td class="text-end fw-medium">$5,576</td>
                                    </tr>
                                    <tr>
                                        <td>reddit Adsense</td>
                                        <td>
                                            <div class="badge bg-label-info rounded-pill">In Draft</div>
                                        </td>
                                        <td class="text-success fw-medium text-end">+5%</td>
                                        <td class="text-end fw-medium">$5</td>
                                    </tr>
                                    <tr>
                                        <td>Email Marketing Campaign</td>
                                        <td>
                                            <div class="badge bg-label-success rounded-pill">Completed</div>
                                        </td>
                                        <td class="text-success fw-medium text-end">+50%</td>
                                        <td class="text-end fw-medium">$857</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Top Referral Source  -->

            <!-- Weekly Sales Chart-->


            <div class="col-12 col-xxl-4 col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <h5 class="mb-0">Последняя активность</h5>
                        </div>
                    </div>
                    <div class="card-body pt-4">
                        <ul class="timeline card-timeline mb-0">
                            <li class="timeline-item timeline-item-transparent">
                                <span class="timeline-point timeline-point-primary"></span>
                                <div class="timeline-event">
                                    <div class="timeline-header mb-3">
                                        <h6 class="mb-0">{{$user->first_name}} перенс задачу "купить хлеб" в колонку "в работе"</h6>
                                        <small class="text-muted">12 мин назад</small>
                                    </div>

                                    <ul class="list-group list-group-flush">
                                        <li
                                            class="list-group-item d-flex justify-content-between align-items-center flex-wrap border-top-0 p-0">
                                            <div class="d-flex flex-wrap align-items-center">
                                                <ul class="list-unstyled users-list d-flex align-items-center avatar-group m-0 me-2">
                                                    <li
                                                        data-bs-toggle="tooltip"
                                                        data-popup="tooltip-custom"
                                                        data-bs-placement="top"
                                                        title="Ivan"
                                                        class="avatar pull-up">
                                                        <img class="rounded-circle" src="/assets/img/avatars/5.png" alt="Avatar" />
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="timeline-item timeline-item-transparent">
                                <span class="timeline-point timeline-point-success"></span>
                                <div class="timeline-event">
                                    <div class="timeline-header mb-3">
                                        <h6 class="mb-0">Ivan создал задачу "купить хлеб"</h6>
                                        <small class="text-muted">45 мин назад</small>
                                    </div>

                                    <ul class="list-group list-group-flush">
                                        <li
                                            class="list-group-item d-flex justify-content-between align-items-center flex-wrap border-top-0 p-0">
                                            <div class="d-flex flex-wrap align-items-center">
                                                <ul class="list-unstyled users-list d-flex align-items-center avatar-group m-0 me-2">
                                                    <li
                                                        data-bs-toggle="tooltip"
                                                        data-popup="tooltip-custom"
                                                        data-bs-placement="top"
                                                        title="Ivan"
                                                        class="avatar pull-up">
                                                        <img class="rounded-circle" src="/assets/img/avatars/5.png" alt="Avatar" />
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="timeline-item timeline-item-transparent">
                                <span class="timeline-point timeline-point-info"></span>
                                <div class="timeline-event">
                                    <div class="timeline-header mb-3">
                                        <h6 class="mb-0">Ivan пригласил вас в проект "Проект 1"</h6>
                                        <small class="text-muted">2 дня назад</small>
                                    </div>
                                    <p class="mb-2">6 участников в этом проекте</p>
                                    <ul class="list-group list-group-flush">
                                        <li
                                            class="list-group-item d-flex justify-content-between align-items-center flex-wrap border-top-0 p-0">
                                            <div class="d-flex flex-wrap align-items-center">
                                                <ul class="list-unstyled users-list d-flex align-items-center avatar-group m-0 me-2">
                                                    <li
                                                        data-bs-toggle="tooltip"
                                                        data-popup="tooltip-custom"
                                                        data-bs-placement="top"
                                                        title="Vinnie Mostowy"
                                                        class="avatar pull-up">
                                                        <img class="rounded-circle" src="/assets/img/avatars/5.png" alt="Avatar" />
                                                    </li>
                                                    <li
                                                        data-bs-toggle="tooltip"
                                                        data-popup="tooltip-custom"
                                                        data-bs-placement="top"
                                                        title="Allen Rieske"
                                                        class="avatar pull-up">
                                                        <img class="rounded-circle" src="/assets/img/avatars/12.png" alt="Avatar" />
                                                    </li>
                                                    <li
                                                        data-bs-toggle="tooltip"
                                                        data-popup="tooltip-custom"
                                                        data-bs-placement="top"
                                                        title="Julee Rossignol"
                                                        class="avatar pull-up">
                                                        <img class="rounded-circle" src="/assets/img/avatars/6.png" alt="Avatar" />
                                                    </li>
                                                    <li class="avatar">
                                      <span
                                          class="avatar-initial rounded-circle pull-up text-heading"
                                          data-bs-toggle="tooltip"
                                          data-bs-placement="bottom"
                                          title="3 more"
                                      >+3</span
                                      >
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!--/ Weekly Sales Chart-->
            <!-- Activity Timeline -->

            <!-- Activity Timeline -->
        </div>
    </div>
@endsection
