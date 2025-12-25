<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Tour.php';

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

$tour = new Tour($db);
$method = $_SERVER['REQUEST_METHOD'];

// Определяем endpoint: /api/tours или /api/tours/available
$request = $_SERVER['REQUEST_URI'];
$isAvailable = strpos($request, 'available') !== false;

switch($method) {
    case 'GET':
        if ($isAvailable) {
            $stmt = $tour->readAvailable();
        } else {
            $stmt = $tour->read();
        }
        
        $num = $stmt->rowCount();
        
        if($num > 0) {
            $tours_arr = array();
            $tours_arr["data"] = array();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $tour_item = array(
                    "id" => $id,
                    "country_id" => $country_id,
                    "country_name" => $country_name,
                    "name" => $name,
                    "description" => $description,
                    "start_date" => $start_date,
                    "end_date" => $end_date,
                    "price" => $price,
                    "max_people" => $max_people,
                    "available_spots" => $available_spots,
                    "created_at" => $created_at
                );
                array_push($tours_arr["data"], $tour_item);
            }
            
            http_response_code(200);
            echo json_encode($tours_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Туры не найдены."));
        }
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->name) && !empty($data->country_id)) {
            $tour->name = $data->name;
            $tour->country_id = $data->country_id;
            $tour->description = $data->description ?? '';
            $tour->start_date = $data->start_date ?? date('Y-m-d');
            $tour->end_date = $data->end_date ?? date('Y-m-d');
            $tour->price = $data->price ?? 0;
            $tour->max_people = $data->max_people ?? 1;
            $tour->available_spots = $data->available_spots ?? $data->max_people ?? 1;

            if($tour->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Тур создан."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Невозможно создать тур."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Невозможно создать тур. Данные неполные."));
        }
        break;
        
    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->id)) {
            $tour->id = $data->id;
            
            if($tour->delete()) {
                http_response_code(200);
                echo json_encode(array("message" => "Тур удален."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Невозможно удалить тур."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Укажите ID тура для удаления."));
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Метод не поддерживается"));
}
?>