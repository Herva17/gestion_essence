<?php
require_once("./Config.php");

class Commande
{
    public $response = array();
public static function enregistrer($id_client, $id_appro, $id_comptable, $quantite)
{
    $data = get_connection();
    $date_commande = date("Y-m-d H:i:s");

    // Récupérer les infos du produit via l'approvisionnement
    $query = $data->prepare("
        SELECT a.quantite as stock_appro, a.prix_unitaire, p.id as id_produit, p.designation 
        FROM approvisionnement a
        JOIN produit p ON a.id_produit = p.id
        WHERE a.id_approvisionnement = :id_appro
    ");
    $query->execute([':id_appro' => $id_appro]);
    $appro = $query->fetch();

    if (!$appro) {
        return ["Message" => "Approvisionnement introuvable"];
    }

    // Vérifier le stock disponible (quantité dans l'approvisionnement)
    if ($quantite > $appro['stock_appro']) {
        return ["Message" => "Quantité commandée supérieure au stock disponible"];
    }

    // Calculer le stock restant après commande
    $stock_restant = $appro['stock_appro'] - $quantite;

    // Vérifier si le stock restant est suffisant (au moins 50)
    if ($stock_restant < 50) {
        return ["Message" => "Stock insuffisant, reste: $stock_restant (le stock doit être au moins de 50 restants)"];
    }

    // Enregistrer la commande
    $query = $data->prepare("INSERT INTO commandes (id_client, Id_appro, id_User, quantite, date_commande) 
                        VALUES (:id_client, :Id_appro, :id_User, :quantite, :date_commande)");
    $success = $query->execute([
        ':id_client' => $id_client,
        ':Id_appro' => $id_appro,
        ':id_User' => $id_comptable,
        ':quantite' => $quantite,
        ':date_commande' => $date_commande
    ]);

    if ($success) {
        // Enregistrer le mouvement (sortie) sans toucher au stock
        $designation = $appro['designation'];
        $prix_unitaire = $appro['prix_unitaire'];
        $type = "sortie";
        $date_mouvement = $date_commande;
        $query_mvt = $data->prepare("INSERT INTO mouvements (designation, Quantite, Prix_Unitaire, type, date_mouvement)
                                 VALUES (:designation, :quantite, :prix_unitaire, :type, :date_mouvement)");
        $query_mvt->execute([
            ':designation' => $designation,
            ':quantite' => $quantite,
            ':prix_unitaire' => $prix_unitaire,
            ':type' => $type,
            ':date_mouvement' => $date_mouvement
        ]);

        return [
            "Reussite" => "Commande enregistrée avec succès (vérification stock OK)",
            "Dernier_Enregistrement" => self::afficher_dernier_enreg(),
            "Stock_Restant" => $stock_restant
        ];
    } else {
        return ["Message" => "Échec d'enregistrement de la commande"];
    }
}
    public static function afficher_dernier_enreg()
    {
        $data = get_connection();
        $donnees = $data->query("SELECT * FROM commandes ORDER BY id DESC LIMIT 1")->fetchAll();
        if (count($donnees) > 0) {
            return $donnees;
        }
    }

    public static function select_all()
    {
        $data = get_connection();
        $donnees = $data->query("
            SELECT c.*, p.designation as produit, a.prix_unitaire, 
                   CONCAT(cl.nom, ' ', cl.prenom) as client,
                   CONCAT(u.nom, ' ', u.prenom) as comptable
            FROM commandes c
            JOIN approvisionnement a ON c.Id_appro = a.id_approvisionnement
            JOIN produit p ON a.id_produit = p.id
            JOIN clients cl ON c.id_client = cl.id
            JOIN utilisateurs u ON c.id_User = u.id
            ORDER BY c.date_commande DESC
        ")->fetchAll();
        
        if (count($donnees) > 0) {
            return $donnees;
        } else {
            $response["Message"] = "Aucune commande disponible";
            return $response;
        }
    }

    public static function update($id, $id_client, $id_appro, $id_comptable, $quantite)
{
    $data = get_connection();
    
    // Récupérer l'ancienne quantité et l'approvisionnement
    $old_cmd = $data->query("SELECT quantite, Id_appro FROM commandes WHERE id = $id")->fetch();
    
    // Vérifier le stock disponible dans l'approvisionnement
    $stock_appro = $data->query("SELECT quantite FROM approvisionnement WHERE id_approvisionnement = {$old_cmd['Id_appro']}")->fetchColumn();
    
    // Calcul de la différence
    $diff = $quantite - $old_cmd['quantite'];
    $new_stock = $stock_appro - $diff;
    
    // Vérifier si le nouveau stock est suffisant
    if ($new_stock < 50) {
        return ["Message" => "Stock insuffisant - Le stock après modification serait de $new_stock (minimum 50 requis)"];
    }

    $query = $data->prepare("UPDATE commandes 
                            SET id_client = :id_client, 
                                Id_appro = :Id_appro,
                                id_User = :id_User,
                                quantite = :quantite
                            WHERE id = :id");

    if ($query->execute([
        ':id' => $id,
        ':id_client' => $id_client,
        ':Id_appro' => $id_appro,
        ':id_User' => $id_comptable,
        ':quantite' => $quantite
    ])) {
        // Supprimé la mise à jour du stock dans l'approvisionnement
        // $data->query("UPDATE approvisionnement SET quantite = quantite - $diff 
        //               WHERE id_approvisionnement = {$old_cmd['Id_appro']}");
        
        $response["message"] = "Commande modifiée avec succès";
        return $response;
    } else {
        $response["Message"] = "Échec de modification de la commande";
        return $response;
    }
}

    public static function select_one($id)
    {
        $data = get_connection();
        $donnees = $data->query("
            SELECT c.*, p.designation as produit, a.prix_unitaire, 
                   CONCAT(cl.nom, ' ', cl.prenom) as client,
                   CONCAT(u.nom, ' ', u.prenom) as comptable
            FROM commandes c
            JOIN approvisionnement a ON c.Id_appro = a.id_approvisionnement
            JOIN produits p ON a.id_produit = p.id
            JOIN clients cl ON c.id_client = cl.id
            JOIN utilisateurs u ON c.id_User = u.id
            WHERE c.id = '$id'
        ")->fetchAll();
        
        if (count($donnees) > 0) {
            return $donnees;
        } else {
            $response["message"] = "Aucune commande trouvée avec cet ID";
            return $response;
        }
    }

    public static function compterCommandes()
    {
        $data = get_connection();
        $donnees = $data->query("SELECT COUNT(id) as total FROM commandes")->fetchAll();
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
        
        // Récupérer la commande avant suppression
        $cmd = $data->query("SELECT quantite, Id_appro FROM commandes WHERE id = $id")->fetch();
        
        if ($data->query("DELETE FROM commandes WHERE id = '$id'")) {
            // Restaurer le stock dans l'approvisionnement
            $data->query("UPDATE approvisionnement SET quantite = quantite + {$cmd['quantite']} 
                         WHERE id_approvisionnement = {$cmd['Id_appro']}");
            
            $response["Reussite"] = "Commande supprimée avec succès";
            return $response;
        } else {
            $response["Message"] = "Échec de suppression de la commande";
            return $response;
        }
    }

    public static function commandes_par_client($id_client)
    {
        $data = get_connection();
        $donnees = $data->query("
            SELECT c.*, p.designation as produit, a.prix_unitaire, 
                   CONCAT(u.nom, ' ', u.prenom) as comptable
            FROM commandes c
            JOIN approvisionnement a ON c.Id_appro = a.id_approvisionnement
            JOIN produits p ON a.id_produit = p.id
            JOIN utilisateurs u ON c.id_User = u.id
            WHERE c.id_client = '$id_client' 
            ORDER BY c.date_commande DESC
        ")->fetchAll();
        
        if (count($donnees) > 0) {
            return $donnees;
        } else {
            $response["message"] = "Aucune commande trouvée pour ce client";
            return $response;
        }
    }

    public static function commandes_par_produit($id_produit)
    {
        $data = get_connection();
        $donnees = $data->query("
            SELECT c.*, p.designation as produit, a.prix_unitaire, 
                   CONCAT(cl.nom, ' ', cl.prenom) as client,
                   CONCAT(u.nom, ' ', u.prenom) as comptable
            FROM commandes c
            JOIN approvisionnement a ON c.Id_appro = a.id_approvisionnement
            JOIN produits p ON a.id_produit = p.id
            JOIN clients cl ON c.id_client = cl.id
            JOIN utilisateurs u ON c.id_User = u.id
            WHERE a.id_produit = '$id_produit' 
            ORDER BY c.date_commande DESC
        ")->fetchAll();
        
        if (count($donnees) > 0) {
            return $donnees;
        } else {
            $response["message"] = "Aucune commande trouvée pour ce produit";
            return $response;
        }
    }

    public static function fiche_journaliere_vente()
    {
        $pdo = get_connection();

        $sql = "
            SELECT 
                DATE(c.date_commande) AS date_vente,
                p.designation AS produit,
                c.quantite,
                a.prix_unitaire AS prix_usd,
                (c.quantite * a.prix_unitaire) AS total_usd,
                (c.quantite * a.prix_unitaire * 3000) AS total_fc,
                CONCAT(cl.prenom, ' ', cl.nom) AS client,
                CONCAT(u.prenom, ' ', u.nom) AS vendeur
            FROM 
                commandes c
            JOIN 
                approvisionnement a ON c.Id_appro = a.id_approvisionnement
            JOIN
                produits p ON a.id_produit = p.id
            LEFT JOIN 
                clients cl ON c.id_client = cl.id
            LEFT JOIN 
                utilisateurs u ON c.id_User = u.id
            WHERE 
                DATE(c.date_commande) = CURDATE()
            ORDER BY 
                c.date_commande DESC
        ";

        $stmt = $pdo->query($sql);
        $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $resultats ?: ["Message" => "Aucune vente enregistrée pour aujourd’hui"];
    }
}