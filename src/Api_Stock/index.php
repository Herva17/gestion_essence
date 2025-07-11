<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Headers: *');

$user = md5('herva');
$mdp = md5('mdp');

if (
    (isset($_GET['user']) && ($_GET['user'] == 'herva' || $_GET['user'] == $user)) &&
    (isset($_GET['mdp']) && ($_GET['mdp'] == "mdp" || $_GET['mdp'] == $mdp))
) {
    // Analyse l'URL pour router vers la bonne ressource
    $request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $url = explode('/', trim($request_uri, '/'));
    $resource = $url[1] ?? null;

    switch ($resource) {
        case 'utilisateur':
            require_once("Routes/Utilisateurs.php");
            exit;
        case 'categorie':
            require_once("Routes/Categorie.php");
            exit;
        case 'client':
            require_once("Routes/client.php");
            exit;
        case 'mouvement':
            require_once("Routes/Mouvement.php");
            exit;
        case 'produit':
            require_once("Routes/Produit.php");
            exit;
        case 'comptable':
            require_once("Routes/comptable.php");
            exit;
        case 'commande':
            require_once("Routes/Commande.php");
            exit;
        default:
            http_response_code(404);
            echo json_encode(["succes" => false, "message" => "Ressource non trouvée"]);
            exit;
    }
} else {
    http_response_code(401);
    echo json_encode(["succes" => false, "message" => "Accès refusé"]);
    exit;
}