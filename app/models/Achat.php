<?php

namespace app\models;

use app\config\Db;

class Achat
{
    public static function getById(int $id): ?array
    {
        $db = Db::getInstance();

        $row = $db->fetchRow(
            "SELECT * FROM achats WHERE id = ?",
            [$id]
        );

        return $row ? $row->getData() : null;
    }

    public static function getAllWithDetails(): array
    {
        $db = Db::getInstance();

        $rows = $db->fetchAll(
            "SELECT a.*, 
                    d.quantite AS don_quantite,
                    bt.name AS besoin_nom,
                    bt.type AS besoin_type,
                    v.name AS ville_nom
             FROM achats a
             JOIN dons d ON a.don_id = d.id
             JOIN besoins_types bt ON a.besoin_type_id = bt.id
             LEFT JOIN villes v ON a.ville_id = v.id
             ORDER BY a.date_achat DESC"
        );

        return array_map(fn($row) => $row->getData(), $rows);
    }

    public static function getByVille(int $ville_id): array
    {
        $db = Db::getInstance();

        $rows = $db->fetchAll(
            "SELECT a.*, 
                    bt.name AS besoin_nom,
                    bt.type AS besoin_type
             FROM achats a
             JOIN besoins_types bt ON a.besoin_type_id = bt.id
             WHERE a.ville_id = ?
             ORDER BY a.date_achat DESC",
            [$ville_id]
        );

        return array_map(fn($row) => $row->getData(), $rows);
    }

    public static function insert(array $data): void
    {
        $db = Db::getInstance();

        // Vérifier que le don utilisé est bien de l'argent
        $don = Don::getById($data['don_id']);
        if (!$don) {
            throw new \Exception("Don non trouvé");
        }

        // Vérifier que le don est de type argent
        $besoinType = BesoinType::getById($don['besoin_type_id']);
        if ($besoinType['type'] !== 'argent') {
            throw new \Exception("Seuls les dons en argent peuvent être utilisés pour acheter");
        }

        // Vérifier le montant disponible du don
        $montantDisponible = Don::getQuantiteDisponible($data['don_id']);
        if ($data['montant_total'] > $montantDisponible) {
            throw new \Exception("Montant insuffisant sur ce don");
        }

        $db->runQuery(
            "INSERT INTO achats (
                don_id, besoin_type_id, quantite, 
                prix_unitaire, montant_total, ville_id,
                date_achat, besoin_id
             ) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)",
            [
                $data['don_id'],
                $data['besoin_type_id'],
                $data['quantite'],
                $data['prix_unitaire'],
                $data['montant_total'],
                $data['ville_id'] ?? null,
                $data['besoin_id'] ?? null
            ]
        );
    }

    public static function getPrixUnitaire(int $besoin_type_id): ?float
    {
        $db = Db::getInstance();
        
        $row = $db->fetchRow(
            "SELECT prix_unitaire FROM prix_unitaires WHERE besoin_type_id = ?",
            [$besoin_type_id]
        );
        
        return $row ? (float)$row->getData()['prix_unitaire'] : null;
    }

    public static function getTotalAchatsParVille(): array
    {
        $db = Db::getInstance();

        $rows = $db->fetchAll(
            "SELECT 
                v.id AS ville_id,
                v.name AS ville_nom,
                COUNT(a.id) AS nombre_achats,
                COALESCE(SUM(a.montant_total), 0) AS total_achete,
                COALESCE(SUM(a.quantite), 0) AS quantite_totale_achetee
             FROM villes v
             LEFT JOIN achats a ON v.id = a.ville_id
             GROUP BY v.id
             ORDER BY v.name"
        );

        return array_map(fn($row) => $row->getData(), $rows);
    }

    public static function getStatsGlobales(): array
    {
        $db = Db::getInstance();

        // Total des besoins en montant
        $besoinsMontant = $db->fetchRow(
            "SELECT 
                COALESCE(SUM(b.quantite * COALESCE(p.prix_unitaire, 0)), 0) AS total_besoins_montant
             FROM besoins b
             LEFT JOIN prix_unitaires p ON b.besoin_type_id = p.besoin_type_id"
        );
        $totalBesoins = $besoinsMontant ? (float)$besoinsMontant->getData()['total_besoins_montant'] : 0;

        // Total des besoins satisfaits
        $satisfaitMontant = $db->fetchRow(
            "SELECT 
                COALESCE(SUM(a.quantite * COALESCE(p.prix_unitaire, 0)), 0) AS total_satisfait_montant
             FROM attributions a
             JOIN besoins b ON a.besoin_id = b.id
             LEFT JOIN prix_unitaires p ON b.besoin_type_id = p.besoin_type_id"
        );
        $totalSatisfait = $satisfaitMontant ? (float)$satisfaitMontant->getData()['total_satisfait_montant'] : 0;

        // Dons reçus en argent
        $donsArgent = $db->fetchRow(
            "SELECT 
                COALESCE(SUM(d.quantite), 0) AS total_dons_argent
             FROM dons d
             JOIN besoins_types bt ON d.besoin_type_id = bt.id
             WHERE bt.type = 'argent'"
        );
        $totalDonsArgent = $donsArgent ? (float)$donsArgent->getData()['total_dons_argent'] : 0;

        // Dons argent utilisés pour achats
        $donsArgentUtilises = $db->fetchRow(
            "SELECT 
                COALESCE(SUM(a.montant_total), 0) AS total_argent_utilise
             FROM achats a"
        );
        $totalArgentUtilise = $donsArgentUtilises ? (float)$donsArgentUtilises->getData()['total_argent_utilise'] : 0;

        // Dons en nature reçus (valeur)
        $donsNature = $db->fetchRow(
            "SELECT 
                COALESCE(SUM(d.quantite * COALESCE(p.prix_unitaire, 0)), 0) AS total_dons_nature_valeur
             FROM dons d
             JOIN besoins_types bt ON d.besoin_type_id = bt.id
             LEFT JOIN prix_unitaires p ON d.besoin_type_id = p.besoin_type_id
             WHERE bt.type != 'argent'"
        );
        $totalDonsNatureValeur = $donsNature ? (float)$donsNature->getData()['total_dons_nature_valeur'] : 0;

        // Dons nature dispatchés (valeur)
        $donsNatureUtilises = $db->fetchRow(
            "SELECT 
                COALESCE(SUM(at.quantite * COALESCE(p.prix_unitaire, 0)), 0) AS total_nature_utilise
             FROM attributions at
             JOIN besoins b ON at.besoin_id = b.id
             LEFT JOIN prix_unitaires p ON b.besoin_type_id = p.besoin_type_id"
        );
        $totalNatureUtilise = $donsNatureUtilises ? (float)$donsNatureUtilises->getData()['total_nature_utilise'] : 0;

        return [
            'total_besoins_montant' => $totalBesoins,
            'total_satisfait_montant' => $totalSatisfait,
            'pourcentage_satisfait' => $totalBesoins > 0 ? round(($totalSatisfait / $totalBesoins) * 100, 2) : 0,
            'total_dons_argent' => $totalDonsArgent,
            'total_argent_utilise' => $totalArgentUtilise,
            'total_argent_restant' => $totalDonsArgent - $totalArgentUtilise,
            'total_dons_nature_valeur' => $totalDonsNatureValeur,
            'total_nature_utilise' => $totalNatureUtilise,
            'total_nature_restant_valeur' => $totalDonsNatureValeur - $totalNatureUtilise
        ];
    }
}