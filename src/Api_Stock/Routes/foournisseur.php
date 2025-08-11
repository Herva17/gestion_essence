<?php
header('Content-Type: application/json');
require_once("./Controlleurs/Fournisseur.php");

// Extraction du chemin de la requête
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$url = explode('/', trim($request_uri, '/'));
$url_path1 = $url[1] ?? null; // "fournisseur"
$url_path2 = $url[2] ?? '';   // action

if ($url_path1 === "fournisseur") {
    // GET requests
    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        if ($url_path2 === "select") {
            select_all_fournisseurs();
            exit;
        } elseif ($url_path2 === "compter") {
            count_fournisseurs();
            exit;
        } elseif ($url_path2 === "select_one") {
            select_one_fournisseur();
            exit;
        } elseif ($url_path2 === "search") {
            search_fournisseurs();
            exit;
        }
    }
    // POST requests
    elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
        if ($url_path2 === "save") {
            save_fournisseur();
            exit;
        } elseif ($url_path2 === "update") {
            update_fournisseur();
            exit;
        } elseif ($url_path2 === "delete") {
            delete_fournisseur();
            exit;
        }
    }
}

// Si aucune route ne correspond
echo json_encode([
    "succes" => false,
    "message" => "Route non trouvée"
]);