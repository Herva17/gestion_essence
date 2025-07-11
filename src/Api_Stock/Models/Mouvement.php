<?php
require_once("./Config.php");

class Mouvement
{
    public $me = array();

    public static function save($designation, $type, $date_mouvement)
    {
        $data = get_connection();
    
        // Insérer le nouveau mouvement
        $query = $data->prepare("INSERT INTO mouvements (designation, type, date_mouvement) 
                                 VALUES (:designation, :type, :date_mouvement)");
        $success = $query->execute([
            ':designation' => $designation,
            ':type' => $type,
            ':date_mouvement' => $date_mouvement
        ]);
    
        if ($success) {
            return [
                "succes" => true,
                "message" => "Mouvement enregistré",
                "dernier_id" => $data->lastInsertId()
            ];
        } else {
            return [
                "succes" => false,
                "message" => "Echec d'enregistrement du mouvement"
            ];
        }
    }

    public static function delete($id)
    {
        $data = get_connection();
        $query = $data->prepare("DELETE FROM mouvements WHERE id = :id");
        $success = $query->execute([':id' => $id]);

        if ($success && $query->rowCount() > 0) {
            return [
                "succes" => true,
                "message" => "Mouvement supprimé avec succès"
            ];
        } else {
            return [
                "succes" => false,
                "message" => "Echec de suppression ou mouvement non trouvé"
            ];
        }
    }

    public static function update($id, $designation, $type, $date_mouvement)
    {
        $data = get_connection();
        $query = $data->prepare("UPDATE mouvements 
                               SET designation = :designation, 
                                   type = :type, 
                                   date_mouvement = :date_mouvement
                               WHERE id = :id");
        $success = $query->execute([
            ':designation' => $designation,
            ':type' => $type,
            ':date_mouvement' => $date_mouvement,
            ':id' => $id
        ]);

        if ($success) {
            return [
                "succes" => true,
                "message" => "Mouvement mis à jour avec succès"
            ];
        } else {
            return [
                "succes" => false,
                "message" => "Echec de mise à jour du mouvement"
            ];
        }
    }

    public static function select_all()
    {
        $data = get_connection();
        $donnees = $data->query("SELECT * FROM mouvements ORDER BY date_mouvement DESC")->fetchAll();
        
        if (count($donnees) > 0) {
            return [
                "succes" => true,
                "data" => $donnees
            ];
        } else {
            return [
                "succes" => false,
                "message" => "Aucun mouvement trouvé"
            ];
        }
    }

    public static function select_one($id)
    {
        $data = get_connection();
        $query = $data->prepare("SELECT * FROM mouvements WHERE id = :id");
        $query->execute([':id' => $id]);
        $donnees = $query->fetchAll();

        if (count($donnees) > 0) {
            return [
                "succes" => true,
                "data" => $donnees[0]
            ];
        } else {
            return [
                "succes" => false,
                "message" => "Mouvement non trouvé"
            ];
        }
    }

    public static function count()
    {
        $data = get_connection();
        $donnees = $data->query("SELECT COUNT(id) as total FROM mouvements")->fetch();
        
        return [
            "succes" => true,
            "total" => $donnees['total']
        ];
    }

    public static function search($search_term)
    {
        $data = get_connection();
        $query = $data->prepare("SELECT * FROM mouvements 
                                WHERE designation LIKE :search 
                                OR type LIKE :search
                                ORDER BY date_mouvement DESC");
        $query->execute([':search' => "%$search_term%"]);
        $results = $query->fetchAll();
        
        if (count($results) > 0) {
            return [
                "succes" => true,
                "data" => $results
            ];
        } else {
            return [
                "succes" => false,
                "message" => "Aucun mouvement trouvé"
            ];
        }
    }

    public static function filter_by_type($type)
    {
        $data = get_connection();
        $query = $data->prepare("SELECT * FROM mouvements 
                                WHERE type = :type
                                ORDER BY date_mouvement DESC");
        $query->execute([':type' => $type]);
        $results = $query->fetchAll();
        
        if (count($results) > 0) {
            return [
                "succes" => true,
                "data" => $results
            ];
        } else {
            return [
                "succes" => false,
                "message" => "Aucun mouvement trouvé pour ce type"
            ];
        }
    }

    public static function filter_by_date($date_debut, $date_fin)
    {
        $data = get_connection();
        $query = $data->prepare("SELECT * FROM mouvements 
                                WHERE date_mouvement BETWEEN :date_debut AND :date_fin
                                ORDER BY date_mouvement DESC");
        $query->execute([
            ':date_debut' => $date_debut,
            ':date_fin' => $date_fin
        ]);
        $results = $query->fetchAll();
        
        if (count($results) > 0) {
            return [
                "succes" => true,
                "data" => $results
            ];
        } else {
            return [
                "succes" => false,
                "message" => "Aucun mouvement trouvé pour cette période"
            ];
        }
    }
}