<div class="tab-content active" id="countries-tab">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fas fa-globe-americas"></i> Управление странами
            </div>
            <div>
                <button class="btn btn-add" onclick="openModal('Country')">
                    <i class="fas fa-plus"></i> Добавить страну
                </button>
                <button class="btn btn-refresh" onclick="loadCountries()">
                    <i class="fas fa-sync-alt"></i> Обновить
                </button>
            </div>
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
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody id="countriesBody"></tbody>
        </table>
    </div>
</div>

<div class="tab-content" id="clients-tab">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fas fa-user-friends"></i> Управление клиентами
            </div>
            <div>
                <button class="btn btn-add" onclick="openModal('Client')">
                    <i class="fas fa-plus"></i> Добавить клиента
                </button>
                <button class="btn btn-refresh" onclick="loadClients()">
                    <i class="fas fa-sync-alt"></i> Обновить
                </button>
            </div>
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
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody id="clientsBody"></tbody>
        </table>
    </div>
</div>

<div class="tab-content" id="tours-tab">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fas fa-suitcase-rolling"></i> Управление турами
            </div>
            <div>
                <button class="btn btn-add" onclick="openModal('Tour')">
                    <i class="fas fa-plus"></i> Добавить тур
                </button>
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
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody id="toursBody"></tbody>
        </table>
    </div>
</div>

<div class="tab-content" id="bookings-tab">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fas fa-calendar-alt"></i> Управление бронированиями
            </div>
            <div>
                <button class="btn btn-add" onclick="openModal('Booking')">
                    <i class="fas fa-plus"></i> Создать бронь
                </button>
                <button class="btn btn-refresh" onclick="loadBookings()">
                    <i class="fas fa-sync-alt"></i> Обновить
                </button>
            </div>
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
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody id="bookingsBody"></tbody>
        </table>
    </div>
</div>

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
        <div id="statsDetails"></div>
    </div>
</div>