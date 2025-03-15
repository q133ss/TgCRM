<!doctype html>
<html
    lang="en"
    class="light-style layout-navbar-fixed layout-menu-fixed layout-compact"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="/assets/"
    data-template="vertical-menu-template"
    data-style="light">
<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>@yield('title') - TgCRM</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
        rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="/assets/vendor/fonts/remixicon/remixicon.css" />
    <link rel="stylesheet" href="/assets/vendor/fonts/flag-icons.css" />

    <!-- Menu waves for no-customizer fix -->
    <link rel="stylesheet" href="/assets/vendor/libs/node-waves/node-waves.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="/assets/vendor/css/rtl/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="/assets/vendor/css/rtl/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="/assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="/assets/vendor/libs/typeahead-js/typeahead.css" />
    <link rel="stylesheet" href="/assets/vendor/libs/apex-charts/apex-charts.css" />
    <link rel="stylesheet" href="/assets/vendor/libs/swiper/swiper.css" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="/assets/vendor/css/pages/cards-statistics.css" />
    <link rel="stylesheet" href="/assets/vendor/css/pages/cards-analytics.css" />

    <!-- Helpers -->
    <script src="/assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="/assets/vendor/js/template-customizer.js"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="/assets/js/config.js"></script>
    @yield('meta')
</head>

<body>
<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <!-- Menu -->

        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
            <div class="app-brand demo">
                @php $uidLink = request()->uid != null ? '?uid='.request()->uid : ''; @endphp
                <a href="{{route('dashboard.index')}}{{$uidLink}}" class="app-brand-link">
                    <span class="app-brand-text demo menu-text fw-semibold ms-2">TgCRM</span>
                </a>

                <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M8.47365 11.7183C8.11707 12.0749 8.11707 12.6531 8.47365 13.0097L12.071 16.607C12.4615 16.9975 12.4615 17.6305 12.071 18.021C11.6805 18.4115 11.0475 18.4115 10.657 18.021L5.83009 13.1941C5.37164 12.7356 5.37164 11.9924 5.83009 11.5339L10.657 6.707C11.0475 6.31653 11.6805 6.31653 12.071 6.707C12.4615 7.09747 12.4615 7.73053 12.071 8.121L8.47365 11.7183Z"
                            fill-opacity="0.9" />
                        <path
                            d="M14.3584 11.8336C14.0654 12.1266 14.0654 12.6014 14.3584 12.8944L18.071 16.607C18.4615 16.9975 18.4615 17.6305 18.071 18.021C17.6805 18.4115 17.0475 18.4115 16.657 18.021L11.6819 13.0459C11.3053 12.6693 11.3053 12.0587 11.6819 11.6821L16.657 6.707C17.0475 6.31653 17.6805 6.31653 18.071 6.707C18.4615 7.09747 18.4615 7.73053 18.071 8.121L14.3584 11.8336Z"
                            fill-opacity="0.4" />
                    </svg>
                </a>
            </div>

            <div class="menu-inner-shadow"></div>

            <ul class="menu-inner py-1">


                <li class="menu-item">
                    <a href="{{route('dashboard.index')}}{{$uidLink}}" class="menu-link">
                        <i class="menu-icon tf-icons ri-home-5-line"></i>
                        <div data-i18n="Главная">Главная</div>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{route('dashboard.calendar')}}{{$uidLink}}" class="menu-link">
                        <i class="menu-icon tf-icons ri-calendar-2-line"></i>
                        <div data-i18n="Календарь">Календарь</div>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{route('dashboard.tasks')}}{{$uidLink}}" class="menu-link">
                        <i class="menu-icon tf-icons ri-task-line"></i>
                        <div data-i18n="Мои задачи">Мои задачи</div>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{route('dashboard.projects')}}{{$uidLink}}" class="menu-link">
                        <i class="menu-icon tf-icons ri-list-check"></i>
                        <div data-i18n="Мои проекты">Мои проекты</div>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{route('dashboard.users')}}{{$uidLink}}" class="menu-link">
                        <i class="menu-icon tf-icons ri-group-line""></i>
                        <div data-i18n="Пользователи">Пользователи</div>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{route('dashboard.faq')}}{{$uidLink}}" class="menu-link">
                        <i class="menu-icon tf-icons ri-question-line"></i>
                        <div data-i18n="FAQ">FAQ</div>
                    </a>
                </li>
            </ul>
        </aside>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
            <!-- Navbar -->

