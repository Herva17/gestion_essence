<?php
require_once("./Config.php");

class Vente
{
    public $response = array();

    public static function enregistrer($id_commande, $quantite, $date_vente)
    {
        $data = get_connection();

        try {
            $data->beginTransaction();

            // 1. Vérifier la commande et récupérer les infos
            $query = $data->prepare("
                SELECT c.quantite as quantite_commande, 
                       a.id_approvisionnement, 
                       a.quantite as quantite_appro, 
                       a.id_produit,
                       p.designation,
                       a.prix_unitaire
                FROM commandes c
                JOIN approvisionnement a ON c.Id_appro = a.id_approvisionnement
                JOIN produit p ON a.id_produit = p.id
                WHERE c.id = :id_commande
            ");
            $query->execute([':id_commande' => $id_commande]);
            $commande = $query->fetch();

            if (!$commande) {
                return ["Message" => "Commande introuvable"];
            }

            // 2. Vérifier les quantités
            if ($quantite <= 0) {
                return ["Message" => "La quantité doit être positive"];
            }

            if ($quantite > $commande['quantite_commande']) {
                return ["Message" => "Quantité vendue supérieure à la quantité commandée"];
            }

            if ($quantite > $commande['quantite_appro']) {
                return ["Message" => "Quantité vendue supérieure au stock approvisionné"];
            }

            // 3. Calculer les nouveaux stocks
            $nouvelle_quantite_commande = $commande['quantite_commande'] - $quantite;
            $nouvelle_quantite_appro = $commande['quantite_appro'] - $quantite;

            // 4. Mettre à jour la commande
            $query = $data->prepare("UPDATE commandes SET quantite = :quantite WHERE id = :id_commande");
            $query->execute([
                ':quantite' => $nouvelle_quantite_commande,
                ':id_commande' => $id_commande
            ]);

            // 5. Mettre à jour l'approvisionnement
            $query = $data->prepare("UPDATE approvisionnement SET quantite = :quantite WHERE id_approvisionnement = :id_appro");
            $query->execute([
                ':quantite' => $nouvelle_quantite_appro,
                ':id_appro' => $commande['id_approvisionnement']]);

            // 6. Enregistrer la vente
            $query = $data->prepare("INSERT INTO vente (id_commande, quantite, date_vente) 
                                    VALUES (:id_commande, :quantite, :date_vente)");
            $success = $query->execute([
                ':id_commande' => $id_commande,
                ':quantite' => $quantite,
                ':date_vente' => $date_vente
            ]);

            if ($success) {
                // Enregistrer le mouvement de vente
                $query_mvt = $data->prepare("
                    INSERT INTO mouvements (designation, Quantite, Prix_Unitaire, type, date_mouvement)
                    VALUES (:designation, :quantite, :prix_unitaire, 'vente', :date_mouvement)
                ");
                $query_mvt->execute([
                    ':designation' => $commande['designation'],
                    ':quantite' => $quantite,
                    ':prix_unitaire' => $commande['prix_unitaire'],
                    ':date_mouvement' => $date_vente
                ]);

                $data->commit();

                return [
                    "Reussite" => "Vente enregistrée avec succès",
                    "Dernier_Enregistrement" => self::afficher_dernier_enreg(),
                    "Stock_Restant_Commande" => $nouvelle_quantite_commande,
                    "Stock_Restant_Appro" => $nouvelle_quantite_appro
                ];
            } else {
                $data->rollBack();
                return ["Message" => "Échec d'enregistrement de la vente"];
            }
        } catch (Exception $e) {
            $data->rollBack();
            return ["Message" => "Erreur: " . $e->getMessage()];
        }
    }

    public static function afficher_dernier_enreg()
    {
        $data = get_connection();
        $donnees = $data->query("SELECT * FROM vente ORDER BY id_vente DESC LIMIT 1")->fetchAll();
        if (count($donnees) > 0) {
            return $donnees;
        }
    }

    public static function select_all()
    {
        $data = get_connection();
        $donnees = $data->query("
            SELECT v.*, 
                   c.id_client, 
                   CONCAT(cl.nom, ' ', cl.prenom) as client,
                   p.designation as produit,
                   a.prix_unitaire,
                   (v.quantite * a.prix_unitaire) as montant_total,
                   u.nom as vendeur
            FROM vente v
            JOIN commandes c ON v.id_commande = c.id
            JOIN clients cl ON c.id_client = cl.id
            JOIN approvisionnement a ON c.Id_appro = a.id_approvisionnement
            JOIN produit p ON a.id_produit = p.id
            JOIN utilisateurs u ON c.id_User = u.id
            ORDER BY v.date_vente DESC
        ")->fetchAll();
        
        if (count($donnees) > 0) {
            return $donnees;
        } else {
            $response["Message"] = "Aucune vente disponible";
            return $response;
        }
    }

    public static function select_one($id_vente)
    {
        $data = get_connection();
        $donnees = $data->query("
            SELECT v.*, 
                   c.id_client, 
                   CONCAT(cl.nom, ' ', cl.prenom) as client,
                   p.designation as produit,
                   a.prix_unitaire,
                   (v.quantite * a.prix_unitaire) as montant_total,
                   u.nom as vendeur
            FROM vente v
            JOIN commandes c ON v.id_commande = c.id
            JOIN clients cl ON c.id_client = cl.id
            JOIN approvisionnement a ON c.Id_appro = a.id_approvisionnement
            JOIN produits p ON a.id_produit = p.id
            JOIN utilisateurs u ON c.id_User = u.id
            WHERE v.id_vente = '$id_vente'
        ")->fetchAll();
        
        if (count($donnees) > 0) {
            return $donnees;
        } else {
            $response["message"] = "Aucune vente trouvée avec cet ID";
            return $response;
        }
    }

    public static function compterVentes()
    {
        $data = get_connection();
        $donnees = $data->query("SELECT COUNT(id_vente) as total FROM vente")->fetchAll();
        if (count($donnees) > 0) {
            return $donnees;
        } else {
            $response["Message"] = "Aucune donnée disponible";
            return $response;
        }
    }

    public static function fiche_journaliere_vente()
    {
        $pdo = get_connection();

        $sql = "
            SELECT 
                DATE(v.date_vente) AS date_vente,
                p.designation AS produit,
                v.quantite,
                a.prix_unitaire AS prix_usd,
                (v.quantite * a.prix_unitaire) AS total_usd,
                (v.quantite * a.prix_unitaire * 3000) AS total_fc,
                CONCAT(cl.prenom, ' ', cl.nom) AS client,
                CONCAT(u.prenom, ' ', u.nom) AS vendeur
            FROM 
                vente v
            JOIN 
                commandes c ON v.id_commande = c.id
            JOIN 
                approvisionnement a ON c.Id_appro = a.id_approvisionnement
            JOIN
                produits p ON a.id_produit = p.id
            LEFT JOIN 
                clients cl ON c.id_client = cl.id
            LEFT JOIN 
                utilisateurs u ON c.id_User = u.id
            WHERE 
                DATE(v.date_vente) = CURDATE()
            ORDER BY 
                v.date_vente DESC
        ";

        $stmt = $pdo->query($sql);
        $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $resultats ?: ["Message" => "Aucune vente enregistrée pour aujourd’hui"];
    }
}