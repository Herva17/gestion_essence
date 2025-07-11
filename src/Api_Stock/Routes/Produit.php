<?php
header('Content-Type: Application/json');
require_once("./Controlleurs/Produit.php");
$data = array();
$url = explode('/', $_SERVER['REQUEST_URI']);
//print_r($url);
$url_path1 = $url[2];
$url_path2 = $url[3];

if ($url_path1 == "produit") {
    // GET requests
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        if ($url_path2 == "select_all") {
            $data["response"] = selectionner_produits();
            echo json_encode($data);
            exit;
        } elseif ($url_path2 == "compter") {
            $data["response"] = compter_produits();
            echo json_encode($data);
            exit;
        } elseif ($url_path2 == "rechercher") {
            $data["response"] = rechercher_produits();
            echo json_encode($data);
            exit;
        } elseif ($url_path2 == "par_categorie") {
            $data["response"] = produits_par_categorie();
            echo json_encode($data);
            exit;
        }
    } 
    // POST requests
    elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
        if ($url_path2 == "select_one") {
            $data["response"] = selectionner_un_produit();
            echo json_encode($data);
            exit;
        } elseif ($url_path2 == "enregistrer") {
            $data["response"] = enregistrer_produit();
            echo json_encode($data);
            exit;
        } elseif ($url_path2 == "modifier") {
            $data["response"] = modifier_produit();
            echo json_encode($data);
            exit;
        } elseif ($url_path2 == "supprimer") {
            $data["response"] = supprimer_produit();
            echo json_encode($data);
            exit;
        }
    }
}

// Si aucune route ne correspond
$data["response"] = ["status" => "error", "message" => "Endpoint non reconnu"];
echo json_encode($data);