<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Booking.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Обработка OPTIONS запроса для CORS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(array("message" => "Нет подключения к БД"));
    exit;
}

$booking = new Booking($db);
$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        $stmt = $booking->read();
        $num = $stmt->rowCount();
        
        if($num > 0) {
            $bookings_arr = array();
            $bookings_arr["data"] = array();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $booking_item = array(
                    "id" => $id,
                    "client_id" => $client_id,
                    "client_name" => $client_name,
                    "tour_id" => $tour_id,
                    "tour_name" => $tour_name,
                    "booking_date" => $booking_date,
                    "status" => $status,
                    "total_price" => $total_price,
                    "notes" => $notes,
                    "created_at" => $created_at
                );
                array_push($bookings_arr["data"], $booking_item);
            }
            
            http_response_code(200);
            echo json_encode($bookings_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Бронирования не найдены."));
        }
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->client_id) && !empty($data->tour_id)) {
            $booking->client_id = $data->client_id;
            $booking->tour_id = $data->tour_id;
            $booking->booking_date = $data->booking_date ?? date('Y-m-d');
            $booking->status = $data->status ?? 'pending';
            $booking->total_price = $data->total_price ?? 0;
            $booking->notes = $data->notes ?? '';

            if($booking->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Бронирование создано."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Невозможно создать бронирование."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Невозможно создать бронирование. Данные неполные."));
        }
        break;
        
    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->id)) {
            $booking->id = $data->id;
            
            if($booking->delete()) {
                http_response_code(200);
                echo json_encode(array("message" => "Бронирование удалено."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Невозможно удалить бронирование."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Укажите ID бронирования для удаления."));
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Метод не поддерживается"));
}
?>