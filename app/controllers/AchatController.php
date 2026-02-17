<?php

namespace app\controllers;

use Flight;
use app\models\Achat;
use app\models\Don;
use app\models\Ville;
use app\models\Besoin;
use app\models\BesoinType;
use app\models\PrixUnitaire;

class AchatController
{
    /**
     * Affiche la liste des achats
     */
    public function index()
    {
        $achats = Achat::getAllWithDetails();
        $villes = Ville::getAll();
        
        Flight::render('dashboard/achats-liste', [
            'achats' => $achats,
            'villes' => $villes
        ]);
    }

    /**
     * Affiche le formulaire d'achat
     */
    public function showCreateForm()
    {
        // Démarrer la session
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Récupérer les dons en argent disponibles
        $donsDisponibles = Don::getDisponibles();
        $donsArgent = array_filter($donsDisponibles, function($don) {
            return $don['type'] === 'argent';
        });
        
        // Récupérer les types de besoins qui ont des prix unitaires
        $prixUnitaires = PrixUnitaire::getAllWithDetails();
        
        // Récupérer les villes
        $villes = Ville::getAll();
        
        // Récupérer les besoins non satisfaits (optionnel)
        $besoins = [];
        $db = \app\config\Db::getInstance();
        $rows = $db->fetchAll(
            "SELECT b.id, v.name AS ville_nom, bt.name AS besoin_nom,
                    b.quantite - COALESCE(SUM(a.quantite), 0) AS reste
             FROM besoins b
             JOIN villes v ON b.ville_id = v.id
             JOIN besoins_types bt ON b.besoin_type_id = bt.id
             LEFT JOIN attributions a ON b.id = a.besoin_id
             GROUP BY b.id
             HAVING reste > 0"
        );
        $besoins = array_map(fn($row) => $row->getData(), $rows);
        
        Flight::render('dashboard/achat-form', [
            'donsArgent' => $donsArgent,
            'prixUnitaires' => $prixUnitaires,
            'villes' => $villes,
            'besoins' => $besoins
        ]);
    }

    /**
     * Traite la création d'un achat
     */
    public function create()
    {
        // Démarrer la session
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Récupérer les données
        $besoin_type_id = $_POST['besoin_type_id'] ?? null;
        $quantite = $_POST['quantite'] ?? null;
        $montant_total = $_POST['montant_total'] ?? 0;
        $prix_unitaire = $_POST['prix_unitaire'] ?? 0;
        $ville_id = $_POST['ville_id'] ?? null;

        $errors = [];

        if (!$besoin_type_id) {
            $errors[] = "Veuillez sélectionner un article à acheter";
        }

        if (!$quantite || $quantite <= 0) {
            $errors[] = "Veuillez saisir une quantité valide";
        }

        if (!$ville_id) {
            $errors[] = "Veuillez sélectionner la ville destinataire";
        }

        // Récupérer tous les dons en argent disponibles
        $donsDisponibles = Don::getDisponibles();
        $donsArgent = array_filter($donsDisponibles, function($don) {
            return $don['type'] === 'argent';
        });

        // Calculer le total disponible
        $totalDisponible = 0;
        foreach($donsArgent as $don) {
            $totalDisponible += $don['quantite_disponible'];
        }

        // Vérifier si on a assez d'argent
        if ($montant_total > $totalDisponible) {
            $errors[] = "Montant total ($montant_total Ar) dépasse le total disponible ($totalDisponible Ar)";
        }

        if (!empty($errors)) {
            $_SESSION['achat_errors'] = $errors;
            Flight::redirect('/achats/create?error=1');
            return;
        }

        try {
            // Répartir l'achat sur plusieurs dons si nécessaire
            $montantRestant = $montant_total;
            
            foreach($donsArgent as $don) {
                if ($montantRestant <= 0) break;
                
                $montantUtilise = min($don['quantite_disponible'], $montantRestant);
                $quantiteAchetee = ($montantUtilise / $prix_unitaire);
                
                // 1. Enregistrer l'achat avec la ville destinataire
                $dataAchat = [
                    'don_id' => $don['id'],
                    'besoin_type_id' => $besoin_type_id,
                    'quantite' => $quantiteAchetee,
                    'prix_unitaire' => $prix_unitaire,
                    'montant_total' => $montantUtilise,
                    'ville_id' => $ville_id,
                    'besoin_id' => null
                ];
                Achat::insert($dataAchat);
                
                // 2. Créer ou mettre à jour le don en nature correspondant
                $donNatureExistant = Don::getByBesoinTypeId($besoin_type_id);
                
                if ($donNatureExistant) {
                    // Mettre à jour le don existant (augmenter la quantité)
                    $nouvelleQuantite = $donNatureExistant['quantite'] + $quantiteAchetee;
                    Don::update($donNatureExistant['id'], ['quantite' => $nouvelleQuantite]);
                } else {
                    // Créer un nouveau don en nature
                    $dataDon = [
                        'besoin_type_id' => $besoin_type_id,
                        'quantite' => $quantiteAchetee
                    ];
                    Don::insert($dataDon);
                }
                
                $montantRestant -= $montantUtilise;
            }
            
            Flight::redirect('/achats?success=1');
            
        } catch (\Exception $e) {
            $_SESSION['achat_errors'] = [$e->getMessage()];
            Flight::redirect('/achats/create?error=1');
        }
    }

    /**
     * Affiche la page de récapitulation (avec AJAX)
     */
    public function recap()
    {
        $stats = Achat::getStatsGlobales();
        $achatsParVille = Achat::getTotalAchatsParVille();
        
        Flight::render('dashboard/recap', [
            'stats' => $stats,
            'achatsParVille' => $achatsParVille
        ]);
    }

    /**
     * API AJAX pour rafraîchir les stats
     */
    public function recapAjax()
    {
        $stats = Achat::getStatsGlobales();
        $achatsParVille = Achat::getTotalAchatsParVille();
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => $stats,
            'achatsParVille' => $achatsParVille,
            'timestamp' => date('d/m/Y H:i:s')
        ]);
        exit;
    }

    /**
     * Filtre les achats par ville
     */
    public function filterByVille($ville_id)
    {
        if ($ville_id == 'all') {
            $achats = Achat::getAllWithDetails();
        } else {
            $achats = Achat::getByVille($ville_id);
        }
        
        $villes = Ville::getAll();
        
        Flight::render('dashboard/achats-liste', [
            'achats' => $achats,
            'villes' => $villes,
            'ville_filter' => $ville_id
        ]);
    }
}