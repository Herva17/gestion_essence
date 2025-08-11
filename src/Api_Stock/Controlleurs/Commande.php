<?php
require_once("./Models/Commande.php");
$retour = array();

function enregistrer_commande()
{
    header('Content-Type: application/json; charset=utf-8');
    $id_client = isset($_POST["id_client"]) ? intval($_POST["id_client"]) : null;
    $id_appro = isset($_POST["id_appro"]) ? intval($_POST["id_appro"]) : null;
    $id_comptable = isset($_POST["id_User"]) ? intval($_POST["id_User"]) : null;
    $quantite = isset($_POST["quantite"]) ? intval($_POST["quantite"]) : null;

    if (!$id_client || !$id_appro || !$id_comptable || !$quantite) {
        echo json_encode([
            "succes" => false,
            "message" => "Les champs ID client, ID approvisionnement et quantité sont obligatoires"
        ]);
        exit;
    }

    if ($quantite <= 0) {
        echo json_encode([
            "succes" => false,
            "message" => "La quantité doit être supérieure à zéro"
        ]);
        exit;
    }

    $result = Commande::enregistrer($id_client, $id_appro, $id_comptable, $quantite);

    if (isset($result["Reussite"])) {
        echo json_encode([
            "succes" => true,
            "message" => $result["Reussite"],
            "data" => $result["Dernier_Enregistrement"] ?? null
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => $result["Message"] ?? "Erreur inconnue"
        ]);
    }
    exit;
}

function modification_commande()
{
    header('Content-Type: application/json; charset=utf-8');
    $id = isset($_POST["id"]) ? intval($_POST["id"]) : null;
    $id_client = isset($_POST["id_client"]) ? intval($_POST["id_client"]) : null;
    $id_appro = isset($_POST["id_appro"]) ? intval($_POST["id_appro"]) : null;
    $id_comptable = isset($_POST["id_User"]) ? intval($_POST["id_User"]) : null;
    $quantite = isset($_POST["quantite"]) ? intval($_POST["quantite"]) : null;

    // Validation des champs obligatoires
    if (!$id || !$id_client || !$id_appro || !$quantite) {
        echo json_encode([
            "succes" => false,
            "message" => "Tous les champs sont obligatoires pour la modification"
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

    // Appel au modèle pour la modification
    $result = Commande::update($id, $id_client, $id_appro, $id_comptable, $quantite);
    
    if (isset($result["message"])) {
        echo json_encode([
            "succes" => true,
            "message" => $result["message"]
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => $result["Message"] ?? "Erreur lors de la modification"
        ]);
    }
    exit;
}

function selectionner_commandes()
{
    header('Content-Type: application/json; charset=utf-8');
    $result = Commande::select_all();
    
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

function selectionner_une_commande()
{
    header('Content-Type: application/json; charset=utf-8');
    $id = isset($_POST["id"]) ? intval($_POST["id"]) : null;
    
    if (!$id) {
        echo json_encode([
            "succes" => false,
            "message" => "ID commande manquant"
        ]);
        exit;
    }

    // Appel au modèle pour récupérer une commande spécifique
    $result = Commande::select_one($id);
    
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

function compter_commandes()
{
    header('Content-Type: application/json; charset=utf-8');
    $result = Commande::compterCommandes();
    
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

function supprimer_commande()
{
    header('Content-Type: application/json; charset=utf-8');
    $id = isset($_POST["id"]) ? intval($_POST["id"]) : null;
    
    if (!$id) {
        echo json_encode([
            "succes" => false,
            "message" => "ID commande manquant"
        ]);
        exit;
    }

    // Appel au modèle pour supprimer une commande
    $result = Commande::delete($id);
    
    if (isset($result["Reussite"])) {
        echo json_encode([
            "succes" => true,
            "message" => $result["Reussite"]
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => $result["Message"] ?? "Erreur lors de la suppression"
        ]);
    }
    exit;
}

function commandes_par_client()
{
    header('Content-Type: application/json; charset=utf-8');
    $id_client = isset($_GET["id_client"]) ? intval($_GET["id_client"]) : null;
    
    if (!$id_client) {
        echo json_encode([
            "succes" => false,
            "message" => "ID client manquant"
        ]);
        exit;
    }

    // Appel au modèle pour filtrer les commandes par client
    $result = Commande::commandes_par_client($id_client);
    
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

function commandes_par_produit()
{
    header('Content-Type: application/json; charset=utf-8');
    $id_produit = isset($_GET["id_produit"]) ? intval($_GET["id_produit"]) : null;
    
    if (!$id_produit) {
        echo json_encode([
            "succes" => false,
            "message" => "ID produit manquant"
        ]);
        exit;
    }

    // Appel au modèle pour filtrer les commandes par produit
    $result = Commande::commandes_par_produit($id_produit);
    
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

function fiche_journaliere_vente()
{
    header('Content-Type: application/json; charset=utf-8');
    $result = Commande::fiche_journaliere_vente(); 

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