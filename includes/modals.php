<!-- Модальные окна добавления -->
<div id="modalCountry" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-plus-circle"></i> Добавить страну</h3>
            <span class="modal-close" onclick="closeModal('Country')">&times;</span>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Название страны:</label>
                <input type="text" id="countryName" placeholder="Например: Греция" class="form-input">
            </div>
            <div class="form-group">
                <label>Код страны:</label>
                <input type="text" id="countryCode" placeholder="Например: GR" class="form-input" maxlength="10">
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" id="countryVisa"> Требуется виза
                </label>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('Country')">Отмена</button>
            <button class="btn btn-primary" onclick="addCountry()">Добавить страну</button>
        </div>
    </div>
</div>

<div id="modalClient" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-user-plus"></i> Добавить клиента</h3>
            <span class="modal-close" onclick="closeModal('Client')">&times;</span>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>ФИО клиента:</label>
                <input type="text" id="clientFullName" placeholder="Иванов Иван Иванович" class="form-input">
            </div>
            <div class="form-group">
                <label>Номер паспорта:</label>
                <input type="text" id="clientPassport" placeholder="1234567890" class="form-input">
            </div>
            <div class="form-group">
                <label>Телефон:</label>
                <input type="tel" id="clientPhone" placeholder="+79161234567" class="form-input">
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" id="clientEmail" placeholder="ivanov@mail.ru" class="form-input">
            </div>
            <div class="form-group">
                <label>Дата рождения:</label>
                <input type="date" id="clientBirthDate" class="form-input">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('Client')">Отмена</button>
            <button class="btn btn-primary" onclick="addClient()">Добавить клиента</button>
        </div>
    </div>
</div>

<div id="modalTour" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-plane"></i> Добавить тур</h3>
            <span class="modal-close" onclick="closeModal('Tour')">&times;</span>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Выберите страну:</label>
                <select id="tourCountry" class="form-input">
                    <option value="">Загрузка стран...</option>
                </select>
            </div>
            <div class="form-group">
                <label>Название тура:</label>
                <input type="text" id="tourName" placeholder="Например: Анталия: Все включено" class="form-input">
            </div>
            <div class="form-group">
                <label>Описание:</label>
                <textarea id="tourDescription" placeholder="Описание тура..." class="form-input" rows="3"></textarea>
            </div>
            <div class="form-row">
                <div class="form-group half">
                    <label>Дата начала:</label>
                    <input type="date" id="tourStartDate" class="form-input">
                </div>
                <div class="form-group half">
                    <label>Дата окончания:</label>
                    <input type="date" id="tourEndDate" class="form-input">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group half">
                    <label>Цена (руб.):</label>
                    <input type="number" id="tourPrice" placeholder="85000" class="form-input">
                </div>
                <div class="form-group half">
                    <label>Количество мест:</label>
                    <input type="number" id="tourMaxPeople" placeholder="20" class="form-input">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('Tour')">Отмена</button>
            <button class="btn btn-primary" onclick="addTour()">Добавить тур</button>
        </div>
    </div>
</div>

<div id="modalBooking" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-calendar-plus"></i> Создать бронирование</h3>
            <span class="modal-close" onclick="closeModal('Booking')">&times;</span>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Выберите клиента:</label>
                <select id="bookingClient" class="form-input">
                    <option value="">Загрузка клиентов...</option>
                </select>
            </div>
            <div class="form-group">
                <label>Выберите тур:</label>
                <select id="bookingTour" class="form-input">
                    <option value="">Загрузка туров...</option>
                </select>
            </div>
            <div class="form-group">
                <label>Дата бронирования:</label>
                <input type="date" id="bookingDate" class="form-input">
            </div>
            <div class="form-group">
                <label>Статус:</label>
                <select id="bookingStatus" class="form-input">
                    <option value="pending">Ожидание</option>
                    <option value="confirmed">Подтверждено</option>
                    <option value="completed">Завершено</option>
                </select>
            </div>
            <div class="form-group">
                <label>Сумма:</label>
                <input type="number" id="bookingPrice" placeholder="85000" class="form-input">
            </div>
            <div class="form-group">
                <label>Примечания:</label>
                <textarea id="bookingNotes" placeholder="Дополнительная информация..." class="form-input" rows="2"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('Booking')">Отмена</button>
            <button class="btn btn-primary" onclick="addBooking()">Создать бронирование</button>
        </div>
    </div>
</div>

<!-- Модальное окно подтверждения удаления -->
<div id="modalConfirm" class="modal">
    <div class="modal-content confirm-modal">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-triangle"></i> Подтверждение удаления</h3>
        </div>
        <div class="modal-body">
            <p id="confirmMessage">Вы уверены, что хотите удалить эту запись?</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('Confirm')">Отмена</button>
            <button class="btn btn-danger" onclick="confirmDelete()">Удалить</button>
        </div>
    </div>
</div>