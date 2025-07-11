<?php
header('Content-Type: Application/json');
require("./Controlleurs/Commande.php");
$data = array();
$url = explode('/', $_SERVER['REQUEST_URI']);
//print_r($url);
$url_path1 = $url[2];  // "commande"
$url_path2 = isset($url[3]) ? $url[3] : ''; // action

if ($url_path1 == "commande") {
    // GET requests
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        if ($url_path2 == "select") {
            $data["me"] = selectionner_commandes();
            echo json_encode($data);
            exit;
        } elseif ($url_path2 == "compter") {
            $data["me"] = compter_commandes();
            echo json_encode($data);
            exit;
        } elseif ($url_path2 == "select_one") {
            $data["me"] = selectionner_une_commande();
            echo json_encode($data);
            exit;
        } elseif ($url_path2 == "by_client") {
            $data["me"] = commandes_par_client();
            echo json_encode($data);
            exit;
        } elseif ($url_path2 == "by_produit") {
            $data["me"] = commandes_par_produit();
            echo json_encode($data);
            exit;
        }
    } 
    // POST requests
    elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
        if ($url_path2 == "save") {   
            $data["me"] = enregistrer_commande();
            echo json_encode($data);
            exit;
        } elseif ($url_path2 == "update") {
            $data["me"] = modification_commande();
            echo json_encode($data);
            exit;
        } elseif ($url_path2 == "delete") {
            $data["me"] = supprimer_commande();
            echo json_encode($data);
            exit;
        }
    }
}

// Si aucune route ne correspond
$data["me"] = ["Message" => "Endpoint non trouvé"];
http_response_code(404);
echo json_encode($data);