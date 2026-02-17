<?php

use app\controllers\DashboardController;
use app\controllers\VilleController;
use app\controllers\BesoinController;
use app\controllers\DonController;
use app\controllers\AttributionController;
use app\controllers\AchatController;
use app\controllers\ResetController;
use app\controllers\VenteController;

use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;

/** 
 * @var Router $router 
 * @var Engine $app
 */


$router->group('', function(Router $router) use ($app) {
    
    
    $router->get('/', function() use ($app) {
        header('Location: /dashboard');
        exit;
    });
    
    
    $router->get('/dashboard', [DashboardController::class, 'index']);
    $router->get('/dashboard/ville/@id:[0-9]+', [DashboardController::class, 'ville']);
    $router->get('/dashboard/export-pdf', [DashboardController::class, 'exportPdf']);
    $router->get('/dashboard/refresh', [DashboardController::class, 'refresh']);
    $router->get('/dashboard/statistiques', [DashboardController::class, 'statistiques']);
    $router->get('/dashboard/rapport', [DashboardController::class, 'showRapportForm']);
    $router->post('/dashboard/rapport/generer', [DashboardController::class, 'genererRapport']);
    $router->get('/dashboard/widget', [DashboardController::class, 'widget']);
    
    
    $router->get('/villes', [VilleController::class, 'list']);
    $router->get('/villes/create', [VilleController::class, 'showCreateForm']);
    $router->post('/villes/create', [VilleController::class, 'create']);
    $router->get('/villes/edit/@id:[0-9]+', [VilleController::class, 'showEditForm']);
    $router->post('/villes/update/@id:[0-9]+', [VilleController::class, 'update']);
    $router->get('/villes/delete/@id:[0-9]+', [VilleController::class, 'delete']);
    
    
    $router->get('/besoins', [BesoinController::class, 'list']);
    $router->get('/besoins/create', [BesoinController::class, 'showCreateForm']);
    $router->post('/besoins/create', [BesoinController::class, 'create']);
    $router->get('/besoins/edit/@id:[0-9]+', [BesoinController::class, 'showEditForm']);
    $router->post('/besoins/update/@id:[0-9]+', [BesoinController::class, 'update']);
    $router->get('/besoins/delete/@id:[0-9]+', [BesoinController::class, 'delete']);
    $router->get('/besoins/ville/@ville_id:[0-9]+', [BesoinController::class, 'getByVille']);
    
    
    $router->get('/dons', [DonController::class, 'list']);
    $router->get('/dons/create', [DonController::class, 'showCreateForm']);
    $router->post('/dons/create', [DonController::class, 'create']);
    $router->get('/dons/edit/@id:[0-9]+', [DonController::class, 'showEditForm']);
    $router->post('/dons/update/@id:[0-9]+', [DonController::class, 'update']);
    $router->get('/dons/delete/@id:[0-9]+', [DonController::class, 'delete']);
    $router->get('/dons/disponibles', [DonController::class, 'disponibles']);
    
    
$router->get('/attributions', [AttributionController::class, 'index']);
$router->get('/attributions/attribuer/@id:[0-9]+', [AttributionController::class, 'showAttributionForm']);
$router->post('/attributions/attribuer', [AttributionController::class, 'attribuer']);
$router->get('/attributions/delete/@id:[0-9]+', [AttributionController::class, 'delete']);


$router->get('/achats', [AchatController::class, 'index']);
$router->get('/achats/create', [AchatController::class, 'showCreateForm']);
$router->post('/achats/create', [AchatController::class, 'create']);
$router->get('/achats/ville/@ville_id:[0-9]+', [AchatController::class, 'filterByVille']);
$router->get('/achats/recap', [AchatController::class, 'recap']);
$router->get('/achats/recap-ajax', [AchatController::class, 'recapAjax']);


$router->post('/reset/reset', [ResetController::class, 'reset']);


$router->get('/ventes', [VenteController::class, 'index']);
$router->get('/ventes/vendre/@id:[0-9]+', [VenteController::class, 'showVenteForm']);
$router->post('/ventes/vendre', [VenteController::class, 'vendre']);

},[]);