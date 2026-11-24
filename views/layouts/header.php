<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'La Bonne Occaz') ?></title>
    <meta name="description" content="<?= htmlspecialchars($metaDesc ?? 'Votre spécialiste en véhicules d\'occasion de qualité') ?>">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1">
    <meta name="author" content="La Bonne Occaz">
    <meta name="theme-color" content="#e63946">
    <?php
    // Canonical : reconstruit l'URL propre depuis le routeur (pas REQUEST_URI qui contient ?url=...)
    $_canonicalPath = isset($_GET['url']) ? '/' . trim($_GET['url'], '/') : '/';
    if ($_canonicalPath !== '/') $_canonicalPath = rtrim($_canonicalPath, '/');
    ?>
    <link rel="canonical" href="<?= htmlspecialchars(BASE_URL . $_canonicalPath) ?>">

    <!-- Open Graph -->
    <meta property="og:title"        content="<?= htmlspecialchars($title ?? 'La Bonne Occaz') ?>">
    <meta property="og:description"  content="<?= htmlspecialchars($metaDesc ?? '') ?>">
    <meta property="og:type"         content="website">
    <meta property="og:image"        content="<?= BASE_URL ?>/public/images/og-image.jpg">
    <meta property="og:image:width"  content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:url"          content="<?= htmlspecialchars(BASE_URL . $_canonicalPath) ?>">
    <meta property="og:locale"       content="fr_FR">
    <meta property="og:site_name"    content="La Bonne Occaz">

    <!-- Twitter Card -->
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="<?= htmlspecialchars($title ?? 'La Bonne Occaz') ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($metaDesc ?? '') ?>">
    <meta name="twitter:image"       content="<?= BASE_URL ?>/public/images/og-image.jpg">
    <?php
    try {
        $_siteSettings = new SiteSettings();
        $_faviconUrl   = $_siteSettings->get('favicon_url');
        $_logoUrl      = $_siteSettings->get('logo_url');
        $_logoText     = $_siteSettings->get('logo_text', 'La Bonne Occaz');
    } catch (Exception $e) {
        $_faviconUrl = $_logoUrl = '';
        $_logoText   = 'La Bonne Occaz';
    }
    ?>
    <?php if ($_faviconUrl): ?>
    <link rel="icon" href="<?= htmlspecialchars($_faviconUrl) ?>">
    <?php else: ?>
    <link rel="icon" href="<?= BASE_URL ?>/public/images/favicon.svg" type="image/svg+xml">
    <?php endif; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/css/style.css?v=<?= filemtime(ROOT_PATH.'/public/css/style.css') ?>" rel="stylesheet">
    <script type="application/ld+json">{"@context":"https://schema.org","@type":"AutoDealer","name":"La Bonne Occaz","url":"<?= BASE_URL ?>","telephone":"<?= htmlspecialchars(cfg_phone()) ?>"}</script>
</head>
<body>

<header class="site-header" id="site-header">
    <nav class="navbar navbar-expand-lg navbar-dark py-0">
        <div class="container">

            <!-- LOGO -->
            <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>/">
                <?php if (!empty($_logoUrl)): ?>
                    <img src="<?= htmlspecialchars($_logoUrl) ?>"
                         alt="<?= htmlspecialchars($_logoText) ?>"
                         style="max-height:46px;max-width:180px;object-fit:contain;">
                <?php else: ?>
                    <span class="logo-icon me-2"><i class="fas fa-car-side"></i></span>
                    <div>
                        <div class="logo-name"><?= htmlspecialchars($_logoText) ?></div>
                        <div class="logo-tagline">VÉHICULES D'OCCASION</div>
                    </div>
                <?php endif; ?>
            </a>

            <!-- TOGGLE MOBILE -->
            <button class="navbar-toggler border-0" type="button"
                    data-bs-toggle="collapse" data-bs-target="#navbarMain"
                    aria-controls="navbarMain" aria-expanded="false" aria-label="Menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- MENU -->
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mx-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link px-3" href="<?= BASE_URL ?>/">
                            <i class="fas fa-home me-1"></i>Accueil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="<?= BASE_URL ?>/vehicules">
                            <i class="fas fa-car me-1"></i>Nos véhicules
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link px-3 dropdown-toggle" href="#"
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-wrench me-1"></i>Nos services
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark border-0 shadow">
                            <li><a class="dropdown-item py-2" href="<?= BASE_URL ?>/vehicules">
                                <i class="fas fa-search me-2 text-primary"></i>Trouver un véhicule</a></li>
                            <li><a class="dropdown-item py-2" href="<?= BASE_URL ?>/contact">
                                <i class="fas fa-calculator me-2 text-primary"></i>Estimation gratuite</a></li>
                            <li><a class="dropdown-item py-2" href="<?= BASE_URL ?>/contact">
                                <i class="fas fa-file-alt me-2 text-primary"></i>Accompagnement admin.</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="<?= BASE_URL ?>/#about">
                            <i class="fas fa-info-circle me-1"></i>À propos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="<?= BASE_URL ?>/contact">
                            <i class="fas fa-envelope me-1"></i>Contact
                        </a>
                    </li>
                </ul>

                <!-- ACTIONS DROITE -->
                <div class="d-flex align-items-center gap-2 ms-2">
                    <a href="<?= BASE_URL ?>/favoris" class="btn-favorites position-relative" title="Mes favoris" id="favoritesBtn">
                        <i class="fas fa-heart"></i>
                        <span class="favorites-count" id="favoritesCount" style="display:none">0</span>
                    </a>
                    <a href="tel:<?= str_replace([' ','.','(',')','-'],'', cfg_phone()) ?>"
                       class="btn btn-phone d-none d-xl-flex align-items-center gap-1">
                        <i class="fas fa-phone-alt"></i><?= htmlspecialchars(cfg_phone()) ?>
                    </a>
                    <a href="<?= BASE_URL ?>/vehicules" class="btn btn-primary-custom fw-semibold">
                        <i class="fas fa-search me-1"></i>Catalogue
                    </a>
                </div>
            </div>

        </div>
    </nav>
</header>

<?php if (!empty($flash)): ?>
<div class="flash-container position-fixed top-0 end-0 p-3" style="z-index:9999;margin-top:72px">
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show shadow">
        <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
        <?= htmlspecialchars($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php endif; ?>

<main class="main-content">
