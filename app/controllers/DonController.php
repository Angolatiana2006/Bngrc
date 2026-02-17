<?php

namespace app\controllers;

use Flight;
use app\models\Don;
use app\models\BesoinType;

class DonController
{
    
    public function list()
    {
        $dons = Don::getAllWithDetails();
        
        Flight::render('dashboard/liste-dons', [
            'dons' => $dons
        ]);
    }

    
    public function showCreateForm()
    {
        
        $typesBesoins = BesoinType::getAll();
        
        Flight::render('dashboard/ajout-don', [
            'typesBesoins' => $typesBesoins
        ]);
    }

  

    public function create()
    {
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $besoin_type_id = $_POST['besoin_type_id'] ?? null;
        $nom_besoin = $_POST['nom_besoin'] ?? '';
        $type_categorie = $_POST['type_categorie'] ?? null;
        $quantite = $_POST['quantite'] ?? null;
        $prix_unitaire = $_POST['prix_unitaire'] ?? null;

        $errors = [];
        
        
        if (!$besoin_type_id && (empty($nom_besoin) || !$type_categorie)) {
            $errors[] = "Veuillez soit sélectionner un type existant, soit remplir les champs pour un nouveau type (catégorie, nom et prix)";
        }
        
        if (!$quantite || $quantite <= 0) {
            $errors[] = "Veuillez saisir une quantité valide (supérieure à 0)";
        }

    
        if (!$besoin_type_id && !empty($nom_besoin) && $type_categorie) {
            if (!$prix_unitaire || $prix_unitaire <= 0) {
                $errors[] = "Veuillez saisir un prix unitaire valide pour le nouveau type de don";
            }
        }

        if (!empty($errors)) {
            $typesBesoins = BesoinType::getAll();
            
            Flight::render('dashboard/ajout-don', [
                'typesBesoins' => $typesBesoins,
                'errors' => $errors,
                'old' => $_POST
            ]);
            return;
        }

        
        if ($besoin_type_id) {
            $final_besoin_type_id = $besoin_type_id;
        } else {
        
            $besoinType = BesoinType::getByNameAndType($nom_besoin, $type_categorie);
            
            if (!$besoinType) {
            
                $typeData = [
                    'type' => $type_categorie,
                    'name' => $nom_besoin
                ];
                BesoinType::insert($typeData);
            
                $besoinType = BesoinType::getByNameAndType($nom_besoin, $type_categorie);
                $final_besoin_type_id = $besoinType['id'];

            
                if ($prix_unitaire && class_exists('app\models\PrixUnitaire')) {
                    \app\models\PrixUnitaire::updateByBesoinTypeId($final_besoin_type_id, $prix_unitaire);
                }
            } else {
                $final_besoin_type_id = $besoinType['id'];
            }
        }

    
        $donExistant = Don::getByBesoinTypeId($final_besoin_type_id);
        
        if ($donExistant) {
            $nouvelleQuantite = $donExistant['quantite'] + $quantite;
            Don::update($donExistant['id'], ['quantite' => $nouvelleQuantite]);
        } else {
            $data = [
                'besoin_type_id' => $final_besoin_type_id,
                'quantite' => $quantite
            ];
            Don::insert($data);
        }

        Flight::redirect('/dons?success=1');
    }
    
    
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

   
    public function delete($id)
    {
        Don::delete($id);
        Flight::redirect('/dons?success=delete');
    }

    
    public function disponibles()
    {
        $dons = Don::getDisponibles();
        
        Flight::render('dashboard/dons-disponibles', [
            'dons' => $dons
        ]);
    }

    
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