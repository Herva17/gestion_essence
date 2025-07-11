<?php
require_once("./Models/Commande.php");
$retour = array();

function enregistrer_commande()
{
    header('Content-Type: application/json; charset=utf-8');
    $id_client = isset($_POST["id_client"]) ? intval($_POST["id_client"]) : null;
    $id_produit = isset($_POST["id_produit"]) ? intval($_POST["id_produit"]) : null;
    $id_comptable = isset($_POST["id_User"]) ? intval($_POST["id_User"]) : null;
    $quantite = isset($_POST["quantite"]) ? intval($_POST["quantite"]) : null;

    if (!$id_client || !$id_produit || !$id_comptable ||!$quantite) {
        echo json_encode([
            "succes" => false,
            "message" => "Les champs ID client, ID produit et quantité sont obligatoires"
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

    $result = Commande::enregistrer($id_client, $id_produit, $id_comptable, $quantite);

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
    $id = isset($_POST["id"]) ? intval($_POST["id"]) : null;
    $id_client = isset($_POST["id_client"]) ? intval($_POST["id_client"]) : null;
    $id_produit = isset($_POST["id_produit"]) ? intval($_POST["id_produit"]) : null;
    $id_comptable = isset($_POST["id_comptable"]) ? intval($_POST["id_comptable"]) : null;
    $quantite = isset($_POST["quantite"]) ? intval($_POST["quantite"]) : null;

    // Validation des champs obligatoires
    if (!$id || !$id_client || !$id_produit || !$quantite) {
        return [
            "Message" => "Tous les champs sont obligatoires pour la modification"
        ];
    }

    // Validation de la quantité
    if ($quantite <= 0) {
        return [
            "Message" => "La quantité doit être supérieure à zéro"
        ];
    }

    // Appel au modèle pour la modification
    return Commande::update($id, $id_client, $id_produit, $id_comptable, $quantite);
}

function selectionner_commandes()
{
    // Appel au modèle pour récupérer toutes les commandes
    return Commande::select_all();
}

function selectionner_une_commande()
{
    $id = isset($_POST["id"]) ? intval($_POST["id"]) : null;
    
    if (!$id) {
        return [
            "Message" => "ID commande manquant"
        ];
    }

    // Appel au modèle pour récupérer une commande spécifique
    return Commande::select_one($id);
}

function compter_commandes()
{
    // Appel au modèle pour compter les commandes
    return Commande::compterCommandes();
}

function supprimer_commande()
{
    $id = isset($_POST["id"]) ? intval($_POST["id"]) : null;
    
    if (!$id) {
        return [
            "Message" => "ID commande manquant"
        ];
    }

    // Appel au modèle pour supprimer une commande
    return Commande::delete($id);
}

function commandes_par_client()
{
    $id_client = isset($_GET["id_client"]) ? intval($_GET["id_client"]) : null;
    
    if (!$id_client) {
        return [
            "Message" => "ID client manquant"
        ];
    }

    // Appel au modèle pour filtrer les commandes par client
    return Commande::commandes_par_client($id_client);
}

function commandes_par_produit()
{
    $id_produit = isset($_GET["id_produit"]) ? intval($_GET["id_produit"]) : null;
    
    if (!$id_produit) {
        return [
            "Message" => "ID produit manquant"
        ];
    }

    // Appel au modèle pour filtrer les commandes par produit
    return Commande::commandes_par_produit($id_produit);
}

