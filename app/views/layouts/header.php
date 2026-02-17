<!doctype html>
<html class="no-js" lang="fr">

<head>
    <meta charset="utf-8">
    <title>BNGRC – <?= $pageTitle ?? 'Tableau de bord' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/png" href="/assets/images/icon/favicon.ico">
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="/assets/css/themify-icons.css">
    <link rel="stylesheet" href="/assets/css/metisMenu.css">
    <link rel="stylesheet" href="/assets/css/slicknav.min.css">
    <link rel="stylesheet" href="/assets/css/typography.css">
    <link rel="stylesheet" href="/assets/css/default-css.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="stylesheet" href="/assets/css/responsive.css">
    <script src="/assets/js/jquery.slimscroll.min.js"></script>
</head>

<body>

<div class="page-container">

    
    <div class="sidebar-menu">
        <div class="sidebar-header">
            <div class="logo">
                <h3 class="text-white">BNGRC</h3>
            </div>
        </div>
        <div class="main-menu">
            <div class="menu-inner">
                <nav>
                    <ul class="metismenu" id="menu">
                        <li class="<?= ($activeMenu == 'dashboard') ? 'active' : '' ?>">
                            <a href="/dashboard"><i class="ti-dashboard"></i><span>Tableau de bord</span></a>
                        </li>
                        <li class="<?= ($activeMenu == 'besoin') ? 'active' : '' ?>">
                            <a href="/besoins/create"><i class="ti-plus"></i><span>Créer un besoin</span></a>
                        </li>
                        <li class="<?= ($activeMenu == 'don') ? 'active' : '' ?>">
                            <a href="/dons/create"><i class="ti-gift"></i><span>Créer un don</span></a>
                        </li>
                        <li class="<?= ($activeMenu == 'attribution') ? 'active' : '' ?>">
                            <a href="/attributions"><i class="ti-share"></i><span>Attribuer un don</span></a>
                        </li>
                        <li class="<?= ($activeMenu == 'dons_liste') ? 'active' : '' ?>">
                            <a href="/dons"><i class="ti-list"></i><span>Liste des dons</span></a>
                        </li>
                        <li class="<?= ($activeMenu == 'achats') ? 'active' : '' ?>">
                            <a href="/achats"><i class="fa fa-shopping-cart"></i><span>Achats</span></a>
                        </li>
                        <li class="<?= ($activeMenu == 'recap') ? 'active' : '' ?>">
                            <a href="/achats/recap"><i class="fa fa-pie-chart"></i><span>Récapitulatif</span></a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
   

    
    <div class="main-content">

        
        <div class="header-area">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="nav-btn pull-left">
                        <span></span><span></span><span></span>
                    </div>
                </div>
                <div class="col-md-6 clearfix">
                    <div class="user-profile pull-right">
                        <h4 class="user-name">Administrateur BNGRC</h4>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="page-title-area">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h4 class="page-title"><?= $pageTitle ?? 'Tableau de bord' ?></h4>
                </div>
                <div class="col-sm-6">
                    <div class="breadcrumbs pull-right">
                        <a href="/dashboard">Tableau de bord</a> 
                        <?php if(isset($breadcrumbs)): ?>
                            <?php foreach($breadcrumbs as $crumb): ?>
                                <span>/</span> 
                                <?php if(isset($crumb['url'])): ?>
                                    <a href="<?= $crumb['url'] ?>"><?= $crumb['label'] ?></a>
                                <?php else: ?>
                                    <span><?= $crumb['label'] ?></span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="main-content-inner">