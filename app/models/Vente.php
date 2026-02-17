<?php

namespace app\models;

use app\config\Db;

class Vente
{
    public static function getById(int $id): ?array
    {
        $db = Db::getInstance();

        $row = $db->fetchRow(
            "SELECT * FROM ventes WHERE id = ?",
            [$id]
        );

        return $row ? $row->getData() : null;
    }

    public static function getAllWithDetails(): array
    {
        $db = Db::getInstance();

        $rows = $db->fetchAll(
            "SELECT v.*, 
                    d.quantite AS don_quantite_originale,
                    bt.name AS besoin_nom,
                    bt.type AS besoin_type
             FROM ventes v
             JOIN dons d ON v.don_id = d.id
             JOIN besoins_types bt ON v.besoin_type_id = bt.id
             ORDER BY v.date_vente DESC"
        );

        return array_map(fn($row) => $row->getData(), $rows);
    }

    public static function insert(array $data): void
    {
        $db = Db::getInstance();

        $db->runQuery(
            "INSERT INTO ventes (
                don_id, besoin_type_id, quantite, 
                prix_achat_unitaire, prix_vente_unitaire, 
                montant_total_vente, pourcentage_remise
             ) VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                $data['don_id'],
                $data['besoin_type_id'],
                $data['quantite'],
                $data['prix_achat_unitaire'],
                $data['prix_vente_unitaire'],
                $data['montant_total_vente'],
                $data['pourcentage_remise'] ?? 10.00
            ]
        );
    }

    public static function getTotalVentes(): float
    {
        $db = Db::getInstance();

        $row = $db->fetchRow(
            "SELECT COALESCE(SUM(montant_total_vente), 0) AS total FROM ventes"
        );

        return $row ? (float)$row->getData()['total'] : 0;
    }

    public static function getStatsVentes(): array
    {
        $db = Db::getInstance();

        $rows = $db->fetchAll(
            "SELECT 
                bt.name AS besoin_nom,
                COUNT(v.id) AS nombre_ventes,
                SUM(v.quantite) AS quantite_totale,
                SUM(v.montant_total_vente) AS montant_total
             FROM ventes v
             JOIN besoins_types bt ON v.besoin_type_id = bt.id
             GROUP BY v.besoin_type_id
             ORDER BY bt.name"
        );

        return array_map(fn($row) => $row->getData(), $rows);
    }
}