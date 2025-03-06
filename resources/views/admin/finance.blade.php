@extends('layouts.admin')
@section('title', 'Финансы')
@section('meta')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection
@section('content')
    <!-- Основной контент -->
    <div class="col-md-10 main-content">
        <h1 class="mt-3">Финансы</h1>

        <!-- Блоки с доходами -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Доходы за месяц</h5>
                        <p class="card-text display-4">150.000 ₽</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Доходы за день</h5>
                        <p class="card-text display-4">50.000 ₽</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Доходы за все время</h5>
                        <p class="card-text display-4">150.000 ₽</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Информация о пользователях и подписках -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Всего пользователей</h5>
                        <p class="card-text display-4">1,250</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Всего активных подписок</h5>
                        <p class="card-text display-4">850</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- График -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">График доходов</h5>
                        <canvas id="incomeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Таблица транзакций -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Список транзакций</h5>
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Дата</th>
                                <th>Сумма ₽</th>
                                <th>Тип</th>
                                <th>Пользователь</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>1</td>
                                <td>2023-10-01 18:30</td>
                                <td>2.000</td>
                                <td><span class="badge bg-success">Оплата подписки</span></td>
                                <td>Иван Иванов</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>2023-10-02 18:30</td>
                                <td>1.500</td>
                                <td><span class="badge bg-danger">Оплата подписки</span></td>
                                <td>Петр Петров</td>
                            </tr>
                            <!-- Добавьте больше строк по мере необходимости -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        const ctx = document.getElementById('incomeChart').getContext('2d');
        const incomeChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт'],
                datasets: [{
                    label: 'Доходы',
                    data: [5000, 7000, 8000, 9000, 10000, 12000, 11000, 13000, 14000, 15000],
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endsection
