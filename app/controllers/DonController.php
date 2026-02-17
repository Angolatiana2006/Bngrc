<?php

namespace app\controllers;

use Flight;
use app\models\Don;
use app\models\BesoinType;

class DonController
{
    /**
     * Affiche la liste des dons (stock BNGRC)
     */
    public function list()
    {
        $dons = Don::getAllWithDetails();
        
        Flight::render('dashboard/liste-dons', [
            'dons' => $dons
        ]);
    }

    /**
     * Affiche le formulaire de création d'un don
     */
    public function showCreateForm()
    {
        // Récupérer les types de besoins existants pour suggestion
        $typesBesoins = BesoinType::getAll();
        
        Flight::render('dashboard/ajout-don', [
            'typesBesoins' => $typesBesoins
        ]);
    }

   /**
 * Traite la création d'un nouveau don
 */
public function create()
{
    // Récupérer les données du formulaire
    $besoin_type_id = $_POST['besoin_type_id'] ?? null;
    $nom_besoin = $_POST['nom_besoin'] ?? '';
    $type_categorie = $_POST['type_categorie'] ?? null;
    $quantite = $_POST['quantite'] ?? null;

    // Validation simple
    $errors = [];
    
    // Vérifier qu'au moins une option est choisie
    if (!$besoin_type_id && (empty($nom_besoin) || !$type_categorie)) {
        $errors[] = "Veuillez soit sélectionner un type existant, soit remplir les champs pour un nouveau type (catégorie et nom)";
    }
    
    if (!$quantite || $quantite <= 0) {
        $errors[] = "Veuillez saisir une quantité valide (supérieure à 0)";
    }

    // S'il y a des erreurs, on réaffiche le formulaire
    if (!empty($errors)) {
        $typesBesoins = BesoinType::getAll();
        
        Flight::render('dashboard/ajout-don', [
            'typesBesoins' => $typesBesoins,
            'errors' => $errors,
            'old' => $_POST
        ]);
        return;
    }

    // Déterminer le besoin_type_id final
    if ($besoin_type_id) {
        // L'utilisateur a choisi un type existant
        $final_besoin_type_id = $besoin_type_id;
    } else {
        // L'utilisateur veut créer un nouveau type
        // Vérifier si ce type existe déjà avec le nom et la catégorie
        $besoinType = BesoinType::getByNameAndType($nom_besoin, $type_categorie);
        
        if (!$besoinType) {
            // Créer un nouveau type de besoin
            $typeData = [
                'type' => $type_categorie,
                'name' => $nom_besoin
            ];
            BesoinType::insert($typeData);
            
            // Récupérer l'ID du nouveau type
            $besoinType = BesoinType::getByNameAndType($nom_besoin, $type_categorie);
            $final_besoin_type_id = $besoinType['id'];
        } else {
            $final_besoin_type_id = $besoinType['id'];
        }
    }

    // Vérifier si un don avec ce besoin_type_id existe déjà
    $donExistant = Don::getByBesoinTypeId($final_besoin_type_id);
    
    if ($donExistant) {
        // Si le don existe, on met à jour la quantité (addition)
        $nouvelleQuantite = $donExistant['quantite'] + $quantite;
        Don::update($donExistant['id'], ['quantite' => $nouvelleQuantite]);
    } else {
        // Sinon, on crée un nouveau don
        $data = [
            'besoin_type_id' => $final_besoin_type_id,
            'quantite' => $quantite
        ];
        Don::insert($data);
    }

    // Rediriger vers la liste des dons
    Flight::redirect('/dons?success=1');
}
    
    /**
     * Affiche le formulaire d'édition d'un don
     */
    public function showEditForm($id)
    {
        $don = Don::getById($id);
        $typesBesoins = BesoinType::getAll();
        
        if (!$don) {
            Flight::halt(404, "Don non trouvé");
        }
        
        Flight::render('dashboard/edit-don', [
            'don' => $don,
            'typesBesoins' => $typesBesoins
        ]);
    }

    /**
     * Met à jour un don existant
     */
    public function update($id)
    {
        $don = Don::getById($id);
        
        if (!$don) {
            Flight::halt(404, "Don non trouvé");
        }
        
        $quantite = $_POST['quantite'] ?? null;
        
        $errors = [];
        if (!$quantite || $quantite <= 0) {
            $errors[] = "Veuillez saisir une quantité valide";
        }
        
        if (!empty($errors)) {
            $typesBesoins = BesoinType::getAll();
            Flight::render('dashboard/edit-don', [
                'don' => $don,
                'typesBesoins' => $typesBesoins,
                'errors' => $errors,
                'old' => $_POST
            ]);
            return;
        }
        
        $data = [
            'besoin_type_id' => $_POST['besoin_type_id'] ?? $don['besoin_type_id'],
            'quantite' => $quantite
        ];
        
        Don::update($id, $data);
        
        Flight::redirect('/dons?success=update');
    }

    /**
     * Supprime un don
     */
    public function delete($id)
    {
        Don::delete($id);
        Flight::redirect('/dons?success=delete');
    }

    /**
     * Affiche les dons disponibles (non encore attribués)
     */
    public function disponibles()
    {
        $dons = Don::getDisponibles();
        
        Flight::render('dashboard/dons-disponibles', [
            'dons' => $dons
        ]);
    }

    /**
     * Convertit l'ID du type en valeur texte
     */
    private function getTypeValue($type_id)
    {
        $types = [
            'nature' => 'nature',
            'materiaux' => 'materiaux',
            'argent' => 'argent',
            'autres' => 'autres',
            1 => 'nature',
            2 => 'nature',
            3 => 'materiaux',
            4 => 'materiaux',
            5 => 'argent',
            6 => 'autres'
        ];
        
        return $types[$type_id] ?? 'autres';
    }
}