<?php
// Начинаем буферизацию
ob_start();

// Получаем текущий URL
$current_url = $_SERVER['REQUEST_URI'];

// Если URL содержит /api/, обрабатываем API запрос
if (strpos($current_url, '/api/') !== false) {
    // Определяем endpoint
    $parts = explode('/', trim($current_url, '/'));
    
    // Ищем позицию 'api' в массиве
    $api_index = array_search('api', $parts);
    
    if ($api_index !== false && isset($parts[$api_index + 1])) {
        $endpoint = $parts[$api_index + 1];
        
        // Подключаем заголовки
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        
        // Подключаем файл API
        $api_file = __DIR__ . '/api/' . $endpoint . '.php';
        
        if (file_exists($api_file)) {
            // Подключаем базу данных
            require_once __DIR__ . '/config/database.php';
            
            // Подключаем API файл
            include $api_file;
        } else {
            http_response_code(404);
            echo json_encode(["error" => "API endpoint '$endpoint' не найден"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Некорректный API запрос"]);
    }
} else {
    // Показываем красивый интерфейс с таблицами
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Comfort Travel - Административная панель</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
                background: #f5f5f5;
                color: #333;
                line-height: 1.6;
            }

            .container {
                max-width: 1400px;
                margin: 0 auto;
                padding: 20px;
            }

            .header {
                background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
                color: white;
                padding: 30px;
                border-radius: 15px;
                margin-bottom: 30px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            }

            .logo {
                display: flex;
                align-items: center;
                gap: 15px;
                margin-bottom: 20px;
            }

            .logo i {
                font-size: 2.5rem;
            }

            h1 {
                font-size: 2.2rem;
                margin-bottom: 10px;
            }

            .subtitle {
                font-size: 1.1rem;
                opacity: 0.9;
            }

            .status-badge {
                display: inline-block;
                background: rgba(255,255,255,0.2);
                padding: 8px 20px;
                border-radius: 50px;
                margin-top: 20px;
                font-weight: 500;
            }

            .tabs {
                display: flex;
                gap: 10px;
                margin-bottom: 30px;
                background: white;
                padding: 10px;
                border-radius: 10px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            }

            .tab {
                padding: 12px 25px;
                cursor: pointer;
                border-radius: 8px;
                font-weight: 500;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .tab:hover {
                background: #f8fafc;
            }

            .tab.active {
                background: #4f46e5;
                color: white;
            }

            .tab-content {
                display: none;
            }

            .tab-content.active {
                display: block;
            }

            .card {
                background: white;
                border-radius: 15px;
                padding: 25px;
                margin-bottom: 25px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            }

            .card-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 25px;
            }

            .card-title {
                font-size: 1.4rem;
                font-weight: 600;
                color: #1e293b;
            }

            .btn {
                background: #4f46e5;
                color: white;
                border: none;
                padding: 12px 24px;
                border-radius: 8px;
                cursor: pointer;
                font-weight: 500;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .btn:hover {
                background: #4338ca;
                transform: translateY(-1px);
            }

            .btn-refresh {
                background: #10b981;
            }

            .btn-refresh:hover {
                background: #0da271;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }

            th {
                background: #f8fafc;
                padding: 15px;
                text-align: left;
                font-weight: 600;
                color: #475569;
                border-bottom: 2px solid #e2e8f0;
            }

            td {
                padding: 15px;
                border-bottom: 1px solid #e2e8f0;
            }

            tr:hover {
                background: #f8fafc;
            }

            .badge {
                display: inline-block;
                padding: 5px 12px;
                border-radius: 50px;
                font-size: 0.85rem;
                font-weight: 500;
            }

            .badge.success {
                background: #d1fae5;
                color: #065f46;
            }

            .badge.warning {
                background: #fef3c7;
                color: #92400e;
            }

            .badge.danger {
                background: #fee2e2;
                color: #991b1b;
            }

            .badge.info {
                background: #dbeafe;
                color: #1e40af;
            }

            .stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
                margin-bottom: 30px;
            }

            .stat-card {
                background: white;
                padding: 25px;
                border-radius: 15px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.05);
                text-align: center;
            }

            .stat-number {
                font-size: 2.5rem;
                font-weight: 700;
                color: #4f46e5;
                margin: 10px 0;
            }

            .stat-label {
                color: #64748b;
                font-size: 0.95rem;
            }

            .loading {
                text-align: center;
                padding: 40px;
                color: #64748b;
            }

            .error-message {
                background: #fee2e2;
                color: #991b1b;
                padding: 15px;
                border-radius: 8px;
                margin: 20px 0;
                display: none;
            }

            .footer {
                text-align: center;
                margin-top: 40px;
                padding-top: 20px;
                border-top: 1px solid #e2e8f0;
                color: #64748b;
                font-size: 0.9rem;
            }

            .visa-badge {
                display: inline-block;
                padding: 4px 10px;
                border-radius: 4px;
                font-size: 0.8rem;
                font-weight: 500;
            }

            .visa-required {
                background: #fee2e2;
                color: #991b1b;
            }

            .visa-free {
                background: #d1fae5;
                color: #065f46;
            }

            .price {
                font-weight: 600;
                color: #059669;
            }

            .status-select {
                padding: 6px 12px;
                border-radius: 6px;
                border: 1px solid #d1d5db;
                font-size: 0.9rem;
            }

            @media (max-width: 768px) {
                .tabs {
                    flex-direction: column;
                }
                
                .container {
                    padding: 10px;
                }
                
                table {
                    display: block;
                    overflow-x: auto;
                }
                
                .stats-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <div class="logo">
                    <i class="fas fa-plane"></i>
                </div>
                <h1>Comfort Travel</h1>
                <div class="subtitle">Административная панель турагентства</div>
                <div class="status-badge">
                    <i class="fas fa-check-circle"></i> Система работает стабильно
                </div>
            </div>

            <div class="tabs">
                <div class="tab active" onclick="switchTab('countries')">
                    <i class="fas fa-globe"></i> Страны
                </div>
                <div class="tab" onclick="switchTab('clients')">
                    <i class="fas fa-users"></i> Клиенты
                </div>
                <div class="tab" onclick="switchTab('tours')">
                    <i class="fas fa-map-marked-alt"></i> Туры
                </div>
                <div class="tab" onclick="switchTab('bookings')">
                    <i class="fas fa-calendar-check"></i> Бронирования
                </div>
                <div class="tab" onclick="switchTab('stats')">
                    <i class="fas fa-chart-bar"></i> Статистика
                </div>
            </div>

            <!-- Сообщение об ошибке -->
            <div class="error-message" id="errorMessage"></div>

            <!-- Вкладка стран -->
            <div class="tab-content active" id="countries-tab">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fas fa-globe-americas"></i> Управление странами
                        </div>
                        <button class="btn btn-refresh" onclick="loadCountries()">
                            <i class="fas fa-sync-alt"></i> Обновить
                        </button>
                    </div>
                    <div class="loading" id="countriesLoading">
                        <i class="fas fa-spinner fa-spin"></i> Загрузка данных...
                    </div>
                    <table id="countriesTable" style="display: none;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Название</th>
                                <th>Код</th>
                                <th>Визовый режим</th>
                                <th>Дата добавления</th>
                            </tr>
                        </thead>
                        <tbody id="countriesBody">
                            <!-- Данные будут здесь -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Вкладка клиентов -->
            <div class="tab-content" id="clients-tab">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fas fa-user-friends"></i> Управление клиентами
                        </div>
                        <button class="btn btn-refresh" onclick="loadClients()">
                            <i class="fas fa-sync-alt"></i> Обновить
                        </button>
                    </div>
                    <div class="loading" id="clientsLoading">
                        <i class="fas fa-spinner fa-spin"></i> Загрузка данных...
                    </div>
                    <table id="clientsTable" style="display: none;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>ФИО</th>
                                <th>Паспорт</th>
                                <th>Телефон</th>
                                <th>Email</th>
                                <th>Дата рождения</th>
                                <th>Дата регистрации</th>
                            </tr>
                        </thead>
                        <tbody id="clientsBody">
                            <!-- Данные будут здесь -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Вкладка туров -->
            <div class="tab-content" id="tours-tab">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fas fa-suitcase-rolling"></i> Управление турами
                        </div>
                        <div>
                            <button class="btn" onclick="loadTours()">
                                <i class="fas fa-list"></i> Все туры
                            </button>
                            <button class="btn btn-refresh" onclick="loadAvailableTours()">
                                <i class="fas fa-check-circle"></i> Доступные туры
                            </button>
                        </div>
                    </div>
                    <div class="loading" id="toursLoading">
                        <i class="fas fa-spinner fa-spin"></i> Загрузка данных...
                    </div>
                    <table id="toursTable" style="display: none;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Страна</th>
                                <th>Название тура</th>
                                <th>Даты</th>
                                <th>Цена</th>
                                <th>Места</th>
                                <th>Статус</th>
                            </tr>
                        </thead>
                        <tbody id="toursBody">
                            <!-- Данные будут здесь -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Вкладка бронирований -->
            <div class="tab-content" id="bookings-tab">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fas fa-calendar-alt"></i> Управление бронированиями
                        </div>
                        <button class="btn btn-refresh" onclick="loadBookings()">
                            <i class="fas fa-sync-alt"></i> Обновить
                        </button>
                    </div>
                    <div class="loading" id="bookingsLoading">
                        <i class="fas fa-spinner fa-spin"></i> Загрузка данных...
                    </div>
                    <table id="bookingsTable" style="display: none;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Клиент</th>
                                <th>Тур</th>
                                <th>Дата брони</th>
                                <th>Сумма</th>
                                <th>Статус</th>
                                <th>Дата создания</th>
                            </tr>
                        </thead>
                        <tbody id="bookingsBody">
                            <!-- Данные будут здесь -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Вкладка статистики -->
            <div class="tab-content" id="stats-tab">
                <div class="stats-grid">
                    <div class="stat-card">
                        <i class="fas fa-globe fa-2x" style="color: #4f46e5;"></i>
                        <div class="stat-number" id="statsCountries">0</div>
                        <div class="stat-label">Стран</div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-users fa-2x" style="color: #10b981;"></i>
                        <div class="stat-number" id="statsClients">0</div>
                        <div class="stat-label">Клиентов</div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-suitcase-rolling fa-2x" style="color: #f59e0b;"></i>
                        <div class="stat-number" id="statsTours">0</div>
                        <div class="stat-label">Туров</div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-calendar-check fa-2x" style="color: #ef4444;"></i>
                        <div class="stat-number" id="statsBookings">0</div>
                        <div class="stat-label">Бронирований</div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Общая статистика</div>
                        <button class="btn btn-refresh" onclick="loadStats()">
                            <i class="fas fa-sync-alt"></i> Обновить
                        </button>
                    </div>
                    <div id="statsDetails">
                        <!-- Детальная статистика будет здесь -->
                    </div>
                </div>
            </div>

            <div class="footer">
                <p>© 2024 Comfort Travel • Версия 1.0 • <span id="currentDate"></span></p>
                <p>Клиентов сегодня: <span id="todayClients">0</span> • Новых броней: <span id="todayBookings">0</span></p>
            </div>
        </div>

        <script>
            // Функция для переключения вкладок
            function switchTab(tabName) {
                // Убираем активный класс со всех вкладок
                document.querySelectorAll('.tab').forEach(tab => {
                    tab.classList.remove('active');
                });
                
                // Добавляем активный класс текущей вкладке
                event.target.classList.add('active');
                
                // Скрываем все содержимое вкладок
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.remove('active');
                });
                
                // Показываем выбранное содержимое
                document.getElementById(tabName + '-tab').classList.add('active');
                
                // Загружаем данные для активной вкладки
                switch(tabName) {
                    case 'countries':
                        loadCountries();
                        break;
                    case 'clients':
                        loadClients();
                        break;
                    case 'tours':
                        loadTours();
                        break;
                    case 'bookings':
                        loadBookings();
                        break;
                    case 'stats':
                        loadStats();
                        break;
                }
            }

            // Показать ошибку
            function showError(message) {
                const errorDiv = document.getElementById('errorMessage');
                errorDiv.textContent = message;
                errorDiv.style.display = 'block';
                setTimeout(() => {
                    errorDiv.style.display = 'none';
                }, 5000);
            }

            // Форматирование даты
            function formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('ru-RU', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
            }

            // Форматирование валюты
            function formatCurrency(amount) {
                return new Intl.NumberFormat('ru-RU', {
                    style: 'currency',
                    currency: 'RUB',
                    minimumFractionDigits: 0
                }).format(amount);
            }

            // Загрузка стран
            async function loadCountries() {
                try {
                    const loading = document.getElementById('countriesLoading');
                    const table = document.getElementById('countriesTable');
                    const body = document.getElementById('countriesBody');
                    
                    loading.style.display = 'block';
                    table.style.display = 'none';
                    body.innerHTML = '';
                    
                    const response = await fetch('api/countries');
                    const data = await response.json();
                    
                    if (data.data && data.data.length > 0) {
                        let html = '';
                        data.data.forEach(country => {
                            html += `
                                <tr>
                                    <td>${country.id}</td>
                                    <td><strong>${country.name}</strong></td>
                                    <td><span class="badge info">${country.code}</span></td>
                                    <td>
                                        ${country.visa_required 
                                            ? '<span class="visa-badge visa-required">Визовый</span>' 
                                            : '<span class="visa-badge visa-free">Без визы</span>'}
                                    </td>
                                    <td>${formatDate(country.created_at)}</td>
                                </tr>
                            `;
                        });
                        body.innerHTML = html;
                        table.style.display = 'table';
                    } else {
                        body.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 40px;">Нет данных о странах</td></tr>';
                        table.style.display = 'table';
                    }
                    
                    loading.style.display = 'none';
                    
                    // Обновляем статистику
                    if (data.data) {
                        document.getElementById('statsCountries').textContent = data.data.length;
                    }
                    
                } catch (error) {
                    loading.style.display = 'none';
                    showError(`Ошибка загрузки стран: ${error.message}`);
                }
            }

            // Загрузка клиентов
            async function loadClients() {
                try {
                    const loading = document.getElementById('clientsLoading');
                    const table = document.getElementById('clientsTable');
                    const body = document.getElementById('clientsBody');
                    
                    loading.style.display = 'block';
                    table.style.display = 'none';
                    body.innerHTML = '';
                    
                    const response = await fetch('api/clients');
                    const data = await response.json();
                    
                    if (data.data && data.data.length > 0) {
                        let html = '';
                        data.data.forEach(client => {
                            const birthDate = new Date(client.birth_date);
                            const age = new Date().getFullYear() - birthDate.getFullYear();
                            
                            html += `
                                <tr>
                                    <td>${client.id}</td>
                                    <td><strong>${client.full_name}</strong></td>
                                    <td><code>${client.passport_number}</code></td>
                                    <td><a href="tel:${client.phone}">${client.phone}</a></td>
                                    <td><a href="mailto:${client.email}">${client.email}</a></td>
                                    <td>${formatDate(client.birth_date)} (${age} лет)</td>
                                    <td>${formatDate(client.created_at)}</td>
                                </tr>
                            `;
                        });
                        body.innerHTML = html;
                        table.style.display = 'table';
                    } else {
                        body.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 40px;">Нет данных о клиентах</td></tr>';
                        table.style.display = 'table';
                    }
                    
                    loading.style.display = 'none';
                    
                    // Обновляем статистику
                    if (data.data) {
                        document.getElementById('statsClients').textContent = data.data.length;
                        document.getElementById('todayClients').textContent = data.data.length;
                    }
                    
                } catch (error) {
                    loading.style.display = 'none';
                    showError(`Ошибка загрузки клиентов: ${error.message}`);
                }
            }

            // Загрузка туров
            async function loadTours() {
                try {
                    const loading = document.getElementById('toursLoading');
                    const table = document.getElementById('toursTable');
                    const body = document.getElementById('toursBody');
                    
                    loading.style.display = 'block';
                    table.style.display = 'none';
                    body.innerHTML = '';
                    
                    const response = await fetch('api/tours');
                    const data = await response.json();
                    
                    if (data.data && data.data.length > 0) {
                        let html = '';
                        data.data.forEach(tour => {
                            const startDate = new Date(tour.start_date);
                            const endDate = new Date(tour.end_date);
                            const days = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
                            const today = new Date();
                            const isUpcoming = startDate > today;
                            const isActive = startDate <= today && endDate >= today;
                            
                            let status = '';
                            if (isActive) {
                                status = '<span class="badge success">Идет сейчас</span>';
                            } else if (isUpcoming) {
                                status = '<span class="badge info">Предстоящий</span>';
                            } else {
                                status = '<span class="badge warning">Завершен</span>';
                            }
                            
                            html += `
                                <tr>
                                    <td>${tour.id}</td>
                                    <td><strong>${tour.country_name}</strong></td>
                                    <td>${tour.name}</td>
                                    <td>
                                        ${formatDate(tour.start_date)} - ${formatDate(tour.end_date)}<br>
                                        <small>(${days} дней)</small>
                                    </td>
                                    <td class="price">${formatCurrency(tour.price)}</td>
                                    <td>
                                        ${tour.available_spots} / ${tour.max_people}<br>
                                        <small>свободно: ${tour.available_spots}</small>
                                    </td>
                                    <td>${status}</td>
                                </tr>
                            `;
                        });
                        body.innerHTML = html;
                        table.style.display = 'table';
                    } else {
                        body.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 40px;">Нет данных о турах</td></tr>';
                        table.style.display = 'table';
                    }
                    
                    loading.style.display = 'none';
                    
                    // Обновляем статистику
                    if (data.data) {
                        document.getElementById('statsTours').textContent = data.data.length;
                    }
                    
                } catch (error) {
                    loading.style.display = 'none';
                    showError(`Ошибка загрузки туров: ${error.message}`);
                }
            }

            // Загрузка доступных туров
            async function loadAvailableTours() {
                try {
                    const loading = document.getElementById('toursLoading');
                    const table = document.getElementById('toursTable');
                    const body = document.getElementById('toursBody');
                    
                    loading.style.display = 'block';
                    table.style.display = 'none';
                    body.innerHTML = '';
                    
                    const response = await fetch('api/tours/available');
                    const data = await response.json();
                    
                    if (data.data && data.data.length > 0) {
                        let html = '';
                        data.data.forEach(tour => {
                            const startDate = new Date(tour.start_date);
                            const endDate = new Date(tour.end_date);
                            const days = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
                            
                            html += `
                                <tr>
                                    <td>${tour.id}</td>
                                    <td><strong>${tour.country_name}</strong></td>
                                    <td>${tour.name}</td>
                                    <td>
                                        ${formatDate(tour.start_date)} - ${formatDate(tour.end_date)}<br>
                                        <small>(${days} дней)</small>
                                    </td>
                                    <td class="price">${formatCurrency(tour.price)}</td>
                                    <td>
                                        <span class="badge success">${tour.available_spots} мест</span>
                                    </td>
                                    <td><span class="badge info">Доступен</span></td>
                                </tr>
                            `;
                        });
                        body.innerHTML = html;
                        table.style.display = 'table';
                    } else {
                        body.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 40px;">Нет доступных туров</td></tr>';
                        table.style.display = 'table';
                    }
                    
                    loading.style.display = 'none';
                    
                } catch (error) {
                    loading.style.display = 'none';
                    showError(`Ошибка загрузки доступных туров: ${error.message}`);
                }
            }

            // Загрузка бронирований
            async function loadBookings() {
                try {
                    const loading = document.getElementById('bookingsLoading');
                    const table = document.getElementById('bookingsTable');
                    const body = document.getElementById('bookingsBody');
                    
                    loading.style.display = 'block';
                    table.style.display = 'none';
                    body.innerHTML = '';
                    
                    const response = await fetch('api/bookings');
                    const data = await response.json();
                    
                    if (data.data && data.data.length > 0) {
                        let html = '';
                        data.data.forEach(booking => {
                            let statusBadge = '';
                            switch(booking.status) {
                                case 'confirmed':
                                    statusBadge = '<span class="badge success">Подтверждено</span>';
                                    break;
                                case 'pending':
                                    statusBadge = '<span class="badge warning">Ожидание</span>';
                                    break;
                                case 'cancelled':
                                    statusBadge = '<span class="badge danger">Отменено</span>';
                                    break;
                                case 'completed':
                                    statusBadge = '<span class="badge info">Завершено</span>';
                                    break;
                                default:
                                    statusBadge = `<span class="badge">${booking.status}</span>`;
                            }
                            
                            html += `
                                <tr>
                                    <td>${booking.id}</td>
                                    <td><strong>${booking.client_name}</strong></td>
                                    <td>${booking.tour_name}</td>
                                    <td>${formatDate(booking.booking_date)}</td>
                                    <td class="price">${formatCurrency(booking.total_price)}</td>
                                    <td>${statusBadge}</td>
                                    <td>${formatDate(booking.created_at)}</td>
                                </tr>
                            `;
                        });
                        body.innerHTML = html;
                        table.style.display = 'table';
                    } else {
                        body.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 40px;">Нет данных о бронированиях</td></tr>';
                        table.style.display = 'table';
                    }
                    
                    loading.style.display = 'none';
                    
                    // Обновляем статистику
                    if (data.data) {
                        document.getElementById('statsBookings').textContent = data.data.length;
                        document.getElementById('todayBookings').textContent = data.data.length;
                    }
                    
                } catch (error) {
                    loading.style.display = 'none';
                    showError(`Ошибка загрузки бронирований: ${error.message}`);
                }
            }

            // Загрузка статистики
            async function loadStats() {
                try {
                    // Загружаем все данные для статистики
                    const endpoints = ['countries', 'clients', 'tours', 'bookings'];
                    const stats = {};
                    
                    for (const endpoint of endpoints) {
                        const response = await fetch(`api/${endpoint}`);
                        const data = await response.json();
                        stats[endpoint] = data.data ? data.data.length : 0;
                        
                        // Обновляем цифры в карточках
                        document.getElementById(`stats${endpoint.charAt(0).toUpperCase() + endpoint.slice(1)}`).textContent = stats[endpoint];
                    }
                    
                    // Детальная статистика
                    let statsHtml = `
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div>
                                <h4>Распределение по странам:</h4>
                                <p>Всего стран: ${stats.countries}</p>
                                <h4>Статистика клиентов:</h4>
                                <p>Всего клиентов: ${stats.clients}</p>
                            </div>
                            <div>
                                <h4>Статистика туров:</h4>
                                <p>Всего туров: ${stats.tours}</p>
                                <h4>Статистика бронирований:</h4>
                                <p>Всего бронирований: ${stats.bookings}</p>
                            </div>
                        </div>
                    `;
                    
                    document.getElementById('statsDetails').innerHTML = statsHtml;
                    
                } catch (error) {
                    showError(`Ошибка загрузки статистики: ${error.message}`);
                }
            }

            // Инициализация при загрузке страницы
            document.addEventListener('DOMContentLoaded', function() {
                // Установка текущей даты
                const now = new Date();
                document.getElementById('currentDate').textContent = now.toLocaleDateString('ru-RU', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                
                // Загружаем страны по умолчанию
                loadCountries();
                loadStats();
                
                // Автоматическое обновление каждые 60 секунд
                setInterval(() => {
                    const activeTab = document.querySelector('.tab.active').getAttribute('onclick');
                    const tabName = activeTab.replace("switchTab('", "").replace("')", "");
                    
                    switch(tabName) {
                        case 'countries':
                            loadCountries();
                            break;
                        case 'clients':
                            loadClients();
                            break;
                        case 'tours':
                            loadTours();
                            break;
                        case 'bookings':
                            loadBookings();
                            break;
                        case 'stats':
                            loadStats();
                            break;
                    }
                }, 60000); // 60 секунд
            });
        </script>
    </body>
    </html>
    <?php
}

// Завершаем буферизацию
ob_end_flush();
?>