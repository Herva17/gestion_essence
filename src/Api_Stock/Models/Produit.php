<?php
require_once("./Config.php");

class Produit
{
    /**
     * Enregistre un nouveau produit
     * @param string $designation
     * @param string $description
     * @param string|null $date_creation
     * @return array
     */
    public static function enregistrer($designation, $description, $date_creation = null)
    {
        $data = get_connection();
        
        if ($date_creation === null) {
            $date_creation = date("Y-m-d H:i:s");
        }

        try {
            $query = $data->prepare("INSERT INTO produit 
                                   (designation, description, date_creation) 
                                   VALUES (:designation, :description, :date_creation)");

            $success = $query->execute([
                ':designation' => htmlspecialchars(trim($designation)),
                ':description' => htmlspecialchars(trim($description)),
                ':date_creation' => $date_creation
            ]);

            if ($success) {
                $dernier_id = $data->lastInsertId();
                return [
                    "success" => true,
                    "message" => "Produit enregistré avec succès",
                    "id" => $dernier_id
                ];
            } else {
                return [
                    "success" => false,
                    "message" => "Échec d'enregistrement du produit"
                ];
            }
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Erreur: " . $e->getMessage()
            ];
        }
    }

    /**
     * Met à jour un produit existant
     * @param int $id
     * @param string $designation
     * @param string $description
     * @return array
     */
    public static function update($id, $designation, $description)
    {
        $data = get_connection();
        
        try {
            $query = $data->prepare("UPDATE produit 
                                   SET designation = :designation,
                                       description = :description
                                   WHERE id = :id");

            $success = $query->execute([
                ':id' => $id,
                ':designation' => htmlspecialchars(trim($designation)),
                ':description' => htmlspecialchars(trim($description))
            ]);

            if ($success) {
                return [
                    "success" => true,
                    "message" => "Produit mis à jour avec succès"
                ];
            } else {
                return [
                    "success" => false,
                    "message" => "Échec de la mise à jour"
                ];
            }
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Erreur: " . $e->getMessage()
            ];
        }
    }

    /**
     * Récupère tous les produits
     * @return array
     */
    public static function select_all()
    {
        $data = get_connection();
        $donnees = $data->query("SELECT * FROM produit ORDER BY date_creation DESC")->fetchAll(PDO::FETCH_ASSOC);
        return $donnees ?: [];
    }

    /**
     * Récupère un produit spécifique
     * @param int $id
     * @return array|null
     */
    public static function select_one($id)
    {
        $data = get_connection();
        $query = $data->prepare("SELECT * FROM produit WHERE id = :id");
        $query->execute([':id' => $id]);
        return $query->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Supprime un produit
     * @param int $id
     * @return array
     */
    public static function delete($id)
    {
        $data = get_connection();
        
        try {
            $query = $data->prepare("DELETE FROM produit WHERE id = :id");
            $success = $query->execute([':id' => $id]);

            if ($success) {
                return [
                    "success" => true,
                    "message" => "Produit supprimé avec succès"
                ];
            } else {
                return [
                    "success" => false,
                    "message" => "Échec de suppression"
                ];
            }
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Erreur: " . $e->getMessage()
            ];
        }
    }

    /**
     * Compte le nombre total de produits
     * @return int
     */
    public static function count()
    {
        $data = get_connection();
        $result = $data->query("SELECT COUNT(id) as total FROM produit")->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total'];
    }

    /**
     * Recherche des produits
     * @param string $search_term
     * @return array
     */
    public static function search($search_term)
    {
        $data = get_connection();
        $query = $data->prepare("SELECT * FROM produit
                               WHERE designation LIKE :search 
                               OR description LIKE :search
                               ORDER BY date_creation DESC");
        $query->execute([':search' => "%$search_term%"]);
        return $query->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Récupère les produits récents
     * @param int $limit
     * @return array
     */
    public static function get_recent($limit = 5)
    {
        $data = get_connection();
        $query = $data->prepare("SELECT * FROM produit 
                               ORDER BY date_creation DESC 
                               LIMIT :limit");
        $query->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}