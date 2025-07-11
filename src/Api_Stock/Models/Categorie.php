<?php
require_once("./Config.php");

class Categorie
{
    public $response = array();

    public static function enregistrer($designation, $description = null)
    {
        $data = get_connection();
        $date_creation = date("Y-m-d H:i:s");
        
        $query = $data->prepare("INSERT INTO categories (designation, description, date_creation) 
                                VALUES (:designation, :description, :date_creation)");
        
        if ($query->execute([
            ':designation' => $designation,
            ':description' => $description,
            ':date_creation' => $date_creation
        ])) {
            $response["message"] = "Catégorie enregistrée avec succès";
            $response["Dernier_Enregistrement"] = self::afficher_dernier_enreg();
            return $response;
        } else {
            $response["Message"] = "Échec d'enregistrement de la catégorie";
            return $response;
        }
    }

    public static function afficher_dernier_enreg()
    {
        $data = get_connection();
        $donnees = $data->query("SELECT * FROM categories ORDER BY id DESC LIMIT 1")->fetchAll();
        if (count($donnees) > 0) {
            return $donnees;
        }
    }

    public static function select_all()
    {
        $data = get_connection();
        $donnees = $data->query("SELECT * FROM categories ORDER BY designation")->fetchAll();
        if (count($donnees) > 0) {
            return $donnees;
        } else {
            $response["Message"] = "Aucune catégorie disponible";
            return $response;
        }
    }

    public static function update($id, $designation, $description = null)
    {
        $data = get_connection();
        $query = $data->prepare("UPDATE categories 
                                SET designation = :designation, 
                                    description = :description 
                                WHERE id = :id");
        
        if ($query->execute([
            ':id' => $id,
            ':designation' => $designation,
            ':description' => $description
        ])) {
            $response["message"] = "Catégorie modifiée avec succès";
            return $response;
        } else {
            $response["Message"] = "Échec de modification de la catégorie";
            return $response;
        }
    }

    public static function select_one($id)
    {
        $data = get_connection();
        $donnees = $data->query("SELECT * FROM categories WHERE id = '$id'")->fetchAll();
        if (count($donnees) > 0) {
            return $donnees;
        } else {
            $response["message"] = "Aucune catégorie trouvée avec cet ID";
            return $response;
        }
    }

    public static function compterCategories()
    {
        $data = get_connection();
        $donnees = $data->query("SELECT COUNT(id) as total FROM categories")->fetchAll();
        if (count($donnees) > 0) {
            return $donnees;
        } else {
            $response["Message"] = "Aucune donnée disponible";
            return $response;
        }
    }

    public static function delete($id)
    {
        $data = get_connection();
        if ($data->query("DELETE FROM categories WHERE id = '$id'")) {
            $response["Reussite"] = "Catégorie supprimée avec succès";
            return $response;
        } else {
            $response["Message"] = "Échec de suppression de la catégorie";
            return $response;
        }
    }

    public static function search($search_term)
    {
        $data = get_connection();
        $query = $data->prepare("SELECT * FROM categories 
                                WHERE designation LIKE :search OR description LIKE :search");
        $query->execute([':search' => "%$search_term%"]);
        $results = $query->fetchAll();
        
        if (count($results) > 0) {
            return $results;
        } else {
            $response["Message"] = "Aucune catégorie trouvée pour cette recherche";
            return $response;
        }
    }
}