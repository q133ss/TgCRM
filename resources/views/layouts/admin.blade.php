<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ панель</title>
    <!-- Подключаем Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Подключаем иконки Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Стили для бокового меню */
        .sidebar {
            height: 100vh;
            background: linear-gradient(180deg, #4e73df, #224abe);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .sidebar .nav-link {
            color: #fff;
            padding: 10px 15px;
            margin: 5px 0;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }
        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            font-weight: bold;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
        }
        /* Стили для основного контента */
        .main-content {
            padding: 20px;
        }
        .table {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .badge {
            font-size: 0.9em;
            padding: 0.5em 0.75em;
        }
    </style>
    @yield('meta')
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Боковое меню -->
        <div class="col-md-2 sidebar">
            <div class="sidebar-sticky">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.users')}}">
                            <i class="bi bi-people"></i> Пользователи
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.finance')}}">
                            <i class="bi bi-cash"></i> Финансы
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        @yield('content')
    </div>
</div>
<!-- Подключаем Bootstrap JS и зависимости -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
@yield('scripts')
</body>
</html>
