<?php

namespace app\models;

use app\config\Db;

class Besoin
{
    public static function getById(int $id): ?array
    {
        $db = Db::getInstance();

        $row = $db->fetchRow(
            "SELECT * FROM besoins WHERE id = ?",
            [$id]
        );

        return $row ? $row->getData() : null;
    }

    public static function getAllWithDetails(): array
    {
        $db = Db::getInstance();

        $rows = $db->fetchAll(
            "SELECT b.*, v.name AS ville_nom, bt.type AS besoin_type, bt.name AS besoin_nom,
                    COALESCE(SUM(a.quantite), 0) AS quantite_attribuee
             FROM besoins b
             JOIN villes v ON b.ville_id = v.id
             JOIN besoins_types bt ON b.besoin_type_id = bt.id
             LEFT JOIN attributions a ON b.id = a.besoin_id
             GROUP BY b.id
             ORDER BY v.name, bt.name"
        );

        return array_map(fn($row) => $row->getData(), $rows);
    }

    public static function getByVille(int $ville_id): array
    {
        $db = Db::getInstance();

        $rows = $db->fetchAll(
            "SELECT b.*, bt.type, bt.name AS besoin_nom,
                    COALESCE(SUM(a.quantite), 0) AS quantite_attribuee
             FROM besoins b
             JOIN besoins_types bt ON b.besoin_type_id = bt.id
             LEFT JOIN attributions a ON b.id = a.besoin_id
             WHERE b.ville_id = ?
             GROUP BY b.id
             ORDER BY bt.name",
            [$ville_id]
        );

        return array_map(fn($row) => $row->getData(), $rows);
    }

    public static function getQuantiteRestante(int $besoin_id): float
    {
        $db = Db::getInstance();

        $row = $db->fetchRow(
            "SELECT b.quantite - COALESCE(SUM(a.quantite), 0) AS reste
             FROM besoins b
             LEFT JOIN attributions a ON b.id = a.besoin_id
             WHERE b.id = ?
             GROUP BY b.id",
            [$besoin_id]
        );

        return $row ? (float)$row->getData()['reste'] : 0;
    }

    public static function insert(array $data): void
    {
        $db = Db::getInstance();

        $db->runQuery(
            "INSERT INTO besoins (ville_id, besoin_type_id, quantite, unite)
             VALUES (?, ?, ?, ?)",
            [
                $data['ville_id'],
                $data['besoin_type_id'],
                $data['quantite'],
                $data['unite'] ?? null
            ]
        );
    }

    public static function update(int $id, array $data): void
    {
        $db = Db::getInstance();

        $db->runQuery(
            "UPDATE besoins SET
                ville_id = ?,
                besoin_type_id = ?,
                quantite = ?,
                unite = ?
             WHERE id = ?",
            [
                $data['ville_id'],
                $data['besoin_type_id'],
                $data['quantite'],
                $data['unite'] ?? null,
                $id
            ]
        );
    }

    public static function delete(int $id): void
    {
        $db = Db::getInstance();

        $db->runQuery(
            "DELETE FROM besoins WHERE id = ?",
            [$id]
        );
    }

    public static function deleteAll(): void
    {
        $db = Db::getInstance();
        $db->exec("DELETE FROM besoins");
    }
}