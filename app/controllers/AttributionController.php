<?php

namespace app\controllers;

use Flight;
use app\models\Attribution;
use app\models\Besoin;
use app\models\Don;
use app\models\Ville;
use app\models\BesoinType;
use app\models\Dashboard;

class AttributionController
{
    /**
     * Affiche la page d'attribution des dons
     */
    public function index()
    {
        // Récupérer tous les besoins avec leurs détails
        $besoins = Dashboard::getBesoinsParVille();
        
        // Récupérer tous les dons disponibles (stock)
        $donsDisponibles = Don::getDisponibles();
        
        // Récupérer l'historique des attributions
        $historique = Attribution::getAllWithDetails();
        
        Flight::render('dashboard/attribution-don', [
            'besoins' => $besoins,
            'donsDisponibles' => $donsDisponibles,
            'historique' => $historique
        ]);
    }

    /**
     * Affiche le formulaire d'attribution pour un besoin spécifique
     */
    public function showAttributionForm($besoin_id)
    {
        // Récupérer les détails du besoin
        $besoin = Besoin::getById($besoin_id);
        if (!$besoin) {
            Flight::halt(404, "Besoin non trouvé");
        }
        
        // Récupérer les détails complets du besoin avec la ville et le type
        $besoinDetails = $this->getBesoinDetails($besoin_id);
        
        // Calculer la quantité restante pour ce besoin
        $quantiteRestante = Besoin::getQuantiteRestante($besoin_id);
        
        // Récupérer les dons disponibles pour ce type de besoin
        $donsDisponibles = Don::getDisponiblesByType($besoin['besoin_type_id']);
        
        Flight::render('dashboard/attribution-form', [
            'besoin' => $besoinDetails,
            'quantiteRestante' => $quantiteRestante,
            'donsDisponibles' => $donsDisponibles
        ]);
    }

    /**
     * Traite l'attribution d'un don à un besoin
     */
    public function attribuer()
    {
        $besoin_id = $_POST['besoin_id'] ?? null;
        $don_id = $_POST['don_id'] ?? null;
        $quantite = $_POST['quantite'] ?? null;

        // Validation
        $errors = [];
        
        if (!$besoin_id) {
            $errors[] = "Besoin non identifié";
        }
        
        if (!$don_id) {
            $errors[] = "Veuillez sélectionner un don à attribuer";
        }
        
        if (!$quantite || $quantite <= 0) {
            $errors[] = "Veuillez saisir une quantité valide";
        }

        // Vérifier la quantité disponible du don
        $quantiteDisponible = Don::getQuantiteDisponible($don_id);
        if ($quantite > $quantiteDisponible) {
            $errors[] = "La quantité demandée ($quantite) dépasse le stock disponible ($quantiteDisponible)";
        }

        // Vérifier la quantité restante du besoin
        $quantiteRestante = Besoin::getQuantiteRestante($besoin_id);
        if ($quantite > $quantiteRestante) {
            $errors[] = "La quantité attribuée ($quantite) dépasse le besoin restant ($quantiteRestante)";
        }

        if (!empty($errors)) {
            // En cas d'erreur, rediriger vers la page d'attribution avec les erreurs
            $_SESSION['attribution_errors'] = $errors;
            $_SESSION['attribution_old'] = $_POST;
            Flight::redirect('/attributions?error=1');
            return;
        }

        try {
            // Créer l'attribution
            $data = [
                'besoin_id' => $besoin_id,
                'don_id' => $don_id,
                'quantite' => $quantite
            ];
            
            Attribution::insert($data);
            
            Flight::redirect('/attributions?success=1');
            
        } catch (\Exception $e) {
            $_SESSION['attribution_errors'] = [$e->getMessage()];
            Flight::redirect('/attributions?error=1');
        }
    }

    /**
     * Récupère les détails complets d'un besoin
     */
    private function getBesoinDetails($besoin_id)
    {
        $db = \app\config\Db::getInstance();
        
        $row = $db->fetchRow(
            "SELECT b.*, v.name AS ville_nom, bt.name AS besoin_nom, bt.type AS besoin_type
             FROM besoins b
             JOIN villes v ON b.ville_id = v.id
             JOIN besoins_types bt ON b.besoin_type_id = bt.id
             WHERE b.id = ?",
            [$besoin_id]
        );
        
        return $row ? $row->getData() : null;
    }

    /**
     * Supprime une attribution (pour correction)
     */
    public function delete($id)
    {
        Attribution::delete($id);
        Flight::redirect('/attributions?success=delete');
    }
}