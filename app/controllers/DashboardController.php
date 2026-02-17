<?php

namespace app\controllers;

use Flight;
use app\models\Dashboard;
use app\models\Ville;
use app\models\Besoin;
use app\models\Don;
use app\models\Attribution;
use app\models\Achat;

class DashboardController
{
    
    public function index()
{
    
    $besoinsParVille = $this->getBesoinsAvecMontants();
    
   
    $achatsParVille = $this->getAchatsParVille();
    
    
    $donsRecents = Dashboard::getDonsRecents(10);
    
    
    $statsFinancieres = $this->getStatsFinancieres();
    
    Flight::render('dashboard/index', [
        'besoinsParVille' => $besoinsParVille,
        'achatsParVille' => $achatsParVille,  
        'donsRecents' => $donsRecents,
        'stats' => $statsFinancieres
    ]);
}

    
    private function getBesoinsAvecMontants()
    {
        $db = \app\config\Db::getInstance();

        $rows = $db->fetchAll(
            "SELECT 
                b.id,
                v.name AS ville_nom,
                bt.name AS besoin_nom,
                bt.type AS besoin_type,
                b.quantite AS quantite_demandee,
                b.unite,
                COALESCE(p.prix_unitaire, 0) AS prix_unitaire,
                b.quantite * COALESCE(p.prix_unitaire, 0) AS montant_total_besoin,
                COALESCE(attrib.quantite_attribuee, 0) AS quantite_attribuee,
                COALESCE(attrib.quantite_attribuee, 0) * COALESCE(p.prix_unitaire, 0) AS montant_satisfait_dons,
                b.quantite - COALESCE(attrib.quantite_attribuee, 0) AS quantite_restante,
                (b.quantite - COALESCE(attrib.quantite_attribuee, 0)) * COALESCE(p.prix_unitaire, 0) AS montant_restant
             FROM besoins b
             JOIN villes v ON b.ville_id = v.id
             JOIN besoins_types bt ON b.besoin_type_id = bt.id
             LEFT JOIN prix_unitaires p ON b.besoin_type_id = p.besoin_type_id
             LEFT JOIN (
                 SELECT besoin_id, SUM(quantite) AS quantite_attribuee
                 FROM attributions
                 GROUP BY besoin_id
             ) attrib ON b.id = attrib.besoin_id
             ORDER BY v.name, bt.name"
        );

        return array_map(fn($row) => $row->getData(), $rows);
    }

   
private function getAchatsParVille()
{
    $db = \app\config\Db::getInstance();

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

    
    private function getStatsFinancieres()
    {
        $db = \app\config\Db::getInstance();

        
        $besoins = $db->fetchRow(
            "SELECT 
                COALESCE(SUM(b.quantite * COALESCE(p.prix_unitaire, 0)), 0) AS total_besoins
             FROM besoins b
             LEFT JOIN prix_unitaires p ON b.besoin_type_id = p.besoin_type_id"
        );
        $totalBesoins = $besoins ? (float)$besoins->getData()['total_besoins'] : 0;

        
        $satisfaitDons = $db->fetchRow(
            "SELECT 
                COALESCE(SUM(at.quantite * COALESCE(p.prix_unitaire, 0)), 0) AS total_satisfait_dons
             FROM attributions at
             JOIN besoins b ON at.besoin_id = b.id
             LEFT JOIN prix_unitaires p ON b.besoin_type_id = p.besoin_type_id"
        );
        $totalSatisfaitDons = $satisfaitDons ? (float)$satisfaitDons->getData()['total_satisfait_dons'] : 0;

        
        $achats = $db->fetchRow(
            "SELECT COALESCE(SUM(montant_total), 0) AS total_achats FROM achats"
        );
        $totalAchats = $achats ? (float)$achats->getData()['total_achats'] : 0;

        
        $totalSatisfait = $totalSatisfaitDons + $totalAchats;

        
        $donsArgent = $db->fetchRow(
            "SELECT 
                COALESCE(SUM(d.quantite), 0) AS total_dons_argent
             FROM dons d
             JOIN besoins_types bt ON d.besoin_type_id = bt.id
             WHERE bt.type = 'argent'"
        );
        $totalDonsArgent = $donsArgent ? (float)$donsArgent->getData()['total_dons_argent'] : 0;

        
        $argentUtilise = $db->fetchRow(
            "SELECT COALESCE(SUM(montant_total), 0) AS total_argent_utilise FROM achats"
        );
        $totalArgentUtilise = $argentUtilise ? (float)$argentUtilise->getData()['total_argent_utilise'] : 0;

        
        $donsNature = $db->fetchRow(
            "SELECT 
                COALESCE(SUM(d.quantite * COALESCE(p.prix_unitaire, 0)), 0) AS total_dons_nature
             FROM dons d
             JOIN besoins_types bt ON d.besoin_type_id = bt.id
             LEFT JOIN prix_unitaires p ON d.besoin_type_id = p.besoin_type_id
             WHERE bt.type != 'argent'"
        );
        $totalDonsNature = $donsNature ? (float)$donsNature->getData()['total_dons_nature'] : 0;

        
        $natureUtilisee = $db->fetchRow(
            "SELECT 
                COALESCE(SUM(at.quantite * COALESCE(p.prix_unitaire, 0)), 0) AS total_nature_utilisee
             FROM attributions at
             JOIN besoins b ON at.besoin_id = b.id
             LEFT JOIN prix_unitaires p ON b.besoin_type_id = p.besoin_type_id"
        );
        $totalNatureUtilisee = $natureUtilisee ? (float)$natureUtilisee->getData()['total_nature_utilisee'] : 0;

        return [
            'total_besoins' => $totalBesoins,
            'total_satisfait_dons' => $totalSatisfaitDons,
            'total_achats' => $totalAchats,
            'total_satisfait' => $totalSatisfait,
            'pourcentage_satisfait' => $totalBesoins > 0 ? round(($totalSatisfait / $totalBesoins) * 100, 2) : 0,
            'total_dons_argent' => $totalDonsArgent,
            'total_argent_utilise' => $totalArgentUtilise,
            'total_argent_restant' => $totalDonsArgent - $totalArgentUtilise,
            'total_dons_nature' => $totalDonsNature,
            'total_nature_utilisee' => $totalNatureUtilisee,
            'total_nature_restante' => $totalDonsNature - $totalNatureUtilisee
        ];
    }

    
}