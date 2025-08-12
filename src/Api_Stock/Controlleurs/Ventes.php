<?php
require_once("./Models/Ventes.php");

function enregistrer_vente()
{
    header('Content-Type: application/json; charset=utf-8');
    $id_commande = isset($_POST["id_commande"]) ? intval($_POST["id_commande"]) : null;
    $quantite = isset($_POST["quantite"]) ? intval($_POST["quantite"]) : null;
    $date_vente = isset($_POST["date_vente"]) ? $_POST["date_vente"] : date("Y-m-d");

    // Validation des champs obligatoires
    if (!$id_commande || !$quantite) {
        echo json_encode([
            "succes" => false,
            "message" => "Les champs ID commande et quantité sont obligatoires"
        ]);
        exit;
    }

    // Validation de la quantité
    if ($quantite <= 0) {
        echo json_encode([
            "succes" => false,
            "message" => "La quantité doit être supérieure à zéro"
        ]);
        exit;
    }

    // Validation de la date
    if (!strtotime($date_vente)) {
        echo json_encode([
            "succes" => false,
            "message" => "Date de vente invalide"
        ]);
        exit;
    }

    // Appel au modèle pour enregistrer la vente
    $result = Vente::enregistrer($id_commande, $quantite, $date_vente);

    if (isset($result["Reussite"])) {
        echo json_encode([
            "succes" => true,
            "message" => $result["Reussite"],
            "data" => $result["Dernier_Enregistrement"] ?? null,
            "stocks" => [
                "commande" => $result["Stock_Restant_Commande"] ?? null,
                "approvisionnement" => $result["Stock_Restant_Appro"] ?? null
            ]
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => $result["Message"] ?? "Erreur inconnue lors de l'enregistrement"
        ]);
    }
    exit;
}

function selectionner_ventes()
{
    header('Content-Type: application/json; charset=utf-8');
    $result = Vente::select_all();
    
    if (isset($result["Message"])) {
        echo json_encode([
            "succes" => false,
            "message" => $result["Message"]
        ]);
    } else {
        echo json_encode([
            "succes" => true,
            "data" => $result
        ]);
    }
    exit;
}

function selectionner_une_vente()
{
    header('Content-Type: application/json; charset=utf-8');
    $id_vente = isset($_GET["id_vente"]) ? intval($_GET["id_vente"]) : null;
    
    if (!$id_vente) {
        echo json_encode([
            "succes" => false,
            "message" => "ID vente manquant"
        ]);
        exit;
    }

    // Appel au modèle pour récupérer une vente spécifique
    $result = Vente::select_one($id_vente);
    
    if (isset($result["message"])) {
        echo json_encode([
            "succes" => false,
            "message" => $result["message"]
        ]);
    } else {
        echo json_encode([
            "succes" => true,
            "data" => $result
        ]);
    }
    exit;
}

function compter_ventes()
{
    header('Content-Type: application/json; charset=utf-8');
    $result = Vente::compterVentes();
    
    if (isset($result["Message"])) {
        echo json_encode([
            "succes" => false,
            "message" => $result["Message"]
        ]);
    } else {
        echo json_encode([
            "succes" => true,
            "data" => $result
        ]);
    }
    exit;
}

function fiche_journaliere_vente()
{
    header('Content-Type: application/json; charset=utf-8');
    $result = Vente::fiche_journaliere_vente();

    if (isset($result[0]) && is_array($result[0])) {
        echo json_encode([
            "succes" => true,
            "data" => $result
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => $result["Message"] ?? "Aucune vente enregistrée aujourd'hui"
        ]);
    }
    exit;
}