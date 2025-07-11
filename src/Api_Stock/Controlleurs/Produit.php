<?php
require_once("./Models/Produit.php");

header('Content-Type: application/json; charset=utf-8');

function enregistrer_produit()
{
// ...dans la fonction enregistrer_produit()...
$nom = isset($_POST["nom"]) ? htmlspecialchars(trim($_POST["nom"])) : null;
$description = isset($_POST["description"]) ? htmlspecialchars(trim($_POST["description"])) : null;
$quantite = isset($_POST["quantite"]) ? intval($_POST["quantite"]) : null;
$prix_unitaire = isset($_POST["prix_unitaire"]) ? floatval($_POST["prix_unitaire"]) : null;
$id_categorie = isset($_POST["id_categorie"]) ? intval($_POST["id_categorie"]) : null;
$id_gerant = isset($_POST["id_User"]) ? intval($_POST["id_User"]) : null;

if (!$nom || !$quantite || !$prix_unitaire || !$id_categorie) {
    return [
        "succes" => false,
        "message" => "Les champs nom, quantité, prix unitaire et catégorie sont obligatoires"
    ];
}
if ($quantite < 0 || $prix_unitaire <= 0) {
    return [
        "succes" => false,
        "message" => "La quantité doit être positive et le prix unitaire doit être supérieur à zéro"
    ];
}
$result = Produit::enregistrer($nom, $description, $quantite, $prix_unitaire, $id_categorie, $id_gerant);
if (isset($result["Reussite"])) {
    return [
        "succes" => true,
        "message" => $result["Reussite"],
        "data" => $result["Dernier_Enregistrement"] ?? null
    ];
} else {
    return [
        "succes" => false,
        "message" => $result["Message"] ?? "Erreur inconnue"
    ];
}
}

function modifier_produit()
{
    $id = isset($_POST["id"]) ? intval($_POST["id"]) : null;
    $nom = isset($_POST["nom"]) ? htmlspecialchars(trim($_POST["nom"])) : null;
    $description = isset($_POST["description"]) ? htmlspecialchars(trim($_POST["description"])) : null;
    $quantite = isset($_POST["quantite"]) ? intval($_POST["quantite"]) : null;
    $prix_unitaire = isset($_POST["prix_unitaire"]) ? floatval($_POST["prix_unitaire"]) : null;
    $id_categorie = isset($_POST["id_categorie"]) ? intval($_POST["id_categorie"]) : null;
    $id_approvisionnement = isset($_POST["id_approvisionnement"]) ? intval($_POST["id_approvisionnement"]) : null;
    $id_gerant = isset($_POST["id_gerant"]) ? intval($_POST["id_gerant"]) : null;

    if (!$id || !$nom || !$quantite || !$prix_unitaire || !$id_categorie) {
        return [
            "succes" => false,
            "message" => "Tous les champs sont obligatoires pour la modification"
        ];
    }
    if ($quantite < 0 || $prix_unitaire <= 0) {
        return [
            "succes" => false,
            "message" => "La quantité doit être positive et le prix unitaire doit être supérieur à zéro"
        ];
    }
    $result = Produit::update($id, $nom, $description, $quantite, $prix_unitaire, $id_categorie, $id_approvisionnement, $id_gerant);
    if (isset($result["Reussite"])) {
        return [
            "succes" => true,
            "message" => $result["Reussite"]
        ];
    } else {
        return [
            "succes" => false,
            "message" => $result["Message"] ?? "Erreur inconnue"
        ];
    }
}

function selectionner_produits()
{
    $result = Produit::select_all();
    if (isset($result[0]) && is_array($result[0])) {
        return [
            "succes" => true,
            "data" => $result
        ];
    } else {
        return [
            "succes" => false,
            "message" => $result["Message"] ?? "Aucun produit trouvé"
        ];
    }
}

function selectionner_un_produit()
{
    $id = isset($_POST["id"]) ? intval($_POST["id"]) : null;
    if (!$id) {
        return [
            "succes" => false,
            "message" => "ID produit manquant"
        ];
    }
    $result = Produit::select_one($id);
    if (isset($result[0]) && is_array($result[0])) {
        return [
            "succes" => true,
            "data" => $result[0]
        ];
    } else {
        return [
            "succes" => false,
            "message" => $result["Message"] ?? "Aucun produit trouvé"
        ];
    }
}

function compter_produits()
{
    $result = Produit::compterProduits();
    if (isset($result[0]["total"])) {
        return [
            "succes" => true,
            "total" => $result[0]["total"]
        ];
    } else {
        return [
            "succes" => false,
            "message" => $result["Message"] ?? "Aucune donnée disponible"
        ];
    }
}

function supprimer_produit()
{
    $id = isset($_POST["id"]) ? intval($_POST["id"]) : null;
    if (!$id) {
        return [
            "succes" => false,
            "message" => "ID produit manquant"
        ];
    }
    $result = Produit::delete($id);
    if (isset($result["Reussite"])) {
        return [
            "succes" => true,
            "message" => $result["Reussite"]
        ];
    } else {
        return [
            "succes" => false,
            "message" => $result["Message"] ?? "Erreur inconnue"
        ];
    }
}

function rechercher_produits()
{
    $search_term = isset($_GET["search"]) ? htmlspecialchars(trim($_GET["search"])) : null;
    if (!$search_term) {
        return [
            "succes" => false,
            "message" => "Terme de recherche manquant"
        ];
    }
    $result = Produit::search($search_term);
    if (isset($result[0]) && is_array($result[0])) {
        return [
            "succes" => true,
            "data" => $result
        ];
    } else {
        return [
            "succes" => false,
            "message" => $result["Message"] ?? "Aucun produit trouvé"
        ];
    }
}

function produits_par_categorie()
{
    $id_categorie = isset($_GET["id_categorie"]) ? intval($_GET["id_categorie"]) : null;
    if (!$id_categorie) {
        return [
            "succes" => false,
            "message" => "ID catégorie manquant"
        ];
    }
    $result = Produit::produits_par_categorie($id_categorie);
    if (isset($result[0]) && is_array($result[0])) {
        return [
            "succes" => true,
            "data" => $result
        ];
    } else {
        return [
            "succes" => false,
            "message" => $result["Message"] ?? "Aucun produit trouvé dans cette catégorie"
        ];
    }
}

// Gestion des requêtes
