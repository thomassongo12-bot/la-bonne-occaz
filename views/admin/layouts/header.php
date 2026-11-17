<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Administration') ?> | La Bonne Occaz</title>
    <meta name="robots" content="noindex, nofollow">
    <?php
    try {
        $_adminSettings = new SiteSettings();
        $_adminFavicon  = $_adminSettings->get('favicon_url');
        $_adminLogo     = $_adminSettings->get('logo_url');
        $_adminLogoText = $_adminSettings->get('logo_text', 'La Bonne Occaz');
    } catch(Exception $e) {
        $_adminFavicon = $_adminLogo = '';
        $_adminLogoText = 'La Bonne Occaz';
    }
    ?>
    <?php if ($_adminFavicon): ?>
    <link rel="icon" href="<?= htmlspecialchars($_adminFavicon) ?>" type="image/x-icon">
    <?php endif; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/css/admin.css" rel="stylesheet">
</head>
<body class="admin-body">

<!-- Sidebar -->
<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-header">
        <?php if (!empty($_adminLogo)): ?>
        <div class="sidebar-logo-img">
            <img src="<?= htmlspecialchars($_adminLogo) ?>"
                 alt="<?= htmlspecialchars($_adminLogoText) ?>"
                 style="max-height:38px; max-width:160px; object-fit:contain; filter:brightness(0) invert(1);">
        </div>
        <small class="sidebar-role">Administration</small>
        <?php else: ?>
        <div class="sidebar-brand">
            <i class="fas fa-car-side me-2"></i>
            <span><?= htmlspecialchars($_adminLogoText) ?></span>
        </div>
        <small class="sidebar-role">Administration</small>
        <?php endif; ?>
    </div>

    <nav class="sidebar-nav">
        <ul>
            <li class="nav-label">Principal</li>
            <li>
                <a href="<?= BASE_URL ?>/admin/dashboard"
                   class="<?= (strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/dashboard') !== false) ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i><span>Tableau de bord</span>
                </a>
            </li>

            <li class="nav-label">Catalogue</li>
            <li>
                <a href="<?= BASE_URL ?>/admin/vehicules"
                   class="<?= (strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/vehicules') !== false) ? 'active' : '' ?>">
                    <i class="fas fa-car"></i><span>Véhicules</span>
                </a>
            </li>

            <li class="nav-label">Demandes</li>
            <li>
                <a href="<?= BASE_URL ?>/admin/demandes"
                   class="<?= (strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/demandes') !== false) ? 'active' : '' ?>">
                    <i class="fas fa-inbox"></i><span>Toutes les demandes</span>
                    <?php
                    try {
                        $cm = new ContactRequest(); $sm = new SellingRequest();
                        $n = $cm->countNew() + $sm->countNew();
                        if ($n > 0) echo "<span class='sidebar-badge'>$n</span>";
                    } catch(Exception $e) {}
                    ?>
                </a>
            </li>

            <li class="nav-label">Contenu</li>
            <li>
                <a href="<?= BASE_URL ?>/admin/temoignages"
                   class="<?= (strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/temoignages') !== false) ? 'active' : '' ?>">
                    <i class="fas fa-star"></i><span>Témoignages</span>
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>/admin/parametres"
                   class="<?= (strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/parametres') !== false) ? 'active' : '' ?>">
                    <i class="fas fa-cog"></i><span>Paramètres</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <a href="<?= BASE_URL ?>/" target="_blank" class="sidebar-link-external">
            <i class="fas fa-external-link-alt me-2"></i>Voir le site
        </a>
        <a href="<?= BASE_URL ?>/admin/deconnexion" class="sidebar-logout">
            <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
        </a>
    </div>
</aside>

<!-- Overlay mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Main wrapper -->
<div class="admin-main" id="adminMain">

    <!-- Top bar -->
    <header class="admin-topbar">
        <button class="sidebar-toggle-btn" id="sidebarToggle" aria-label="Menu">
            <i class="fas fa-bars"></i>
        </button>
        <div class="topbar-title"><?= htmlspecialchars($title ?? 'Administration') ?></div>
        <div class="topbar-right">
            <span class="topbar-user">
                <i class="fas fa-user-circle me-1"></i>
                <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?>
            </span>
        </div>
    </header>

    <!-- Flash -->
    <?php if (!empty($flash)): ?>
    <div class="admin-flash">
        <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
            <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
            <?= htmlspecialchars($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Page content -->
    <div class="admin-content">
