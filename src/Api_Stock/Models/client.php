<?php
require_once("./Config.php");

class Client
{
    public static function save($Nom, $Prenom, $Sexe, $Adresse, $Telephone, $Email, $Date_Creation)
    {
        $data = get_connection();

        // Vérifier si l'email ou le téléphone existe déjà
        $query = $data->prepare("SELECT id FROM clients WHERE email = :email OR telephone = :telephone");
        $query->execute([
            ':email' => $Email,
            ':telephone' => $Telephone
        ]);
        if ($query->fetch()) {
            return [
                "Message" => "L'email ou le numéro de téléphone existe déjà. Veuillez en choisir un autre."
            ];
        }

        // Insérer le nouveau client
        $query = $data->prepare("INSERT INTO clients (nom, prenom, sexe, adresse, telephone, email, date_creation) 
                                 VALUES (:nom, :prenom, :sexe, :adresse, :telephone, :email, :date_creation)");
        $success = $query->execute([
            ':nom' => $Nom,
            ':prenom' => $Prenom,
            ':sexe' => $Sexe,
            ':adresse' => $Adresse,
            ':telephone' => $Telephone,
            ':email' => $Email,
            ':date_creation' => $Date_Creation
        ]);

        if ($success) {
            return [
                "Reussite" => "Client enregistré",
                "Dernier_Enregistrement" => self::get_last()
            ];
        } else {
            return [
                "Message" => "Echec d'enregistrement"
            ];
        }
    }

    public static function delete($Id_Client)
    {
        $data = get_connection();
        $query = $data->prepare("DELETE FROM clients WHERE id = :id");
        if ($query->execute([':id' => $Id_Client])) {
            return ["Reussite" => "Suppression réussie"];
        } else {
            return ["Message" => "Echec de suppression"];
        }
    }

    public static function update($Id_Client, $Nom, $Prenom, $Sexe, $Adresse, $Telephone, $Email)
    {
        $data = get_connection();
        $query = $data->prepare("UPDATE clients 
            SET nom = :nom, prenom = :prenom, sexe = :sexe, adresse = :adresse, 
                telephone = :telephone, email = :email
            WHERE id = :id");
        $success = $query->execute([
            ':nom' => $Nom,
            ':prenom' => $Prenom,
            ':sexe' => $Sexe,
            ':adresse' => $Adresse,
            ':telephone' => $Telephone,
            ':email' => $Email,
            ':id' => $Id_Client
        ]);
        if ($success) {
            return ["Reussite" => "Modification réussie"];
        } else {
            return ["Message" => "Echec de modification"];
        }
    }

    public static function select_all()
    {
        $data = get_connection();
        $stmt = $data->query("SELECT * FROM clients ORDER BY nom, prenom");
        $donnees = $stmt->fetchAll();
        if ($donnees && count($donnees) > 0) {
            return $donnees;
        } else {
            return ["Message" => "Aucun client disponible"];
        }
    }

    public static function select_one($Id_Client)
    {
        $data = get_connection();
        $query = $data->prepare("SELECT * FROM clients WHERE id = :id");
        $query->execute([':id' => $Id_Client]);
        $donnees = $query->fetchAll();
        if ($donnees && count($donnees) > 0) {
            return $donnees;
        } else {
            return ["Message" => "Aucun client trouvé avec cet ID"];
        }
    }

    public static function get_last()
    {
        $data = get_connection();
        $stmt = $data->query("SELECT * FROM clients ORDER BY id DESC LIMIT 1");
        $donnees = $stmt->fetchAll();
        if ($donnees && count($donnees) > 0) {
            return $donnees;
        } else {
            return [];
        }
    }

    public static function count_clients()
    {
        $data = get_connection();
        $stmt = $data->query("SELECT count(id) as total FROM clients");
        $donnees = $stmt->fetchAll();
        if ($donnees && count($donnees) > 0) {
            return $donnees;
        } else {
            return ["Message" => "Aucune donnée disponible"];
        }
    }

    public static function search($search_term)
    {
        $data = get_connection();
        $query = $data->prepare("SELECT * FROM clients 
                                WHERE nom LIKE :search OR prenom LIKE :search OR email LIKE :search OR telephone LIKE :search");
        $query->execute([':search' => "%$search_term%"]);
        $results = $query->fetchAll();

        if ($results && count($results) > 0) {
            return $results;
        } else {
            return ["Message" => "Aucun client trouvé pour cette recherche"];
        }
    }
}