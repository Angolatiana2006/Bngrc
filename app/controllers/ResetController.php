<?php

namespace app\controllers;

use Flight;
use app\config\Db;

class ResetController
{
   
    private function initBackupTables($db)
    {
        
        $tables = $db->fetchAll("SHOW TABLES LIKE 'dons_initial'");
        if (empty($tables)) {
            
            $db->exec("CREATE TABLE dons_initial LIKE dons");
        }

        $tables = $db->fetchAll("SHOW TABLES LIKE 'besoins_initial'");
        if (empty($tables)) {
            
            $db->exec("CREATE TABLE besoins_initial LIKE besoins");
        }

        
        $countDons = $db->fetchRow("SELECT COUNT(*) as total FROM dons_initial");
        $countBesoins = $db->fetchRow("SELECT COUNT(*) as total FROM besoins_initial");

       
        if ($countDons['total'] == 0) {
            $db->exec("TRUNCATE TABLE dons_initial");
            $db->exec("INSERT INTO dons_initial SELECT * FROM dons");
        }

        if ($countBesoins['total'] == 0) {
            $db->exec("TRUNCATE TABLE besoins_initial");
            $db->exec("INSERT INTO besoins_initial SELECT * FROM besoins");
        }

        return true;
    }

    
    public function reset()
    {
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $db = Db::getInstance();

        try {
            
            $this->initBackupTables($db);

            
            $db->beginTransaction();

            
            $db->exec("DELETE FROM attributions");
            $db->exec("DELETE FROM achats");
            
            
            $db->exec("DELETE FROM dons");
            $db->exec("
                INSERT INTO dons (id, besoin_type_id, quantite, date_don)
                SELECT id, besoin_type_id, quantite, date_don FROM dons_initial
            ");
            
            
            $db->exec("DELETE FROM besoins");
            $db->exec("
                INSERT INTO besoins (id, ville_id, besoin_type_id, quantite, unite, date_creation)
                SELECT id, ville_id, besoin_type_id, quantite, unite, date_creation FROM besoins_initial
            ");
            
           
            $db->commit();
            
            $_SESSION['reset_success'] = "Toutes les actions ont été réinitialisées avec succès";
            Flight::redirect('/dashboard?reset_success=1');
            
        } catch (\Exception $e) {
            
            try {
                $db->rollBack();
            } catch (\Exception $rollbackError) {
                
            }
            
            $_SESSION['reset_error'] = "Erreur lors de la réinitialisation: " . $e->getMessage();
            Flight::redirect('/dashboard?reset_error=1');
        }
    }
}