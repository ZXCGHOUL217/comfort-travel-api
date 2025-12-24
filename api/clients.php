<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Client.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(array("message" => "Нет подключения к БД"));
    exit;
}

$client = new Client($db);
$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        $stmt = $client->read();
        $num = $stmt->rowCount();
        
        if($num > 0) {
            $clients_arr = array();
            $clients_arr["data"] = array();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $client_item = array(
                    "id" => $id,
                    "full_name" => $full_name,
                    "passport_number" => $passport_number,
                    "phone" => $phone,
                    "email" => $email,
                    "birth_date" => $birth_date,
                    "created_at" => $created_at
                );
                array_push($clients_arr["data"], $client_item);
            }
            
            http_response_code(200);
            echo json_encode($clients_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Клиенты не найдены."));
        }
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->full_name) && !empty($data->passport_number)) {
            $client->full_name = $data->full_name;
            $client->passport_number = $data->passport_number;
            $client->phone = $data->phone ?? '';
            $client->email = $data->email ?? '';
            $client->birth_date = $data->birth_date ?? date('Y-m-d');

            if($client->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Клиент создан."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Невозможно создать клиента."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Невозможно создать клиента. Данные неполные."));
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Метод не поддерживается"));
}
?>