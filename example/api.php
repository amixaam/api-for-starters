<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header('Access-Control-Allow-Methods: *');
header("Access-Control-Allow-Headers: *");
include_once 'database.php';


$apiKey = isset($_SERVER['HTTP_APIKEY']) ? $_SERVER['HTTP_APIKEY'] : '';
$keys = ["fullAccess" => "Kabihe#@#42@069", "viewAccess" => "KabiheREAD", "editAccess" => "KabiheWRITE"];

$isKeyValid = false;
foreach ($keys as $key => $value) {
    if ($apiKey === $value) {
        $isKeyValid = true;
        break;
    }
}
if (!$isKeyValid) {
    echo json_encode(array("message" => "Invalid API key $apiKey"));
    exit();
}

$database = new Database();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if ($apiKey == $keys['fullAccess'] || $apiKey == $keys['viewAccess']) {
            if (isset($_GET['id'])) {
                if ($_GET['id'] == null) {
                    echo json_encode(array("message" => "Missing ID"));
                    exit();
                }
                $database->GetTaskId($_GET['id']);
            } else {
                $database->GetTasks();
            }
        } else {
            echo json_encode(array("message" => "Invalid API key permission"));
        }
        break;

    case 'POST':
        if ($apiKey == $keys['fullAccess']  || $apiKey == $keys['editAccess']) {
            $database->PostTask($_POST);
        } else {
            echo json_encode(array("message" => "Invalid API key permission"));
        }
        break;

    case 'PUT':
        if ($apiKey == $keys['fullAccess'] || $apiKey == $keys['editAccess']) {
            if (isset($_GET['id'])) {
                if ($_GET['id'] == null) {
                    echo json_encode(array("message" => "Missing ID"));
                    exit();
                }
                parse_str(file_get_contents("php://input"), $_PUT);
                $database->PutTaskId($_GET['id'], $_PUT);
            } else {
                echo json_encode(array("message" => "Missing ID"));
            }
        } else {
            echo json_encode(array("message" => "Invalid API key permission"));
        }
        break;

    case 'DELETE':
        if ($apiKey == $keys['fullAccess']) {
            if (isset($_GET['id'])) {
                if ($_GET['id'] == null) {
                    echo json_encode(array("message" => "Missing ID"));
                    exit();
                }
                $database->DeleteTaskId($_GET['id']);
            } else {
                echo json_encode(array("message" => "Missing ID"));
            }
        } else {
            echo json_encode(array("message" => "Invalid API key permission"));
        }
        break;
    default:
        echo json_encode(array("message" => "Missing method"));
        break;
}
