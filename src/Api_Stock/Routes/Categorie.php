<?php
header('Content-Type: Application/json');
require_once("./Controlleurs/Categorie.php");
$data = array();
$url = explode('/', $_SERVER['REQUEST_URI']);
$url_path1 = $url[2];  // "categorie"
$url_path2 = isset($url[3]) ? $url[3] : ''; // action

if ($url_path1 == "categorie") {
    // GET requests
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        if ($url_path2 == "select") {
            $data["me"] = selectionner_categories();
            echo json_encode($data);
            exit;
        } elseif ($url_path2 == "compter") {
            $data["me"] = compter_categories();
            echo json_encode($data);
            exit;
        } elseif ($url_path2 == "select_one") {
            $data["me"] = selectionner_une_categorie();
            echo json_encode($data);
            exit;
        } elseif ($url_path2 == "search") {
            $data["me"] = rechercher_categories();
            echo json_encode($data);
            exit;
        }
    } 
    // POST requests
    elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
        if ($url_path2 == "save") {   
            $data["me"] = enregistrer_categorie();
            echo json_encode($data);
            exit;
        } elseif ($url_path2 == "update") {
            $data["me"] = modification_categorie();
            echo json_encode($data);
            exit;
        } elseif ($url_path2 == "delete") {
            $data["me"] = supprimer_categorie();
            echo json_encode($data);
            exit;
        }
    }
}

// Si aucune route ne correspond
$data["me"] = ["Message" => "Endpoint non trouvé"];
http_response_code(404);
echo json_encode($data);