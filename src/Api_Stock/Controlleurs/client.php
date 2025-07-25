<?php
require_once("./Models/Client.php");

// Fonction pour enregistrer un client
function save_client()
{
    $Nom = isset($_POST["nom"]) ? htmlspecialchars(trim($_POST["nom"])) : null;
    $Prenom = isset($_POST["prenom"]) ? htmlspecialchars(trim($_POST["prenom"])) : null;
    $Sexe = isset($_POST["sexe"]) ? htmlspecialchars(trim($_POST["sexe"])) : null;
    $Adresse = isset($_POST["adresse"]) ? htmlspecialchars(trim($_POST["adresse"])) : null;
    $Telephone = isset($_POST["telephone"]) ? htmlspecialchars(trim($_POST["telephone"])) : null;
    $Email = isset($_POST["email"]) ? htmlspecialchars(trim($_POST["email"])) : null;
    $Date_Creation = isset($_POST["date_creation"]) ? htmlspecialchars(trim($_POST["date_creation"])) : date("Y-m-d H:i:s");

    if (!$Nom || !$Prenom || !$Sexe || !$Telephone || !$Email) {
        echo json_encode([
            "succes" => false,
            "message" => "Les champs nom, prénom, téléphone et email sont obligatoires"
        ]);
        return;
    }

    $result = Client::save($Nom, $Prenom, $Sexe, $Adresse, $Telephone, $Email, $Date_Creation);

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
}

// Fonction pour supprimer un client
function delete_client()
{
    header('Content-Type: application/json');
    $Id_Client = isset($_POST["id"]) ? htmlspecialchars(trim($_POST["id"])) : null;

    if (!$Id_Client) {
        echo json_encode([
            "succes" => false,
            "message" => "ID client manquant"
        ]);
        return;
    }

    $result = Client::delete($Id_Client);
    if (isset($result["Reussite"])) {
        echo json_encode([
            "succes" => true,
            "message" => $result["Reussite"]
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => $result["Message"] ?? "Erreur inconnue"
        ]);
    }
}

// Fonction pour modifier un client
function update_client()
{
    header('Content-Type: application/json');
    $Id_Client = isset($_POST["id"]) ? htmlspecialchars(trim($_POST["id"])) : null;
    $Nom = isset($_POST["nom"]) ? htmlspecialchars(trim($_POST["nom"])) : null;
    $Prenom = isset($_POST["prenom"]) ? htmlspecialchars(trim($_POST["prenom"])) : null;
    $Sexe = isset($_POST["sexe"]) ? htmlspecialchars(trim($_POST["sexe"])) : null;
    $Adresse = isset($_POST["adresse"]) ? htmlspecialchars(trim($_POST["adresse"])) : null;
    $Telephone = isset($_POST["telephone"]) ? htmlspecialchars(trim($_POST["telephone"])) : null;
    $Email = isset($_POST["email"]) ? htmlspecialchars(trim($_POST["email"])) : null;

    if (!$Id_Client || !$Nom || !$Prenom || !$Telephone || !$Email) {
        echo json_encode([
            "succes" => false,
            "message" => "Tous les champs sont obligatoires pour la modification"
        ]);
        return;
    }

    $result = Client::update($Id_Client, $Nom, $Prenom, $Sexe, $Adresse, $Telephone, $Email);
    if (isset($result["Reussite"])) {
        echo json_encode([
            "succes" => true,
            "message" => $result["Reussite"]
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => $result["Message"] ?? "Erreur inconnue"
        ]);
    }
}

// Fonction pour récupérer tous les clients
function select_all_clients()
{
    header('Content-Type: application/json');
    $result = Client::select_all();
    if (isset($result[0]) && is_array($result[0])) {
        echo json_encode([
            "succes" => true,
            "data" => $result
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => $result["Message"] ?? "Aucun client trouvé"
        ]);
    }
}

// Fonction pour récupérer un client spécifique
function select_one_client()
{
    header('Content-Type: application/json');
    $Id_Client = isset($_GET["id"]) ? htmlspecialchars(trim($_GET["id"])) : null;

    if (!$Id_Client) {
        echo json_encode([
            "succes" => false,
            "message" => "ID client manquant"
        ]);
        return;
    }

    $result = Client::select_one($Id_Client);
    if (isset($result[0]) && is_array($result[0])) {
        echo json_encode([
            "succes" => true,
            "data" => $result[0]
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => $result["Message"] ?? "Aucun client trouvé"
        ]);
    }
}

// Fonction pour compter les clients
function count_clients()
{
    header('Content-Type: application/json');
    $result = Client::count_clients();
    if (isset($result[0]["total"])) {
        echo json_encode([
            "succes" => true,
            "total" => $result[0]["total"]
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => $result["Message"] ?? "Aucune donnée disponible"
        ]);
    }
}

// Fonction pour rechercher des clients
function search_clients()
{
    header('Content-Type: application/json');
    $search_term = isset($_GET["search"]) ? htmlspecialchars(trim($_GET["search"])) : null;

    if (!$search_term) {
        echo json_encode([
            "succes" => false,
            "message" => "Terme de recherche manquant"
        ]);
        return;
    }

    $result = Client::search($search_term);
    if (isset($result[0]) && is_array($result[0])) {
        echo json_encode([
            "succes" => true,
            "data" => $result
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => $result["Message"] ?? "Aucun client trouvé"
        ]);
    }
}