{{--            <nav--}}
{{--                class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"--}}
{{--                id="layout-navbar">--}}
{{--                <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">--}}
{{--                    <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">--}}
{{--                        <i class="ri-menu-fill ri-22px"></i>--}}
{{--                    </a>--}}
{{--                </div>--}}

{{--                <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">--}}
{{--                    <!-- Search -->--}}
{{--                    <div class="navbar-nav align-items-center">--}}
{{--                        <div class="nav-item navbar-search-wrapper mb-0">--}}
{{--                            <a class="nav-item nav-link search-toggler fw-normal px-0" href="javascript:void(0);">--}}
{{--                                <i class="ri-search-line ri-22px scaleX-n1-rtl me-3"></i>--}}
{{--                                <span class="d-none d-md-inline-block text-muted">Search (Ctrl+/)</span>--}}
{{--                            </a>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <!-- /Search -->--}}

{{--                    <ul class="navbar-nav flex-row align-items-center ms-auto">--}}
{{--                        <!-- Language -->--}}
{{--                        <li class="nav-item dropdown-language dropdown">--}}
{{--                            <a--}}
{{--                                class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"--}}
{{--                                href="javascript:void(0);"--}}
{{--                                data-bs-toggle="dropdown">--}}
{{--                                <i class="ri-translate-2 ri-22px"></i>--}}
{{--                            </a>--}}
{{--                            <ul class="dropdown-menu dropdown-menu-end">--}}
{{--                                <li>--}}
{{--                                    <a class="dropdown-item" href="javascript:void(0);" data-language="en" data-text-direction="ltr">--}}
{{--                                        <span class="align-middle">English</span>--}}
{{--                                    </a>--}}
{{--                                </li>--}}
{{--                                <li>--}}
{{--                                    <a class="dropdown-item" href="javascript:void(0);" data-language="fr" data-text-direction="ltr">--}}
{{--                                        <span class="align-middle">French</span>--}}
{{--                                    </a>--}}
{{--                                </li>--}}
{{--                                <li>--}}
{{--                                    <a class="dropdown-item" href="javascript:void(0);" data-language="ar" data-text-direction="rtl">--}}
{{--                                        <span class="align-middle">Arabic</span>--}}
{{--                                    </a>--}}
{{--                                </li>--}}
{{--                                <li>--}}
{{--                                    <a class="dropdown-item" href="javascript:void(0);" data-language="de" data-text-direction="ltr">--}}
{{--                                        <span class="align-middle">German</span>--}}
{{--                                    </a>--}}
{{--                                </li>--}}
{{--                            </ul>--}}
{{--                        </li>--}}
{{--                        <!--/ Language -->--}}

{{--                        <!-- Style Switcher -->--}}
{{--                        <li class="nav-item dropdown-style-switcher dropdown me-1 me-xl-0">--}}
{{--                            <a--}}
{{--                                class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"--}}
{{--                                href="javascript:void(0);"--}}
{{--                                data-bs-toggle="dropdown">--}}
{{--                                <i class="ri-22px"></i>--}}
{{--                            </a>--}}
{{--                            <ul class="dropdown-menu dropdown-menu-end dropdown-styles">--}}
{{--                                <li>--}}
{{--                                    <a class="dropdown-item" href="javascript:void(0);" data-theme="light">--}}
{{--                                        <span class="align-middle"><i class="ri-sun-line ri-22px me-3"></i>Light</span>--}}
{{--                                    </a>--}}
{{--                                </li>--}}
{{--                                <li>--}}
{{--                                    <a class="dropdown-item" href="javascript:void(0);" data-theme="dark">--}}
{{--                                        <span class="align-middle"><i class="ri-moon-clear-line ri-22px me-3"></i>Dark</span>--}}
{{--                                    </a>--}}
{{--                                </li>--}}
{{--                                <li>--}}
{{--                                    <a class="dropdown-item" href="javascript:void(0);" data-theme="system">--}}
{{--                                        <span class="align-middle"><i class="ri-computer-line ri-22px me-3"></i>System</span>--}}
{{--                                    </a>--}}
{{--                                </li>--}}
{{--                            </ul>--}}
{{--                        </li>--}}
{{--                        <!-- / Style Switcher-->--}}

{{--                        <!-- Quick links  -->--}}
{{--                        <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown me-1 me-xl-0">--}}
{{--                            <a--}}
{{--                                class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"--}}
{{--                                href="javascript:void(0);"--}}
{{--                                data-bs-toggle="dropdown"--}}
{{--                                data-bs-auto-close="outside"--}}
{{--                                aria-expanded="false">--}}
{{--                                <i class="ri-star-smile-line ri-22px"></i>--}}
{{--                            </a>--}}
{{--                            <div class="dropdown-menu dropdown-menu-end py-0">--}}
{{--                                <div class="dropdown-menu-header border-bottom py-50">--}}
{{--                                    <div class="dropdown-header d-flex align-items-center py-2">--}}
{{--                                        <h6 class="mb-0 me-auto">Shortcuts</h6>--}}
{{--                                        <a--}}
{{--                                            href="javascript:void(0)"--}}
{{--                                            class="btn btn-text-secondary rounded-pill btn-icon dropdown-shortcuts-add text-heading"--}}
{{--                                            data-bs-toggle="tooltip"--}}
{{--                                            data-bs-placement="top"--}}
{{--                                            title="Add shortcuts"--}}
{{--                                        ><i class="ri-add-line ri-24px"></i--}}
{{--                                            ></a>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="dropdown-shortcuts-list scrollable-container">--}}
{{--                                    <div class="row row-bordered overflow-visible g-0">--}}
{{--                                        <div class="dropdown-shortcuts-item col">--}}
{{--                          <span class="dropdown-shortcuts-icon rounded-circle mb-3">--}}
{{--                            <i class="ri-calendar-line ri-26px text-heading"></i>--}}
{{--                          </span>--}}
{{--                                            <a href="app-calendar.html" class="stretched-link">Calendar</a>--}}
{{--                                            <small class="mb-0">Appointments</small>--}}
{{--                                        </div>--}}
{{--                                        <div class="dropdown-shortcuts-item col">--}}
{{--                          <span class="dropdown-shortcuts-icon rounded-circle mb-3">--}}
{{--                            <i class="ri-file-text-line ri-26px text-heading"></i>--}}
{{--                          </span>--}}
{{--                                            <a href="app-invoice-list.html" class="stretched-link">Invoice App</a>--}}
{{--                                            <small class="mb-0">Manage Accounts</small>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="row row-bordered overflow-visible g-0">--}}
{{--                                        <div class="dropdown-shortcuts-item col">--}}
{{--                          <span class="dropdown-shortcuts-icon rounded-circle mb-3">--}}
{{--                            <i class="ri-user-line ri-26px text-heading"></i>--}}
{{--                          </span>--}}
{{--                                            <a href="app-user-list.html" class="stretched-link">User App</a>--}}
{{--                                            <small class="mb-0">Manage Users</small>--}}
{{--                                        </div>--}}
{{--                                        <div class="dropdown-shortcuts-item col">--}}
{{--                          <span class="dropdown-shortcuts-icon rounded-circle mb-3">--}}
{{--                            <i class="ri-computer-line ri-26px text-heading"></i>--}}
{{--                          </span>--}}
{{--                                            <a href="app-access-roles.html" class="stretched-link">Role Management</a>--}}
{{--                                            <small class="mb-0">Permission</small>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="row row-bordered overflow-visible g-0">--}}
{{--                                        <div class="dropdown-shortcuts-item col">--}}
{{--                          <span class="dropdown-shortcuts-icon rounded-circle mb-3">--}}
{{--                            <i class="ri-pie-chart-2-line ri-26px text-heading"></i>--}}
{{--                          </span>--}}
{{--                                            <a href="index.html" class="stretched-link">Dashboard</a>--}}
{{--                                            <small class="mb-0">Analytics</small>--}}
{{--                                        </div>--}}
{{--                                        <div class="dropdown-shortcuts-item col">--}}
{{--                          <span class="dropdown-shortcuts-icon rounded-circle mb-3">--}}
{{--                            <i class="ri-settings-4-line ri-26px text-heading"></i>--}}
{{--                          </span>--}}
{{--                                            <a href="pages-account-settings-account.html" class="stretched-link">Setting</a>--}}
{{--                                            <small class="mb-0">Account Settings</small>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="row row-bordered overflow-visible g-0">--}}
{{--                                        <div class="dropdown-shortcuts-item col">--}}
{{--                          <span class="dropdown-shortcuts-icon rounded-circle mb-3">--}}
{{--                            <i class="ri-question-line ri-26px text-heading"></i>--}}
{{--                          </span>--}}
{{--                                            <a href="pages-faq.html" class="stretched-link">FAQs</a>--}}
{{--                                            <small class="mb-0">FAQs & Articles</small>--}}
{{--                                        </div>--}}
{{--                                        <div class="dropdown-shortcuts-item col">--}}
{{--                          <span class="dropdown-shortcuts-icon rounded-circle mb-3">--}}
{{--                            <i class="ri-tv-2-line ri-26px text-heading"></i>--}}
{{--                          </span>--}}
{{--                                            <a href="modal-examples.html" class="stretched-link">Modals</a>--}}
{{--                                            <small class="mb-0">Useful Popups</small>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </li>--}}
{{--                        <!-- Quick links -->--}}

{{--                        <!-- Notification -->--}}
{{--                        <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-4 me-xl-1">--}}
{{--                            <a--}}
{{--                                class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"--}}
{{--                                href="javascript:void(0);"--}}
{{--                                data-bs-toggle="dropdown"--}}
{{--                                data-bs-auto-close="outside"--}}
{{--                                aria-expanded="false">--}}
{{--                                <i class="ri-notification-2-line ri-22px"></i>--}}
{{--                                <span--}}
{{--                                    class="position-absolute top-0 start-50 translate-middle-y badge badge-dot bg-danger mt-2 border"></span>--}}
{{--                            </a>--}}
{{--                            <ul class="dropdown-menu dropdown-menu-end py-0">--}}
{{--                                <li class="dropdown-menu-header border-bottom py-50">--}}
{{--                                    <div class="dropdown-header d-flex align-items-center py-2">--}}
{{--                                        <h6 class="mb-0 me-auto">Notification</h6>--}}
{{--                                        <div class="d-flex align-items-center">--}}
{{--                                            <span class="badge rounded-pill bg-label-primary fs-xsmall me-2">8 New</span>--}}
{{--                                            <a--}}
{{--                                                href="javascript:void(0)"--}}
{{--                                                class="btn btn-text-secondary rounded-pill btn-icon dropdown-notifications-all"--}}
{{--                                                data-bs-toggle="tooltip"--}}
{{--                                                data-bs-placement="top"--}}
{{--                                                title="Mark all as read"--}}
{{--                                            ><i class="ri-mail-open-line text-heading ri-20px"></i--}}
{{--                                                ></a>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </li>--}}
{{--                                <li class="dropdown-notifications-list scrollable-container">--}}
{{--                                    <ul class="list-group list-group-flush">--}}
{{--                                        <li class="list-group-item list-group-item-action dropdown-notifications-item">--}}
{{--                                            <div class="d-flex">--}}
{{--                                                <div class="flex-shrink-0 me-3">--}}
{{--                                                    <div class="avatar">--}}
{{--                                                        <img src="/assets/img/avatars/1.png" alt class="rounded-circle" />--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                                <div class="flex-grow-1">--}}
{{--                                                    <h6 class="small mb-1">Congratulation Lettie 🎉</h6>--}}
{{--                                                    <small class="mb-1 d-block text-body">Won the monthly best seller gold badge</small>--}}
{{--                                                    <small class="text-muted">1h ago</small>--}}
{{--                                                </div>--}}
{{--                                                <div class="flex-shrink-0 dropdown-notifications-actions">--}}
{{--                                                    <a href="javascript:void(0)" class="dropdown-notifications-read"--}}
{{--                                                    ><span class="badge badge-dot"></span--}}
{{--                                                        ></a>--}}
{{--                                                    <a href="javascript:void(0)" class="dropdown-notifications-archive"--}}
{{--                                                    ><span class="ri-close-line ri-20px"></span--}}
{{--                                                        ></a>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </li>--}}
{{--                                        <li class="list-group-item list-group-item-action dropdown-notifications-item">--}}
{{--                                            <div class="d-flex">--}}
{{--                                                <div class="flex-shrink-0 me-3">--}}
{{--                                                    <div class="avatar">--}}
{{--                                                        <span class="avatar-initial rounded-circle bg-label-danger">CF</span>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                                <div class="flex-grow-1">--}}
{{--                                                    <h6 class="mb-1 small">Charles Franklin</h6>--}}
{{--                                                    <small class="mb-1 d-block text-body">Accepted your connection</small>--}}
{{--                                                    <small class="text-muted">12hr ago</small>--}}
{{--                                                </div>--}}
{{--                                                <div class="flex-shrink-0 dropdown-notifications-actions">--}}
{{--                                                    <a href="javascript:void(0)" class="dropdown-notifications-read"--}}
{{--                                                    ><span class="badge badge-dot"></span--}}
{{--                                                        ></a>--}}
{{--                                                    <a href="javascript:void(0)" class="dropdown-notifications-archive"--}}
{{--                                                    ><span class="ri-close-line ri-20px"></span--}}
{{--                                                        ></a>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </li>--}}
{{--                                        <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">--}}
{{--                                            <div class="d-flex">--}}
{{--                                                <div class="flex-shrink-0 me-3">--}}
{{--                                                    <div class="avatar">--}}
{{--                                                        <img src="/assets/img/avatars/2.png" alt class="rounded-circle" />--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                                <div class="flex-grow-1">--}}
{{--                                                    <h6 class="mb-1 small">New Message ✉️</h6>--}}
{{--                                                    <small class="mb-1 d-block text-body">You have new message from Natalie</small>--}}
{{--                                                    <small class="text-muted">1h ago</small>--}}
{{--                                                </div>--}}
{{--                                                <div class="flex-shrink-0 dropdown-notifications-actions">--}}
{{--                                                    <a href="javascript:void(0)" class="dropdown-notifications-read"--}}
{{--                                                    ><span class="badge badge-dot"></span--}}
{{--                                                        ></a>--}}
{{--                                                    <a href="javascript:void(0)" class="dropdown-notifications-archive"--}}
{{--                                                    ><span class="ri-close-line ri-20px"></span--}}
{{--                                                        ></a>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </li>--}}
{{--                                        <li class="list-group-item list-group-item-action dropdown-notifications-item">--}}
{{--                                            <div class="d-flex">--}}
{{--                                                <div class="flex-shrink-0 me-3">--}}
{{--                                                    <div class="avatar">--}}
{{--                                <span class="avatar-initial rounded-circle bg-label-success"--}}
{{--                                ><i class="ri-shopping-cart-2-line"></i--}}
{{--                                    ></span>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                                <div class="flex-grow-1">--}}
{{--                                                    <h6 class="mb-1 small">Whoo! You have new order 🛒</h6>--}}
{{--                                                    <small class="mb-1 d-block text-body">ACME Inc. made new order $1,154</small>--}}
{{--                                                    <small class="text-muted">1 day ago</small>--}}
{{--                                                </div>--}}
{{--                                                <div class="flex-shrink-0 dropdown-notifications-actions">--}}
{{--                                                    <a href="javascript:void(0)" class="dropdown-notifications-read"--}}
{{--                                                    ><span class="badge badge-dot"></span--}}
{{--                                                        ></a>--}}
{{--                                                    <a href="javascript:void(0)" class="dropdown-notifications-archive"--}}
{{--                                                    ><span class="ri-close-line ri-20px"></span--}}
{{--                                                        ></a>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </li>--}}
{{--                                        <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">--}}
{{--                                            <div class="d-flex">--}}
{{--                                                <div class="flex-shrink-0 me-3">--}}
{{--                                                    <div class="avatar">--}}
{{--                                                        <img src="/assets/img/avatars/9.png" alt class="rounded-circle" />--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                                <div class="flex-grow-1">--}}
{{--                                                    <h6 class="mb-1 small">Application has been approved 🚀</h6>--}}
{{--                                                    <small class="mb-1 d-block text-body"--}}
{{--                                                    >Your ABC project application has been approved.</small--}}
{{--                                                    >--}}
{{--                                                    <small class="text-muted">2 days ago</small>--}}
{{--                                                </div>--}}
{{--                                                <div class="flex-shrink-0 dropdown-notifications-actions">--}}
{{--                                                    <a href="javascript:void(0)" class="dropdown-notifications-read"--}}
{{--                                                    ><span class="badge badge-dot"></span--}}
{{--                                                        ></a>--}}
{{--                                                    <a href="javascript:void(0)" class="dropdown-notifications-archive"--}}
{{--                                                    ><span class="ri-close-line ri-20px"></span--}}
{{--                                                        ></a>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </li>--}}
{{--                                        <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">--}}
{{--                                            <div class="d-flex">--}}
{{--                                                <div class="flex-shrink-0 me-3">--}}
{{--                                                    <div class="avatar">--}}
{{--                                <span class="avatar-initial rounded-circle bg-label-success"--}}
{{--                                ><i class="ri-pie-chart-2-line"></i--}}
{{--                                    ></span>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                                <div class="flex-grow-1">--}}
{{--                                                    <h6 class="mb-1 small">Monthly report is generated</h6>--}}
{{--                                                    <small class="mb-1 d-block text-body">July monthly financial report is generated </small>--}}
{{--                                                    <small class="text-muted">3 days ago</small>--}}
{{--                                                </div>--}}
{{--                                                <div class="flex-shrink-0 dropdown-notifications-actions">--}}
{{--                                                    <a href="javascript:void(0)" class="dropdown-notifications-read"--}}
{{--                                                    ><span class="badge badge-dot"></span--}}
{{--                                                        ></a>--}}
{{--                                                    <a href="javascript:void(0)" class="dropdown-notifications-archive"--}}
{{--                                                    ><span class="ri-close-line ri-20px"></span--}}
{{--                                                        ></a>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </li>--}}
{{--                                        <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">--}}
{{--                                            <div class="d-flex">--}}
{{--                                                <div class="flex-shrink-0 me-3">--}}
{{--                                                    <div class="avatar">--}}
{{--                                                        <img src="/assets/img/avatars/5.png" alt class="rounded-circle" />--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                                <div class="flex-grow-1">--}}
{{--                                                    <h6 class="mb-1 small">Send connection request</h6>--}}
{{--                                                    <small class="mb-1 d-block text-body">Peter sent you connection request</small>--}}
{{--                                                    <small class="text-muted">4 days ago</small>--}}
{{--                                                </div>--}}
{{--                                                <div class="flex-shrink-0 dropdown-notifications-actions">--}}
{{--                                                    <a href="javascript:void(0)" class="dropdown-notifications-read"--}}
{{--                                                    ><span class="badge badge-dot"></span--}}
{{--                                                        ></a>--}}
{{--                                                    <a href="javascript:void(0)" class="dropdown-notifications-archive"--}}
{{--                                                    ><span class="ri-close-line ri-20px"></span--}}
{{--                                                        ></a>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </li>--}}
{{--                                        <li class="list-group-item list-group-item-action dropdown-notifications-item">--}}
{{--                                            <div class="d-flex">--}}
{{--                                                <div class="flex-shrink-0 me-3">--}}
{{--                                                    <div class="avatar">--}}
{{--                                                        <img src="/assets/img/avatars/6.png" alt class="rounded-circle" />--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                                <div class="flex-grow-1">--}}
{{--                                                    <h6 class="mb-1 small">New message from Jane</h6>--}}
{{--                                                    <small class="mb-1 d-block text-body">Your have new message from Jane</small>--}}
{{--                                                    <small class="text-muted">5 days ago</small>--}}
{{--                                                </div>--}}
{{--                                                <div class="flex-shrink-0 dropdown-notifications-actions">--}}
{{--                                                    <a href="javascript:void(0)" class="dropdown-notifications-read"--}}
{{--                                                    ><span class="badge badge-dot"></span--}}
{{--                                                        ></a>--}}
{{--                                                    <a href="javascript:void(0)" class="dropdown-notifications-archive"--}}
{{--                                                    ><span class="ri-close-line ri-20px"></span--}}
{{--                                                        ></a>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </li>--}}
{{--                                        <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">--}}
{{--                                            <div class="d-flex">--}}
{{--                                                <div class="flex-shrink-0 me-3">--}}
{{--                                                    <div class="avatar">--}}
{{--                                <span class="avatar-initial rounded-circle bg-label-warning"--}}
{{--                                ><i class="ri-error-warning-line"></i--}}
{{--                                    ></span>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                                <div class="flex-grow-1">--}}
{{--                                                    <h6 class="mb-1 small">CPU is running high</h6>--}}
{{--                                                    <small class="mb-1 d-block text-body"--}}
{{--                                                    >CPU Utilization Percent is currently at 88.63%,</small--}}
{{--                                                    >--}}
{{--                                                    <small class="text-muted">5 days ago</small>--}}
{{--                                                </div>--}}
{{--                                                <div class="flex-shrink-0 dropdown-notifications-actions">--}}
{{--                                                    <a href="javascript:void(0)" class="dropdown-notifications-read"--}}
{{--                                                    ><span class="badge badge-dot"></span--}}
{{--                                                        ></a>--}}
{{--                                                    <a href="javascript:void(0)" class="dropdown-notifications-archive"--}}
{{--                                                    ><span class="ri-close-line ri-20px"></span--}}
{{--                                                        ></a>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </li>--}}
{{--                                    </ul>--}}
{{--                                </li>--}}
{{--                                <li class="border-top">--}}
{{--                                    <div class="d-grid p-4">--}}
{{--                                        <a class="btn btn-primary btn-sm d-flex" href="javascript:void(0);">--}}
{{--                                            <small class="align-middle">View all notifications</small>--}}
{{--                                        </a>--}}
{{--                                    </div>--}}
{{--                                </li>--}}
{{--                            </ul>--}}
{{--                        </li>--}}
{{--                        <!--/ Notification -->--}}

{{--                        <!-- User -->--}}
{{--                        <li class="nav-item navbar-dropdown dropdown-user dropdown">--}}
{{--                            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">--}}
{{--                                <div class="avatar avatar-online">--}}
{{--                                    <img src="/assets/img/avatars/1.png" alt class="rounded-circle" />--}}
{{--                                </div>--}}
{{--                            </a>--}}
{{--                            <ul class="dropdown-menu dropdown-menu-end">--}}
{{--                                <li>--}}
{{--                                    <a class="dropdown-item" href="pages-account-settings-account.html">--}}
{{--                                        <div class="d-flex">--}}
{{--                                            <div class="flex-shrink-0 me-2">--}}
{{--                                                <div class="avatar avatar-online">--}}
{{--                                                    <img src="/assets/img/avatars/1.png" alt class="rounded-circle" />--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                            <div class="flex-grow-1">--}}
{{--                                                <span class="fw-medium d-block small">John Doe</span>--}}
{{--                                                <small class="text-muted">Admin</small>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </a>--}}
{{--                                </li>--}}
{{--                                <li>--}}
{{--                                    <div class="dropdown-divider"></div>--}}
{{--                                </li>--}}
{{--                                <li>--}}
{{--                                    <a class="dropdown-item" href="pages-profile-user.html">--}}
{{--                                        <i class="ri-user-3-line ri-22px me-3"></i><span class="align-middle">My Profile</span>--}}
{{--                                    </a>--}}
{{--                                </li>--}}
{{--                                <li>--}}
{{--                                    <a class="dropdown-item" href="pages-account-settings-account.html">--}}
{{--                                        <i class="ri-settings-4-line ri-22px me-3"></i><span class="align-middle">Settings</span>--}}
{{--                                    </a>--}}
{{--                                </li>--}}
{{--                                <li>--}}
{{--                                    <a class="dropdown-item" href="pages-account-settings-billing.html">--}}
{{--                        <span class="d-flex align-items-center align-middle">--}}
{{--                          <i class="flex-shrink-0 ri-file-text-line ri-22px me-3"></i>--}}
{{--                          <span class="flex-grow-1 align-middle">Billing</span>--}}
{{--                          <span class="flex-shrink-0 badge badge-center rounded-pill bg-danger">4</span>--}}
{{--                        </span>--}}
{{--                                    </a>--}}
{{--                                </li>--}}
{{--                                <li>--}}
{{--                                    <div class="dropdown-divider"></div>--}}
{{--                                </li>--}}
{{--                                <li>--}}
{{--                                    <a class="dropdown-item" href="pages-pricing.html">--}}
{{--                                        <i class="ri-money-dollar-circle-line ri-22px me-3"></i--}}
{{--                                        ><span class="align-middle">Pricing</span>--}}
{{--                                    </a>--}}
{{--                                </li>--}}
{{--                                <li>--}}
{{--                                    <a class="dropdown-item" href="pages-faq.html">--}}
{{--                                        <i class="ri-question-line ri-22px me-3"></i><span class="align-middle">FAQ</span>--}}
{{--                                    </a>--}}
{{--                                </li>--}}
{{--                                <li>--}}
{{--                                    <div class="d-grid px-4 pt-2 pb-1">--}}
{{--                                        <a class="btn btn-sm btn-danger d-flex" href="auth-login-cover.html" target="_blank">--}}
{{--                                            <small class="align-middle">Logout</small>--}}
{{--                                            <i class="ri-logout-box-r-line ms-2 ri-16px"></i>--}}
{{--                                        </a>--}}
{{--                                    </div>--}}
{{--                                </li>--}}
{{--                            </ul>--}}
{{--                        </li>--}}
{{--                        <!--/ User -->--}}
{{--                    </ul>--}}
{{--                </div>--}}

{{--                <!-- Search Small Screens -->--}}
{{--                <div class="navbar-search-wrapper search-input-wrapper d-none">--}}
{{--                    <input--}}
{{--                        type="text"--}}
{{--                        class="form-control search-input container-xxl border-0"--}}
{{--                        placeholder="Search..."--}}
{{--                        aria-label="Search..." />--}}
{{--                    <i class="ri-close-fill search-toggler cursor-pointer"></i>--}}
{{--                </div>--}}
{{--            </nav>--}}

            <!-- / Navbar -->

            <!-- Content wrapper -->
            <div class="content-wrapper">
                <!-- Content -->
                @yield('content')
                <!-- / Content -->

                <!-- Footer -->
                <footer class="content-footer footer bg-footer-theme">
                    <div class="container-xxl">
                        <div
                            class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
                            <div class="text-body mb-2 mb-md-0">
                                © Copyright
                                <script>
                                    document.write(new Date().getFullYear());
                                </script>
                            </div>
                            <div class="d-none d-lg-inline-block">
                                <a href="#" class="footer-link me-4" target="_blank"
                                >Политика конфиденциальности</a
                                >
                                <a href="#" target="_blank" class="footer-link me-4"
                                >Пользовательское соглашение</a
                                >
                            </div>
                        </div>
                    </div>
                </footer>
                <!-- / Footer -->

                <div class="content-backdrop fade"></div>
            </div>
            <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>

    <!-- Drag Target Area To SlideIn Menu On Small Screens -->
    <div class="drag-target"></div>
</div>
<!-- / Layout wrapper -->

<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->
<script src="/assets/vendor/libs/jquery/jquery.js"></script>
<script src="/assets/vendor/libs/popper/popper.js"></script>
<script src="/assets/vendor/js/bootstrap.js"></script>
<script src="/assets/vendor/libs/node-waves/node-waves.js"></script>
<script src="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="/assets/vendor/libs/hammer/hammer.js"></script>
<script src="/assets/vendor/libs/i18n/i18n.js"></script>
<script src="/assets/vendor/libs/typeahead-js/typeahead.js"></script>
<script src="/assets/vendor/js/menu.js"></script>

<!-- endbuild -->

<!-- Vendors JS -->
<script src="/assets/vendor/libs/apex-charts/apexcharts.js"></script>
<script src="/assets/vendor/libs/swiper/swiper.js"></script>

<!-- Main JS -->
<script src="/assets/js/main.js"></script>

@yield('scripts')
</body>
</html>
