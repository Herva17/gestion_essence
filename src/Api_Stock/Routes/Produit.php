<?php
header('Content-Type: application/json');
require_once("./Controlleurs/Produit.php");

// Nettoyer tout buffer de sortie existant
ob_clean();

$url = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$url_path1 = $url[1] ?? null; // "produit"
$url_path2 = $url[2] ?? '';   // action

if ($url_path1 === "produit") {
    try {
        // GET requests
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            switch ($url_path2) {
                case "select_all":
                    selectionner_produits();
                    break;
                     case "fiche_stock":
                    fiche_stock();
                    break;
                case "compter":
                    compter_produits();
                    break;
                case "rechercher":
                    rechercher_produits();
                    break;
                default:
                    http_response_code(404);
                    echo json_encode([
                        "success" => false,
                        "message" => "Endpoint GET non reconnu"
                    ]);
            }
            exit;
        }
        // POST requests
        elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
            switch ($url_path2) {
                case "select_one":
                    selectionner_un_produit();
                    break;
                case "enregistrer":
                    enregistrer_produit();
                    break;
                case "modifier":
                    modifier_produit();
                    break;
                case "supprimer":
                    supprimer_produit();
                    break;
                default:
                    http_response_code(404);
                    echo json_encode([
                        "success" => false,
                        "message" => "Endpoint POST non reconnu"
                    ]);
            }
            exit;
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Erreur serveur: " . $e->getMessage()
        ]);
        exit;
    }
}

// Si aucune route ne correspond
http_response_code(404);
echo json_encode([
    "success" => false,
    "message" => "Endpoint non reconnu"
]);