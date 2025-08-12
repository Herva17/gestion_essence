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

public static function fiche_stock() {
    // Connexion à la base de données
      $data = get_connection();
    
    try {
        $sql = "
        WITH 
        entrees AS (
            SELECT 
                a.id_produit,
                p.designation AS produit,
                a.id_approvisionnement,
                a.date_approvisionnement AS date_entree,
                a.quantite AS quantite_m3,
                a.quantite * 1000 AS quantite_litre,
                a.prix_unitaire,
                'ENTREE' AS type_mouvement
            FROM 
                approvisionnement a
            JOIN produit p ON a.id_produit = p.id
        ),
        
        sorties AS (
            SELECT 
                c.Id_appro,
                v.id_commande,
                v.date_vente AS date_sortie,
                v.quantite AS quantite_m3,
                v.quantite * 1000 AS quantite_litre,
                'SORTIE' AS type_mouvement
            FROM 
                vente v
            JOIN commandes c ON v.id_commande = c.id
        ),
        
        stock_fifo AS (
            SELECT 
                e.id_produit,
                e.produit,
                e.id_approvisionnement,
                e.date_entree,
                s.date_sortie,
                e.prix_unitaire,
                e.quantite_m3 AS quantite_entree_m3,
                e.quantite_litre AS quantite_entree_litre,
                COALESCE(s.quantite_m3, 0) AS quantite_sortie_m3,
                COALESCE(s.quantite_litre, 0) AS quantite_sortie_litre,
                (e.quantite_m3 - COALESCE(s.quantite_m3, 0)) AS stock_restant_m3,
                (e.quantite_litre - COALESCE(s.quantite_litre, 0)) AS stock_restant_litre
            FROM 
                entrees e
            LEFT JOIN sorties s ON e.id_approvisionnement = s.Id_appro
        )
        
        SELECT 
            id_produit,
            produit,
            id_approvisionnement,
            date_entree,
            date_sortie,
            prix_unitaire,
            quantite_entree_m3,
            quantite_entree_litre,
            quantite_sortie_m3,
            quantite_sortie_litre,
            stock_restant_m3,
            stock_restant_litre,
            (quantite_entree_m3 * prix_unitaire) AS valeur_entree,
            (quantite_sortie_m3 * prix_unitaire) AS valeur_sortie,
            (stock_restant_m3 * prix_unitaire) AS valeur_stock
        FROM 
            stock_fifo
        ORDER BY 
            id_produit, date_entree
        ";

        $stmt = $data->prepare($sql);
        $stmt->execute();
        
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calcul des totaux par produit
        $totaux = [];
        foreach ($result as $row) {
            $id_produit = $row['id_produit'];
            
            if (!isset($totaux[$id_produit])) {
                $totaux[$id_produit] = [
                    'produit' => $row['produit'],
                    'total_entree_m3' => 0,
                    'total_entree_litre' => 0,
                    'total_sortie_m3' => 0,
                    'total_sortie_litre' => 0,
                    'stock_restant_m3' => 0,
                    'stock_restant_litre' => 0,
                    'valeur_stock' => 0
                ];
            }
            
            $totaux[$id_produit]['total_entree_m3'] += $row['quantite_entree_m3'];
            $totaux[$id_produit]['total_entree_litre'] += $row['quantite_entree_litre'];
            $totaux[$id_produit]['total_sortie_m3'] += $row['quantite_sortie_m3'];
            $totaux[$id_produit]['total_sortie_litre'] += $row['quantite_sortie_litre'];
            $totaux[$id_produit]['stock_restant_m3'] += $row['stock_restant_m3'];
            $totaux[$id_produit]['stock_restant_litre'] += $row['stock_restant_litre'];
            $totaux[$id_produit]['valeur_stock'] += $row['valeur_stock'];
        }
        
        return [
            'success' => true,
            'data' => $result,
            'totaux' => $totaux
        ];
        
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Erreur lors de la récupération de la fiche de stock: ' . $e->getMessage()
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