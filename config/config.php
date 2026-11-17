<?php
/**
 * La Bonne Occaz - Configuration générale
 */

// URL de base (adapter selon l'environnement)
define('BASE_URL', 'http://localhost/bon-occaz');
define('SITE_NAME', 'La Bonne Occaz');
define('SITE_DESCRIPTION', 'Votre spécialiste en véhicules d\'occasion de qualité');
define('ROOT_PATH', dirname(__DIR__));
define('VIEWS_PATH', ROOT_PATH . '/views');
define('UPLOADS_PATH', ROOT_PATH . '/public/uploads');
define('UPLOADS_URL', BASE_URL . '/public/uploads');

// Pagination
define('VEHICLES_PER_PAGE', 12);
define('ADMIN_PER_PAGE', 20);

// Upload images
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5 MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp', 'gif']);

// Contact
define('CONTACT_EMAIL', 'contact@labonneoccaz.fr');
define('CONTACT_PHONE', '04 50 XX XX XX');
define('CONTACT_ADDRESS', '12 Rue de l\'Industrie, 74100 Annemasse');
define('CONTACT_HOURS', 'Lun-Sam : 9h00 - 18h30');

// WhatsApp
define('WHATSAPP_NUMBER', '33450XXXXXX');

// Environnement
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('Europe/Paris');
