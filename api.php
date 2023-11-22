<?php
//replace with your origin
header("Access-Control-Allow-Origin: http://localhost:5173");
header('Access-Control-Allow-Methods: *');
header("Access-Control-Allow-Headers: *");
include_once 'database.php';


$apiKey = isset($_SERVER['HTTP_APIKEY']) ? $_SERVER['HTTP_APIKEY'] : '';
// API KEYS (add keys here)
$keys = [
    "fullAccess" => "FULLKEY",
    "viewAccess" => "READKEY",
    "editAccess" => "WRITEKEY"
];

// validates keys
$isKeyValid = false;
foreach ($keys as $key => $value) {
    if ($apiKey === $value) {
        $isKeyValid = true;
        break;
    }
}

// customize perms here
$perm = "";
switch ($apiKey) {
    case $keys['fullAccess']:
        $perm = "rw";
        break;
    case $keys['viewAccess']:
        $perm = "r";
        break;
    case $keys['editAccess']:
        $perm = "w";
        break;
}

if (!$isKeyValid || $perm == "") {
    echo json_encode(array("message" => "Invalid API key $apiKey"));
    exit();
}

$database = new Database();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // perm check
        if (!str_contains($perm, "r")) {
            echo json_encode(array("message" => "Invalid API key permission"));
            break;
        }

        $database->GetFunction($_GET);
        break;

    case 'POST':
        // perm check
        if (!str_contains($perm, "w")) {
            echo json_encode(array("message" => "Invalid API key permission"));
            break;
        }

        $database->PostFunction($_POST);
        break;

    case 'PUT':
        // perm check
        if (!str_contains($perm, "w")) {
            echo json_encode(array("message" => "Invalid API key permission"));
            break;
        }

        $database->PutFunction($_PUT);
        break;

    case 'DELETE':
        // perm check
        if (!str_contains($perm, "w")) {
            echo json_encode(array("message" => "Invalid API key permission"));
            break;
        }

        $database->DeleteFunction($_DELETE);
        break;

    default:
        echo json_encode(array("message" => "Missing method"));
        break;
}
