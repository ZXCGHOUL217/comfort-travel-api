<?php
ob_start();
$current_url = $_SERVER['REQUEST_URI'];

if (strpos($current_url, '/api/') !== false) {
    $parts = explode('/', trim($current_url, '/'));
    $api_index = array_search('api', $parts);
    
    if ($api_index !== false && isset($parts[$api_index + 1])) {
        $endpoint = $parts[$api_index + 1];
        
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        
        $api_file = __DIR__ . '/api/' . $endpoint . '.php';
        
        if (file_exists($api_file)) {
            require_once __DIR__ . '/config/database.php';
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
    include __DIR__ . '/includes/header.php';
    ?>
    
    <div class="container">
        <?php include __DIR__ . '/includes/header-content.php'; ?>
        
        <div class="tabs">
            <div class="tab active" onclick="switchTab('countries', event)">
                <i class="fas fa-globe"></i> Страны
            </div>
            <div class="tab" onclick="switchTab('clients', event)">
                <i class="fas fa-users"></i> Клиенты
            </div>
            <div class="tab" onclick="switchTab('tours', event)">
                <i class="fas fa-map-marked-alt"></i> Туры
            </div>
            <div class="tab" onclick="switchTab('bookings', event)">
                <i class="fas fa-calendar-check"></i> Бронирования
            </div>
            <div class="tab" onclick="switchTab('stats', event)">
                <i class="fas fa-chart-bar"></i> Статистика
            </div>
        </div>

        <div class="error-message" id="errorMessage"></div>

        <?php include __DIR__ . '/includes/tabs.php'; ?>
        <?php include __DIR__ . '/includes/footer-content.php'; ?>
    </div>

    <?php 
    include __DIR__ . '/includes/modals.php';
    include __DIR__ . '/includes/notifications.php';
    include __DIR__ . '/includes/footer.php';
}

ob_end_flush();
?>