<?php

namespace app\controllers;

use Flight;
use app\models\Besoin;
use app\models\Ville;
use app\models\BesoinType;

class BesoinController
{
    /**
     * Affiche le formulaire de création d'un besoin
     */
    public function showCreateForm()
    {
        // Récupérer la liste des villes
        $villes = Ville::getAll();
        
        // Récupérer les types de besoins (nature, materiaux, argent, autres)
        $typesBesoins = BesoinType::getAll();
        
        Flight::render('dashboard/ajout-besoin', [
            'villes' => $villes,
            'typesBesoins' => $typesBesoins
        ]);
    }

    /**
     * Traite la création d'un nouveau besoin
     */
    public function create()
    {
        // Récupérer les données du formulaire
        $ville_id = $_POST['ville_id'] ?? null;
        $besoin_type_id = $_POST['besoin_type_id'] ?? null;
        $nom_besoin = $_POST['nom_besoin'] ?? ''; // Nouveau champ pour le nom personnalisé
        $quantite = $_POST['quantite'] ?? null;
        $unite = $_POST['unite'] ?? null;

        // Validation simple
        $errors = [];
        
        if (!$ville_id) {
            $errors[] = "Veuillez sélectionner une ville";
        }
        
        if (!$besoin_type_id) {
            $errors[] = "Veuillez sélectionner un type de besoin";
        }
        
        if (empty($nom_besoin)) {
            $errors[] = "Veuillez saisir le nom du besoin (riz, sucre, etc.)";
        }
        
        if (!$quantite || $quantite <= 0) {
            $errors[] = "Veuillez saisir une quantité valide (supérieure à 0)";
        }

        // S'il y a des erreurs, on réaffiche le formulaire avec les erreurs
        if (!empty($errors)) {
            $villes = Ville::getAll();
            $typesBesoins = BesoinType::getAll();
            
            Flight::render('dashboard/ajout-besoin', [
                'villes' => $villes,
                'typesBesoins' => $typesBesoins,
                'errors' => $errors,
                'old' => $_POST // Pour pré-remplir le formulaire
            ]);
            return;
        }

        // Vérifier si le type de besoin personnalisé existe déjà
        $besoinType = BesoinType::getByNameAndType($nom_besoin, $besoin_type_id);
        
        if (!$besoinType) {
            // Créer un nouveau type de besoin
            $typeData = [
                'type' => $this->getTypeValue($besoin_type_id), // On récupère la valeur du type
                'name' => $nom_besoin
            ];
            BesoinType::insert($typeData);
            
            // Récupérer l'ID du nouveau type
            $besoinType = BesoinType::getByNameAndType($nom_besoin, $besoin_type_id);
            $besoin_type_id = $besoinType['id'];
        } else {
            $besoin_type_id = $besoinType['id'];
        }

        // Préparer les données pour l'insertion
        $data = [
            'ville_id' => $ville_id,
            'besoin_type_id' => $besoin_type_id,
            'quantite' => $quantite,
            'unite' => $unite
        ];

        // Insérer le besoin
        Besoin::insert($data);

        // Rediriger vers le tableau de bord
        Flight::redirect('/dashboard?success=1');
    }
    
    /**
     * Convertit l'ID du type en valeur texte
     */
    private function getTypeValue($type_id)
    {
        $types = [
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