<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>404 - Page introuvable | La Bonne Occaz</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="<?= defined('BASE_URL') ? BASE_URL : '' ?>/public/css/style.css" rel="stylesheet">
</head>
<body>
<div class="container text-center" style="padding:120px 20px">
    <div style="font-size:8rem;color:#e63946">404</div>
    <h1 class="mt-2">Page introuvable</h1>
    <p class="text-muted lead">La page que vous recherchez n'existe pas ou a été déplacée.</p>
    <div class="mt-4 d-flex gap-3 justify-content-center flex-wrap">
        <a href="<?= defined('BASE_URL') ? BASE_URL : '' ?>/" class="btn btn-primary btn-lg">
            <i class="fas fa-home me-2"></i>Retour à l'accueil
        </a>
        <a href="<?= defined('BASE_URL') ? BASE_URL : '' ?>/vehicules" class="btn btn-outline-primary btn-lg">
            <i class="fas fa-car me-2"></i>Voir les véhicules
        </a>
    </div>
</div>
</body>
</html>
