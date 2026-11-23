<?php
/**
 * La Bonne Occaz — Point d'entrée PRODUCTION (Hostinger)
 * 
 * Sur Hostinger, les fichiers vont dans public_html/
 * Ce fichier doit être renommé index.php et placé dans public_html/
 * 
 * Structure sur le serveur :
 *   public_html/           ← contenu de /public/ + ce fichier renommé index.php
 *   app/                   ← contenu du reste du projet (hors public/)
 *   app/config/
 *   app/controllers/
 *   app/models/
 *   app/views/
 *   app/database/
 */

define('ROOT_PATH', dirname(__DIR__) . '/app');   // ← dossier app/ au-dessus de public_html
// Si tout est dans public_html dans un sous-dossier "app" :
// define('ROOT_PATH', __DIR__ . '/app');

// Détection automatique du schéma et host
$_scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$_host   = $_SERVER['HTTP_HOST'] ?? 'votredomaine.com';
define('BASE_URL', $_scheme . '://' . $_host);    // ← pas de sous-dossier en prod

define('SITE_NAME', 'La Bonne Occaz');
define('SITE_DESCRIPTION', 'Votre spécialiste en véhicules d\'occasion de qualité');
define('VIEWS_PATH',   ROOT_PATH . '/views');
define('UPLOADS_PATH', __DIR__ . '/uploads');      // dans public_html/uploads/
define('UPLOADS_URL',  BASE_URL . '/uploads');

define('VEHICLES_PER_PAGE', 12);
define('ADMIN_PER_PAGE',    20);
define('MAX_FILE_SIZE',     5 * 1024 * 1024);
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp', 'gif']);

define('CONTACT_EMAIL',   'contact@labonneoccaz.fr');
define('CONTACT_PHONE',   '04 50 XX XX XX');
define('CONTACT_ADDRESS', '12 Rue de l\'Industrie, 74100 Annemasse');
define('CONTACT_HOURS',   'Lun-Sam : 9h00 - 18h30');
define('WHATSAPP_NUMBER', '33450XXXXXX');
define('DEBUG_MODE', false);   // ← TOUJOURS false en production !

date_default_timezone_set('Europe/Paris');
error_reporting(0);
ini_set('display_errors', 0);

require_once ROOT_PATH . '/config/database.php';

// ... (reste identique à index.php local)
// Copiez le contenu de votre index.php local à partir de la ligne "Coordonnées dynamiques"
