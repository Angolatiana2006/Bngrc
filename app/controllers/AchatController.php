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
    
    public function index()
    {
        $achats = Achat::getAllWithDetails();
        $villes = Ville::getAll();
        
        Flight::render('dashboard/achats-liste', [
            'achats' => $achats,
            'villes' => $villes
        ]);
    }

    
    public function showCreateForm()
    {
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        
        $donsDisponibles = Don::getDisponibles();
        $donsArgent = array_filter($donsDisponibles, function($don) {
            return $don['type'] === 'argent';
        });
        
        
        $prixUnitaires = PrixUnitaire::getAllWithDetails();
        
        
        $villes = Ville::getAll();
        
        
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

    
    public function create()
    {
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        
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

        
        $donsDisponibles = Don::getDisponibles();
        $donsArgent = array_filter($donsDisponibles, function($don) {
            return $don['type'] === 'argent';
        });

        
        $totalDisponible = 0;
        foreach($donsArgent as $don) {
            $totalDisponible += $don['quantite_disponible'];
        }

        
        if ($montant_total > $totalDisponible) {
            $errors[] = "Montant total ($montant_total Ar) dépasse le total disponible ($totalDisponible Ar)";
        }

        if (!empty($errors)) {
            $_SESSION['achat_errors'] = $errors;
            Flight::redirect('/achats/create?error=1');
            return;
        }

        try {
            
            $montantRestant = $montant_total;
            
            foreach($donsArgent as $don) {
                if ($montantRestant <= 0) break;
                
                $montantUtilise = min($don['quantite_disponible'], $montantRestant);
                $quantiteAchetee = ($montantUtilise / $prix_unitaire);
                
                
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
                
                
                $donNatureExistant = Don::getByBesoinTypeId($besoin_type_id);
                
                if ($donNatureExistant) {
                    
                    $nouvelleQuantite = $donNatureExistant['quantite'] + $quantiteAchetee;
                    Don::update($donNatureExistant['id'], ['quantite' => $nouvelleQuantite]);
                } else {
                    
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

    
    public function recap()
    {
        $stats = Achat::getStatsGlobales();
        $achatsParVille = Achat::getTotalAchatsParVille();
        
        Flight::render('dashboard/recap', [
            'stats' => $stats,
            'achatsParVille' => $achatsParVille
        ]);
    }

    
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