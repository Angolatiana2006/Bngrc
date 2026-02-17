<?php

namespace app\models;

use app\config\Db;
use Exception;

class Attribution
{
    public static function getById(int $id): ?array
    {
        $db = Db::getInstance();

        $row = $db->fetchRow(
            "SELECT * FROM attributions WHERE id = ?",
            [$id]
        );

        return $row ? $row->getData() : null;
    }

    public static function getAllWithDetails(): array
    {
        $db = Db::getInstance();

        $rows = $db->fetchAll(
            "SELECT a.*, 
                    v.name AS ville_nom,
                    bt.type AS besoin_type,
                    bt.name AS besoin_nom,
                    b.quantite AS besoin_quantite,
                    b.unite,
                    d.quantite AS don_quantite
             FROM attributions a
             JOIN besoins b ON a.besoin_id = b.id
             JOIN dons d ON a.don_id = d.id
             JOIN villes v ON b.ville_id = v.id
             JOIN besoins_types bt ON b.besoin_type_id = bt.id
             ORDER BY a.date_attribution DESC"
        );

        return array_map(fn($row) => $row->getData(), $rows);
    }

    public static function getByVille(int $ville_id): array
    {
        $db = Db::getInstance();

        $rows = $db->fetchAll(
            "SELECT a.*, 
                    bt.name AS besoin_nom,
                    b.unite,
                    d.date_don
             FROM attributions a
             JOIN besoins b ON a.besoin_id = b.id
             JOIN dons d ON a.don_id = d.id
             JOIN besoins_types bt ON b.besoin_type_id = bt.id
             WHERE b.ville_id = ?
             ORDER BY a.date_attribution DESC",
            [$ville_id]
        );

        return array_map(fn($row) => $row->getData(), $rows);
    }

    public static function insert(array $data): void
    {
        $db = Db::getInstance();

        // Vérifier la quantité disponible du don
        $quantite_disponible = Don::getQuantiteDisponible($data['don_id']);
        
        if ($data['quantite'] > $quantite_disponible) {
            throw new \Exception("Erreur : La quantité donnée ({$data['quantite']}) est supérieure à la quantité disponible du don ({$quantite_disponible})");
        }

        // Vérifier la quantité restante du besoin
        $quantite_restante = Besoin::getQuantiteRestante($data['besoin_id']);
        
        if ($data['quantite'] > $quantite_restante) {
            throw new \Exception("Erreur : La quantité attribuée ({$data['quantite']}) dépasse le besoin restant ({$quantite_restante})");
        }

        $db->runQuery(
            "INSERT INTO attributions (besoin_id, don_id, quantite, date_attribution)
            VALUES (?, ?, ?, NOW())",
            [
                $data['besoin_id'],
                $data['don_id'],
                $data['quantite']
            ]
        );
    }

    public static function update(int $id, array $data): void
    {
        $db = Db::getInstance();

        // Récupérer l'attribution actuelle pour ajuster les quantités
        $attribution = self::getById($id);
        
        // Vérifier la quantité disponible du don (en tenant compte de l'ancienne attribution)
        $quantite_disponible = Don::getQuantiteDisponible($data['don_id']) + $attribution['quantite'];
        
        if ($data['quantite'] > $quantite_disponible) {
            throw new Exception("Erreur : La quantité donnée ({$data['quantite']}) est supérieure à la quantité disponible du don ({$quantite_disponible})");
        }

        // Vérifier la quantité restante du besoin (en tenant compte de l'ancienne attribution)
        $quantite_restante = Besoin::getQuantiteRestante($data['besoin_id']) + $attribution['quantite'];
        
        if ($data['quantite'] > $quantite_restante) {
            throw new Exception("Erreur : La quantité attribuée ({$data['quantite']}) dépasse le besoin restant ({$quantite_restante})");
        }

        $db->runQuery(
            "UPDATE attributions SET
                besoin_id = ?,
                don_id = ?,
                quantite = ?
             WHERE id = ?",
            [
                $data['besoin_id'],
                $data['don_id'],
                $data['quantite'],
                $id
            ]
        );
    }

    public static function delete(int $id): void
    {
        $db = Db::getInstance();

        $db->runQuery(
            "DELETE FROM attributions WHERE id = ?",
            [$id]
        );
    }

    public static function deleteAll(): void
    {
        $db = Db::getInstance();
        $db->exec("DELETE FROM attributions");
    }
}