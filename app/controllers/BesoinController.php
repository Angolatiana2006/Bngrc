<?php

namespace app\controllers;

use Flight;
use app\models\Besoin;
use app\models\Ville;
use app\models\BesoinType;

class BesoinController
{
    
    public function showCreateForm()
    {
        
        $villes = Ville::getAll();
        
        
        $typesBesoins = BesoinType::getAll();
        
        Flight::render('dashboard/ajout-besoin', [
            'villes' => $villes,
            'typesBesoins' => $typesBesoins
        ]);
    }

    
    public function create()
    {
        
        $ville_id = $_POST['ville_id'] ?? null;
        $besoin_type_id = $_POST['besoin_type_id'] ?? null;
        $nom_besoin = $_POST['nom_besoin'] ?? ''; 
        $quantite = $_POST['quantite'] ?? null;
        $unite = $_POST['unite'] ?? null;

        
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

        
        if (!empty($errors)) {
            $villes = Ville::getAll();
            $typesBesoins = BesoinType::getAll();
            
            Flight::render('dashboard/ajout-besoin', [
                'villes' => $villes,
                'typesBesoins' => $typesBesoins,
                'errors' => $errors,
                'old' => $_POST 
            ]);
            return;
        }

        
        $besoinType = BesoinType::getByNameAndType($nom_besoin, $besoin_type_id);
        
        if (!$besoinType) {
            
            $typeData = [
                'type' => $this->getTypeValue($besoin_type_id), 
                'name' => $nom_besoin
            ];
            BesoinType::insert($typeData);
            
            
            $besoinType = BesoinType::getByNameAndType($nom_besoin, $besoin_type_id);
            $besoin_type_id = $besoinType['id'];
        } else {
            $besoin_type_id = $besoinType['id'];
        }

        
        $data = [
            'ville_id' => $ville_id,
            'besoin_type_id' => $besoin_type_id,
            'quantite' => $quantite,
            'unite' => $unite
        ];

        
        Besoin::insert($data);

        
        Flight::redirect('/dashboard?success=1');
    }
    
    
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