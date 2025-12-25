// Функция для переключения вкладок
function switchTab(tabName, event) {
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

// Открыть модальное окно
function openModal(type) {
    document.getElementById('modal' + type).style.display = 'flex';
    
    // Загружаем данные для выпадающих списков если нужно
    if (type === 'Tour') {
        loadCountriesForSelect();
    } else if (type === 'Booking') {
        loadClientsForSelect();
        loadToursForSelect();
    }
}

// Закрыть модальное окно
function closeModal(type) {
    document.getElementById('modal' + type).style.display = 'none';
}

// Показать уведомление
function showNotification(message, type = 'success') {
    const notification = type === 'success' 
        ? document.getElementById('successNotification')
        : document.getElementById('errorNotification');
    
    const messageElement = notification.querySelector('span');
    messageElement.textContent = message;
    
    notification.style.display = 'block';
    
    // Скрыть через 3 секунды
    setTimeout(() => {
        notification.style.display = 'none';
    }, 3000);
}

// Загрузка стран для выпадающего списка (для туров)
async function loadCountriesForSelect() {
    try {
        const response = await fetch('api/countries');
        const data = await response.json();
        
        const select = document.getElementById('tourCountry');
        select.innerHTML = '<option value="">Выберите страну</option>';
        
        if (data.data && data.data.length > 0) {
            data.data.forEach(country => {
                const option = document.createElement('option');
                option.value = country.id;
                option.textContent = country.name + (country.visa_required ? ' (виза)' : '');
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Ошибка загрузки стран:', error);
    }
}

// Загрузка клиентов для выпадающего списка (для бронирований)
async function loadClientsForSelect() {
    try {
        const response = await fetch('api/clients');
        const data = await response.json();
        
        const select = document.getElementById('bookingClient');
        select.innerHTML = '<option value="">Выберите клиента</option>';
        
        if (data.data && data.data.length > 0) {
            data.data.forEach(client => {
                const option = document.createElement('option');
                option.value = client.id;
                option.textContent = client.full_name;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Ошибка загрузки клиентов:', error);
    }
}

// Загрузка туров для выпадающего списка (для бронирований)
async function loadToursForSelect() {
    try {
        const response = await fetch('api/tours');
        const data = await response.json();
        
        const select = document.getElementById('bookingTour');
        select.innerHTML = '<option value="">Выберите тур</option>';
        
        if (data.data && data.data.length > 0) {
            data.data.forEach(tour => {
                const option = document.createElement('option');
                option.value = tour.id;
                option.textContent = tour.name + ' - ' + formatCurrency(tour.price);
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Ошибка загрузки туров:', error);
    }
}

// Переменные для удаления
let deleteId = null;
let deleteType = null;

// Подтверждение удаления
function confirmDeleteItem(id, type, name = '') {
    deleteId = id;
    deleteType = type;
    
    const messages = {
        'country': 'страну "' + name + '"',
        'client': 'клиента "' + name + '"',
        'tour': 'тур "' + name + '"',
        'booking': 'бронирование #' + id
    };
    
    document.getElementById('confirmMessage').textContent = 
        `Вы уверены, что хотите удалить ${messages[type]}?`;
    
    openModal('Confirm');
}

// Выполнить удаление
async function confirmDelete() {
    if (!deleteId || !deleteType) return;
    
    try {
        let endpoint = '';
        switch(deleteType) {
            case 'country':
                endpoint = 'countries';
                break;
            case 'client':
                endpoint = 'clients';
                break;
            case 'tour':
                endpoint = 'tours';
                break;
            case 'booking':
                endpoint = 'bookings';
                break;
        }
        
        const response = await fetch(`api/${endpoint}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id: deleteId
            })
        });
        
        if (response.ok) {
            showNotification('Запись успешно удалена!');
            
            // Закрыть модальное окно подтверждения
            closeModal('Confirm');
            
            // Обновить список в зависимости от текущей вкладки
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
            }
        } else {
            const data = await response.json();
            showNotification(data.message || 'Ошибка при удалении', 'error');
        }
    } catch (error) {
        showNotification('Ошибка сети: ' + error.message, 'error');
    }
    
    // Сбросить переменные
    deleteId = null;
    deleteType = null;
}

// Добавить страну
async function addCountry() {
    const name = document.getElementById('countryName').value.trim();
    const code = document.getElementById('countryCode').value.trim();
    const visaRequired = document.getElementById('countryVisa').checked;
    
    if (!name || !code) {
        showNotification('Заполните все обязательные поля', 'error');
        return;
    }
    
    console.log('Отправка данных:', { name, code, visa_required: visaRequired }); // Для отладки
    
    try {
        const response = await fetch('api/countries', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                name: name,
                code: code,
                visa_required: visaRequired ? 1 : 0  // Преобразуем boolean в число
            })
        });
        
        const data = await response.json();
        
        if (response.ok) {
            showNotification('Страна успешно добавлена!');
            closeModal('Country');
            
            // Очистить форму
            document.getElementById('countryName').value = '';
            document.getElementById('countryCode').value = '';
            document.getElementById('countryVisa').checked = false;
            
            // Обновить список
            loadCountries();
        } else {
            showNotification(data.message || 'Ошибка при добавлении', 'error');
        }
    } catch (error) {
        showNotification('Ошибка сети: ' + error.message, 'error');
    }
}

// Добавить клиента
async function addClient() {
    const fullName = document.getElementById('clientFullName').value.trim();
    const passport = document.getElementById('clientPassport').value.trim();
    const phone = document.getElementById('clientPhone').value.trim();
    const email = document.getElementById('clientEmail').value.trim();
    const birthDate = document.getElementById('clientBirthDate').value;
    
    if (!fullName || !passport) {
        showNotification('Заполните ФИО и номер паспорта', 'error');
        return;
    }
    
    try {
        const response = await fetch('api/clients', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                full_name: fullName,
                passport_number: passport,
                phone: phone || null,
                email: email || null,
                birth_date: birthDate || null
            })
        });
        
        const data = await response.json();
        
        if (response.ok) {
            showNotification('Клиент успешно добавлен!');
            closeModal('Client');
            
            // Очистить форму
            document.getElementById('clientFullName').value = '';
            document.getElementById('clientPassport').value = '';
            document.getElementById('clientPhone').value = '';
            document.getElementById('clientEmail').value = '';
            document.getElementById('clientBirthDate').value = '';
            
            // Обновить список
            loadClients();
        } else {
            showNotification(data.message || 'Ошибка при добавлении', 'error');
        }
    } catch (error) {
        showNotification('Ошибка сети: ' + error.message, 'error');
    }
}

// Добавить тур
async function addTour() {
    const countryId = document.getElementById('tourCountry').value;
    const name = document.getElementById('tourName').value.trim();
    const description = document.getElementById('tourDescription').value.trim();
    const startDate = document.getElementById('tourStartDate').value;
    const endDate = document.getElementById('tourEndDate').value;
    const price = parseFloat(document.getElementById('tourPrice').value);
    const maxPeople = parseInt(document.getElementById('tourMaxPeople').value);
    
    if (!countryId || !name || !startDate || !endDate || !price || !maxPeople) {
        showNotification('Заполните все обязательные поля', 'error');
        return;
    }
    
    if (startDate > endDate) {
        showNotification('Дата начала не может быть позже даты окончания', 'error');
        return;
    }
    
    try {
        const response = await fetch('api/tours', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                country_id: parseInt(countryId),
                name: name,
                description: description || null,
                start_date: startDate,
                end_date: endDate,
                price: price,
                max_people: maxPeople,
                available_spots: maxPeople // При создании все места свободны
            })
        });
        
        const data = await response.json();
        
        if (response.ok) {
            showNotification('Тур успешно добавлен!');
            closeModal('Tour');
            
            // Очистить форму
            document.getElementById('tourCountry').selectedIndex = 0;
            document.getElementById('tourName').value = '';
            document.getElementById('tourDescription').value = '';
            document.getElementById('tourStartDate').value = '';
            document.getElementById('tourEndDate').value = '';
            document.getElementById('tourPrice').value = '';
            document.getElementById('tourMaxPeople').value = '';
            
            // Обновить список
            loadTours();
        } else {
            showNotification(data.message || 'Ошибка при добавлении', 'error');
        }
    } catch (error) {
        showNotification('Ошибка сети: ' + error.message, 'error');
    }
}

// Добавить бронирование
async function addBooking() {
    const clientId = document.getElementById('bookingClient').value;
    const tourId = document.getElementById('bookingTour').value;
    const bookingDate = document.getElementById('bookingDate').value;
    const status = document.getElementById('bookingStatus').value;
    const price = parseFloat(document.getElementById('bookingPrice').value);
    const notes = document.getElementById('bookingNotes').value.trim();
    
    if (!clientId || !tourId) {
        showNotification('Выберите клиента и тур', 'error');
        return;
    }
    
    try {
        const response = await fetch('api/bookings', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                client_id: parseInt(clientId),
                tour_id: parseInt(tourId),
                booking_date: bookingDate || new Date().toISOString().split('T')[0],
                status: status,
                total_price: price || 0,
                notes: notes || ''
            })
        });
        
        const data = await response.json();
        
        if (response.ok) {
            showNotification('Бронирование успешно создано!');
            closeModal('Booking');
            
            // Очистить форму
            document.getElementById('bookingClient').selectedIndex = 0;
            document.getElementById('bookingTour').selectedIndex = 0;
            document.getElementById('bookingDate').value = '';
            document.getElementById('bookingStatus').selectedIndex = 0;
            document.getElementById('bookingPrice').value = '';
            document.getElementById('bookingNotes').value = '';
            
            // Обновить список
            loadBookings();
        } else {
            showNotification(data.message || 'Ошибка при создании', 'error');
        }
    } catch (error) {
        showNotification('Ошибка сети: ' + error.message, 'error');
    }
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
                        <td>
                            <div class="actions">
                                <button class="btn-action btn-delete" onclick="confirmDeleteItem(${country.id}, 'country', '${country.name}')">
                                    <i class="fas fa-trash"></i> Удалить
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            body.innerHTML = html;
            table.style.display = 'table';
        } else {
            body.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 40px;">Нет данных о странах</td></tr>';
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
                        <td>
                            <div class="actions">
                                <button class="btn-action btn-delete" onclick="confirmDeleteItem(${client.id}, 'client', '${client.full_name}')">
                                    <i class="fas fa-trash"></i> Удалить
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            body.innerHTML = html;
            table.style.display = 'table';
        } else {
            body.innerHTML = '<tr><td colspan="8" style="text-align: center; padding: 40px;">Нет данных о клиентах</td></tr>';
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
                        <td>
                            <div class="actions">
                                <button class="btn-action btn-delete" onclick="confirmDeleteItem(${tour.id}, 'tour', '${tour.name}')">
                                    <i class="fas fa-trash"></i> Удалить
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            body.innerHTML = html;
            table.style.display = 'table';
        } else {
            body.innerHTML = '<tr><td colspan="8" style="text-align: center; padding: 40px;">Нет данных о турах</td></tr>';
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
                        <td>
                            <div class="actions">
                                <button class="btn-action btn-delete" onclick="confirmDeleteItem(${tour.id}, 'tour', '${tour.name}')">
                                    <i class="fas fa-trash"></i> Удалить
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            body.innerHTML = html;
            table.style.display = 'table';
        } else {
            body.innerHTML = '<tr><td colspan="8" style="text-align: center; padding: 40px;">Нет доступных туров</td></tr>';
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
                        <td>
                            <div class="actions">
                                <button class="btn-action btn-delete" onclick="confirmDeleteItem(${booking.id}, 'booking')">
                                    <i class="fas fa-trash"></i> Удалить
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            body.innerHTML = html;
            table.style.display = 'table';
        } else {
            body.innerHTML = '<tr><td colspan="8" style="text-align: center; padding: 40px;">Нет данных о бронированиях</td></tr>';
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