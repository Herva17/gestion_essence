<?php
require_once("./Models/Categorie.php");
$retour = array();

function enregistrer_categorie()
{
    // Récupération et sécurisation des données
    $designation = isset($_POST["designation"]) ? htmlspecialchars(trim($_POST["designation"])) : null;
    $description = isset($_POST["description"]) ? htmlspecialchars(trim($_POST["description"])) : null;

    // Validation des champs obligatoires
    if (!$designation) {
        return [
            "Message" => "Le champ designation est obligatoire"
        ];
    }

    // Appel au modèle pour l'enregistrement
    return Categorie::enregistrer($designation, $description);
}

function modification_categorie()
{
    $id = isset($_POST["id"]) ? htmlspecialchars(trim($_POST["id"])) : null;
    $designation = isset($_POST["designation"]) ? htmlspecialchars(trim($_POST["designation"])) : null;
    $description = isset($_POST["description"]) ? htmlspecialchars(trim($_POST["description"])) : null;

    // Validation des champs obligatoires
    if (!$id || !$designation) {
        return [
            "Message" => "L'ID et la designation sont obligatoires pour la modification"
        ];
    }

    // Appel au modèle pour la modification
    return Categorie::update($id, $designation, $description);
}

function selectionner_categories()
{
    // Appel au modèle pour récupérer toutes les catégories
    return Categorie::select_all();
}

function selectionner_une_categorie()
{
    $id = isset($_POST["id"]) ? htmlspecialchars(trim($_POST["id"])) : null;
    
    if (!$id) {
        return [
            "Message" => "ID catégorie manquant"
        ];
    }

    // Appel au modèle pour récupérer une catégorie spécifique
    return Categorie::select_one($id);
}

function compter_categories()
{
    // Appel au modèle pour compter les catégories
    return Categorie::compterCategories();
}

function supprimer_categorie()
{
    $id = isset($_POST["id"]) ? htmlspecialchars(trim($_POST["id"])) : null;
    
    if (!$id) {
        return [
            "Message" => "ID catégorie manquant"
        ];
    }

    // Appel au modèle pour supprimer une catégorie
    return Categorie::delete($id);
}

function rechercher_categories()
{
    $search_term = isset($_GET["search"]) ? htmlspecialchars(trim($_GET["search"])) : null;
    
    if (!$search_term) {
        return [
            "Message" => "Terme de recherche manquant"
        ];
    }

    // Appel au modèle pour rechercher des catégories
    return Categorie::search($search_term);
}