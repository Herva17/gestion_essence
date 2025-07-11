<?php
header('Content-Type: application/json');
require_once("./Controlleurs/Client.php");

// Extraction du chemin de la requête
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$url = explode('/', trim($request_uri, '/'));
$url_path1 = $url[1] ?? null; // "client"
$url_path2 = $url[2] ?? '';   // action

if ($url_path1 === "client") {
    // GET requests
    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        if ($url_path2 === "select") {
            select_all_clients();
            exit;
        } elseif ($url_path2 === "compter") {
            count_clients();
            exit;
        } elseif ($url_path2 === "select_one") {
            select_one_client();
            exit;
        } elseif ($url_path2 === "search") {
            search_clients();
            exit;
        }
    }
    // POST requests
    elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
        if ($url_path2 === "save") {
            save_client();
            exit;
        } elseif ($url_path2 === "update") {
            update_client();
            exit;
        } elseif ($url_path2 === "delete") {
            delete_client();
            exit;
        }
    }
}

