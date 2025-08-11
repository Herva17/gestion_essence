<?php
header('Content-Type: application/json');
require_once("./Controlleurs/Approvisionnement.php");

$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$url = explode('/', trim($request_uri, '/'));
$url_path1 = $url[1] ?? null; // "approvisionnement"
$url_path2 = $url[2] ?? '';   // action

if ($url_path1 === "approvisionnement") {
    // GET requests
    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        if ($url_path2 === "select") {
            select_all_approvisionnements();
            exit;
        } elseif ($url_path2 === "compter") {
            count_approvisionnements();
            exit;
        } elseif ($url_path2 === "select_one") {
            select_one_approvisionnement();
            exit;
        } elseif ($url_path2 === "search") {
            search_approvisionnements();
            exit;
        }
    }
    // POST requests
    elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
        if ($url_path2 === "save") {
            save_approvisionnement();
            exit;
        } elseif ($url_path2 === "update") {
            update_approvisionnement();
            exit;
        } elseif ($url_path2 === "delete") {
            delete_approvisionnement();
            exit;
        }
    }
}

echo json_encode([
    "succes" => false,
    "message" => "Route non trouvée"
]);