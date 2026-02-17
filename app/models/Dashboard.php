<?php

namespace app\models;

use app\config\Db;

class Dashboard
{
    public static function getStatsVilles(): array
    {
        $db = Db::getInstance();

        $rows = $db->fetchAll(
            "SELECT 
                v.id,
                v.name AS ville_nom,
                COUNT(DISTINCT b.id) AS nombre_besoins,
                COALESCE(SUM(b.quantite), 0) AS total_besoins_quantite,
                COALESCE(SUM(a.quantite), 0) AS total_dons_recus,
                CASE 
                    WHEN COALESCE(SUM(b.quantite), 0) > 0 
                    THEN (COALESCE(SUM(a.quantite), 0) / COALESCE(SUM(b.quantite), 1)) * 100 
                    ELSE 0 
                END AS pourcentage_realise
             FROM villes v
             LEFT JOIN besoins b ON v.id = b.ville_id
             LEFT JOIN attributions a ON b.id = a.besoin_id
             GROUP BY v.id
             ORDER BY v.name"
        );

        return array_map(fn($row) => $row->getData(), $rows);
    }

    public static function getBesoinsParVille(): array
{
    $db = Db::getInstance();

    $rows = $db->fetchAll(
        "SELECT 
            b.id,
            v.name AS ville_nom,
            bt.name AS besoin_nom,
            bt.type AS besoin_type,
            b.quantite AS besoin_quantite,
            b.unite,
            b.date_creation,
            COALESCE(SUM(a.quantite), 0) AS quantite_attribuee,
            b.quantite - COALESCE(SUM(a.quantite), 0) AS reste
         FROM villes v
         JOIN besoins b ON v.id = b.ville_id
         JOIN besoins_types bt ON b.besoin_type_id = bt.id
         LEFT JOIN attributions a ON b.id = a.besoin_id
         GROUP BY b.id
         ORDER BY b.date_creation DESC, v.name, bt.name"
    );

    return array_map(fn($row) => $row->getData(), $rows);
}

    public static function getDonsRecents(int $limite = 10): array
    {
        $db = Db::getInstance();

        $rows = $db->fetchAll(
            "SELECT 
                d.id,
                bt.name AS besoin_nom,
                bt.type AS besoin_type,
                d.quantite,
                d.date_don,
                COALESCE(SUM(a.quantite), 0) AS quantite_attribuee
             FROM dons d
             JOIN besoins_types bt ON d.besoin_type_id = bt.id
             LEFT JOIN attributions a ON d.id = a.don_id
             GROUP BY d.id
             ORDER BY d.date_don DESC
             LIMIT ?",
            [$limite]
        );

        return array_map(fn($row) => $row->getData(), $rows);
    }
}