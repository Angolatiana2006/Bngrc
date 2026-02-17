<?php

namespace app\models;

use app\config\Db;

class Don
{
    public static function getById(int $id): ?array
    {
        $db = Db::getInstance();

        $row = $db->fetchRow(
            "SELECT * FROM dons WHERE id = ?",
            [$id]
        );

        return $row ? $row->getData() : null;
    }

    public static function getAllWithDetails(): array
    {
        $db = Db::getInstance();

        $rows = $db->fetchAll(
            "SELECT d.*, 
                    bt.type, 
                    bt.name AS besoin_nom,
                    (SELECT COALESCE(SUM(quantite), 0) FROM attributions WHERE don_id = d.id) AS quantite_attribuee,
                    (SELECT COALESCE(SUM(montant_total), 0) FROM achats WHERE don_id = d.id) AS montant_utilise_achats
             FROM dons d
             JOIN besoins_types bt ON d.besoin_type_id = bt.id
             ORDER BY d.date_don DESC"
        );

        return array_map(fn($row) => $row->getData(), $rows);
    }

    public static function getDisponibles(): array
    {
        $db = Db::getInstance();

        $rows = $db->fetchAll(
            "SELECT d.*, 
                    bt.type, 
                    bt.name AS besoin_nom,
                    d.quantite - 
                    COALESCE((SELECT SUM(quantite) FROM attributions WHERE don_id = d.id), 0) - 
                    CASE WHEN bt.type = 'argent' 
                         THEN COALESCE((SELECT SUM(montant_total) FROM achats WHERE don_id = d.id), 0) 
                         ELSE 0 
                    END AS quantite_disponible
             FROM dons d
             JOIN besoins_types bt ON d.besoin_type_id = bt.id
             HAVING quantite_disponible > 0
             ORDER BY d.date_don ASC"
        );

        return array_map(fn($row) => $row->getData(), $rows);
    }

    public static function getByBesoinTypeId(int $besoin_type_id): ?array
    {
        $db = Db::getInstance();

        $row = $db->fetchRow(
            "SELECT * FROM dons WHERE besoin_type_id = ?",
            [$besoin_type_id]
        );

        return $row ? $row->getData() : null;
    }

    public static function getDisponiblesByType(int $besoin_type_id): array
    {
        $db = Db::getInstance();

        $rows = $db->fetchAll(
            "SELECT d.*, 
                    bt.type, 
                    bt.name AS besoin_nom,
                    d.quantite - 
                    COALESCE((SELECT SUM(quantite) FROM attributions WHERE don_id = d.id), 0) - 
                    CASE WHEN bt.type = 'argent' 
                         THEN COALESCE((SELECT SUM(montant_total) FROM achats WHERE don_id = d.id), 0) 
                         ELSE 0 
                    END AS quantite_disponible
             FROM dons d
             JOIN besoins_types bt ON d.besoin_type_id = bt.id
             WHERE d.besoin_type_id = ?
             HAVING quantite_disponible > 0
             ORDER BY d.date_don ASC",
            [$besoin_type_id]
        );

        return array_map(fn($row) => $row->getData(), $rows);
    }

    public static function getQuantiteDisponible(int $don_id): float
    {
        $db = Db::getInstance();

        $row = $db->fetchRow(
            "SELECT d.quantite - 
                    COALESCE((SELECT SUM(quantite) FROM attributions WHERE don_id = d.id), 0) - 
                    CASE WHEN bt.type = 'argent' 
                         THEN COALESCE((SELECT SUM(montant_total) FROM achats WHERE don_id = d.id), 0) 
                         ELSE 0 
                    END AS disponible
             FROM dons d
             JOIN besoins_types bt ON d.besoin_type_id = bt.id
             WHERE d.id = ?",
            [$don_id]
        );

        return $row ? (float)$row->getData()['disponible'] : 0;
    }

    public static function insert(array $data): void
    {
        $db = Db::getInstance();

        // Vérifier si un don avec ce besoin_type_id existe déjà
        $donExistant = self::getByBesoinTypeId($data['besoin_type_id']);
        
        if ($donExistant) {
            // Mise à jour de la quantité
            $nouvelleQuantite = $donExistant['quantite'] + $data['quantite'];
            self::update($donExistant['id'], ['quantite' => $nouvelleQuantite]);
        } else {
            // Création d'un nouveau don
            $db->runQuery(
                "INSERT INTO dons (besoin_type_id, quantite, date_don)
                 VALUES (?, ?, NOW())",
                [$data['besoin_type_id'], $data['quantite']]
            );
        }
    }

    public static function update(int $id, array $data): void
    {
        $db = Db::getInstance();

        $fields = [];
        $params = [];
        
        if (isset($data['besoin_type_id'])) {
            $fields[] = "besoin_type_id = ?";
            $params[] = $data['besoin_type_id'];
        }
        
        if (isset($data['quantite'])) {
            $fields[] = "quantite = ?";
            $params[] = $data['quantite'];
        }
        
        if (empty($fields)) {
            return;
        }
        
        $params[] = $id;
        
        $sql = "UPDATE dons SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $db->runQuery($sql, $params);
    }

    public static function delete(int $id): void
    {
        $db = Db::getInstance();

        $db->runQuery(
            "DELETE FROM dons WHERE id = ?",
            [$id]
        );
    }

    public static function deleteAll(): void
    {
        $db = Db::getInstance();
        $db->exec("DELETE FROM dons");
    }
}