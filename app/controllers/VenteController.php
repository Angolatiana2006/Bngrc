<?php

namespace app\controllers;

use Flight;
use app\models\Vente;
use app\models\Don;
use app\models\Besoin;
use app\models\BesoinType;

class VenteController
{
    /**
     * Affiche la liste des ventes
     */
    public function index()
    {
        $ventes = Vente::getAllWithDetails();
        $stats = Vente::getStatsVentes();
        $totalVentes = Vente::getTotalVentes();
        
        Flight::render('dashboard/ventes-liste', [
            'ventes' => $ventes,
            'stats' => $stats,
            'totalVentes' => $totalVentes
        ]);
    }

    /**
 * Affiche le formulaire de vente pour un don spécifique
 */
public function showVenteForm($don_id)
{
    // Récupérer le don
    $don = Don::getById($don_id);
    if (!$don) {
        Flight::halt(404, "Don non trouvé");
    }

    // Récupérer les détails complets du don
    $donsDisponibles = Don::getDisponibles();
    $donDetails = null;
    foreach($donsDisponibles as $d) {
        if ($d['id'] == $don_id) {
            $donDetails = $d;
            break;
        }
    }

    if (!$donDetails) {
        Flight::halt(404, "Don non disponible");
    }

    // Vérifier si ce type de don est demandé par des villes
    $besoinsActifs = $this->checkBesoinsActifs($don['besoin_type_id']);
    
    // Récupérer le prix unitaire depuis les prix unitaires
    $prix_unitaire = $this->getPrixUnitaire($don['besoin_type_id']);
    $prix_vente = $prix_unitaire * 0.9; // 10% de remise
    
    Flight::render('dashboard/vente-form', [
        'don' => $donDetails,
        'besoinsActifs' => $besoinsActifs,
        'prix_achat' => $prix_unitaire,  // Prix d'achat = prix unitaire
        'prix_vente' => $prix_vente,
        'remise' => 10
    ]);
}

    /**
     * Traite la vente d'un don
     */
    public function vendre()
    {
        // Démarrer la session
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $don_id = $_POST['don_id'] ?? null;
        $quantite = $_POST['quantite'] ?? null;
        $besoin_type_id = $_POST['besoin_type_id'] ?? null;

        $errors = [];

        if (!$don_id) {
            $errors[] = "Don non identifié";
        }

        if (!$quantite || $quantite <= 0) {
            $errors[] = "Veuillez saisir une quantité valide";
        }

        // Vérifier la quantité disponible
        $quantiteDisponible = Don::getQuantiteDisponible($don_id);
        if ($quantite > $quantiteDisponible) {
            $errors[] = "Quantité insuffisante. Disponible: $quantiteDisponible";
        }

        // Récupérer les détails du don
        $don = Don::getById($don_id);
        if (!$don) {
            $errors[] = "Don non trouvé";
        }

        // Vérifier si ce type de don est demandé par des villes
        $besoinsActifs = $this->checkBesoinsActifs($don['besoin_type_id']);
        if ($besoinsActifs['existe']) {
            $errors[] = "Impossible de vendre ce don car il est encore demandé par des villes: " . $besoinsActifs['message'];
        }

        if (!empty($errors)) {
            $_SESSION['vente_errors'] = $errors;
            Flight::redirect('/dons?vente_error=1');
            return;
        }

        try {
            // Récupérer le prix unitaire
            $prix_unitaire = $this->getPrixUnitaire($don['besoin_type_id']);
            $prix_vente = $prix_unitaire * 0.9;
            $montant_total = $quantite * $prix_vente;

            // Enregistrer la vente
            $data = [
                'don_id' => $don_id,
                'besoin_type_id' => $don['besoin_type_id'],
                'quantite' => $quantite,
                'prix_achat_unitaire' => $prix_unitaire,
                'prix_vente_unitaire' => $prix_vente,
                'montant_total_vente' => $montant_total,
                'pourcentage_remise' => 10.00
            ];

            Vente::insert($data);

            // Mettre à jour le don (diminuer la quantité)
            $nouvelleQuantite = $don['quantite'] - $quantite;
            Don::update($don_id, ['quantite' => $nouvelleQuantite]);

            Flight::redirect('/ventes?success=1');
            
        } catch (\Exception $e) {
            $_SESSION['vente_errors'] = [$e->getMessage()];
            Flight::redirect('/dons?vente_error=1');
        }
    }

    /**
     * Vérifie si un type de besoin est encore demandé
     */
    private function checkBesoinsActifs($besoin_type_id)
    {
        $db = \app\config\Db::getInstance();

        $rows = $db->fetchAll(
            "SELECT b.id, v.name AS ville_nom, 
                    b.quantite - COALESCE(SUM(a.quantite), 0) AS reste
             FROM besoins b
             JOIN villes v ON b.ville_id = v.id
             LEFT JOIN attributions a ON b.id = a.besoin_id
             WHERE b.besoin_type_id = ?
             GROUP BY b.id
             HAVING reste > 0",
            [$besoin_type_id]
        );

        $besoins = array_map(fn($row) => $row->getData(), $rows);

        if (empty($besoins)) {
            return ['existe' => false, 'message' => ''];
        }

        $villes = [];
        foreach($besoins as $b) {
            $villes[] = $b['ville_nom'] . " (" . $b['reste'] . " unités)";
        }

        return [
            'existe' => true,
            'message' => implode(', ', $villes)
        ];
    }

    /**
     * Récupère le prix unitaire d'un type de besoin
     */
    private function getPrixUnitaire($besoin_type_id)
    {
        $db = \app\config\Db::getInstance();
        
        $row = $db->fetchRow(
            "SELECT prix_unitaire FROM prix_unitaires WHERE besoin_type_id = ?",
            [$besoin_type_id]
        );
        
        return $row ? (float)$row->getData()['prix_unitaire'] : 0;
    }
    
}