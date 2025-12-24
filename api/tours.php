<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Tour.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

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
        
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Метод не поддерживается"));
}
?>