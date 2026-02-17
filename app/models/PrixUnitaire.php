<?php

namespace app\models;

use app\config\Db;

class PrixUnitaire
{
    public static function getById(int $id): ?array
    {
        $db = Db::getInstance();

        $row = $db->fetchRow(
            "SELECT * FROM prix_unitaires WHERE id = ?",
            [$id]
        );

        return $row ? $row->getData() : null;
    }

    public static function getByBesoinTypeId(int $besoin_type_id): ?array
    {
        $db = Db::getInstance();

        $row = $db->fetchRow(
            "SELECT * FROM prix_unitaires WHERE besoin_type_id = ?",
            [$besoin_type_id]
        );

        return $row ? $row->getData() : null;
    }

    public static function getAllWithDetails(): array
    {
        $db = Db::getInstance();

        $rows = $db->fetchAll(
            "SELECT p.*, bt.name AS besoin_nom, bt.type AS besoin_type
             FROM prix_unitaires p
             JOIN besoins_types bt ON p.besoin_type_id = bt.id
             ORDER BY bt.type, bt.name"
        );

        return array_map(fn($row) => $row->getData(), $rows);
    }

    public static function insert(array $data): void
    {
        $db = Db::getInstance();

        $db->runQuery(
            "INSERT INTO prix_unitaires (besoin_type_id, prix_unitaire)
             VALUES (?, ?)",
            [$data['besoin_type_id'], $data['prix_unitaire']]
        );
    }

    public static function update(int $id, array $data): void
    {
        $db = Db::getInstance();

        $db->runQuery(
            "UPDATE prix_unitaires SET 
                prix_unitaire = ?,
                date_mise_a_jour = NOW()
             WHERE id = ?",
            [$data['prix_unitaire'], $id]
        );
    }

    public static function updateByBesoinTypeId(int $besoin_type_id, float $prix_unitaire): void
    {
        $db = Db::getInstance();

        
        $existant = self::getByBesoinTypeId($besoin_type_id);
        
        if ($existant) {
            
            $db->runQuery(
                "UPDATE prix_unitaires SET 
                    prix_unitaire = ?,
                    date_mise_a_jour = NOW()
                 WHERE besoin_type_id = ?",
                [$prix_unitaire, $besoin_type_id]
            );
        } else {
            
            $db->runQuery(
                "INSERT INTO prix_unitaires (besoin_type_id, prix_unitaire)
                 VALUES (?, ?)",
                [$besoin_type_id, $prix_unitaire]
            );
        }
    }

    public static function delete(int $id): void
    {
        $db = Db::getInstance();

        $db->runQuery(
            "DELETE FROM prix_unitaires WHERE id = ?",
            [$id]
        );
    }
}