<?php
/**
 * La Bonne Occaz - Routeur principal
 */

define('ROOT_PATH', __DIR__);
$_host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
$_scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
// En local XAMPP le projet est dans /bon-occaz, en production il est à la racine
$_isLocal = in_array($_host, ['localhost', 'localhost:8080', '127.0.0.1', '127.0.0.1:8080']);
$_subdir  = $_isLocal ? '/bon-occaz' : '';
define('BASE_URL', $_scheme . '://' . $_host . $_subdir);
define('SITE_NAME', 'La Bonne Occaz');
define('SITE_DESCRIPTION', 'Votre spécialiste en véhicules d\'occasion de qualité');
define('VIEWS_PATH', ROOT_PATH . '/views');
define('UPLOADS_PATH', ROOT_PATH . '/public/uploads');
define('UPLOADS_URL', BASE_URL . '/public/uploads');

// Pagination
define('VEHICLES_PER_PAGE', 12);
define('ADMIN_PER_PAGE', 20);

// Upload images
define('MAX_FILE_SIZE', 5 * 1024 * 1024);
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp', 'gif']);

// Contact — valeurs par défaut (écrasées par la BDD ci-dessous)
define('CONTACT_EMAIL',   'contact@labonneoccaz.fr');
define('CONTACT_PHONE',   '04 50 XX XX XX');
define('CONTACT_ADDRESS', '12 Rue de l\'Industrie, 74100 Annemasse');
define('CONTACT_HOURS',   'Lun-Sam : 9h00 - 18h30');
define('WHATSAPP_NUMBER', '33450XXXXXX');

// DEBUG_MODE : false automatiquement en production (pas localhost)
define('DEBUG_MODE', $_isLocal);
unset($_isLocal, $_subdir);

date_default_timezone_set('Europe/Paris');

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

require_once ROOT_PATH . '/config/database.php';

// ── Coordonnées dynamiques depuis site_settings ──────────────────
// On écrase les constantes par défaut avec les valeurs enregistrées en admin
try {
    $_db  = Database::getInstance();
    $_res = $_db->query(
        "SELECT setting_key, setting_val FROM site_settings
         WHERE setting_key IN ('contact_phone','contact_email','contact_address','contact_hours','whatsapp_number')
         AND setting_val IS NOT NULL AND setting_val != ''"
    );
    foreach ($_res->fetchAll() as $_row) {
        $_k = $_row['setting_key'];
        $_v = trim($_row['setting_val']);
        if (empty($_v)) continue;
        switch ($_k) {
            case 'contact_phone':   if (!defined('_DYN_PHONE'))   { define('_DYN_PHONE',   $_v); } break;
            case 'contact_email':   if (!defined('_DYN_EMAIL'))   { define('_DYN_EMAIL',   $_v); } break;
            case 'contact_address': if (!defined('_DYN_ADDRESS')) { define('_DYN_ADDRESS', $_v); } break;
            case 'contact_hours':   if (!defined('_DYN_HOURS'))   { define('_DYN_HOURS',   $_v); } break;
            case 'whatsapp_number': if (!defined('_DYN_WA'))      { define('_DYN_WA',      $_v); } break;
        }
    }
    unset($_db, $_res, $_row, $_k, $_v);
} catch (Exception $_e) {}

// Redéfinir les constantes de contact avec les valeurs BDD si disponibles
// PHP ne permet pas de redéfinir une constante, on utilise une fonction helper
function cfg_phone():   string { return defined('_DYN_PHONE')   ? _DYN_PHONE   : CONTACT_PHONE; }
function cfg_email():   string { return defined('_DYN_EMAIL')   ? _DYN_EMAIL   : CONTACT_EMAIL; }
function cfg_address(): string { return defined('_DYN_ADDRESS') ? _DYN_ADDRESS : CONTACT_ADDRESS; }
function cfg_hours():   string { return defined('_DYN_HOURS')   ? _DYN_HOURS   : CONTACT_HOURS; }
function cfg_wa():      string { return defined('_DYN_WA')      ? _DYN_WA      : WHATSAPP_NUMBER; }

// Autoload
spl_autoload_register(function ($class) {
    $dirs = [
        ROOT_PATH . '/models/',
        ROOT_PATH . '/controllers/',
        ROOT_PATH . '/controllers/admin/',
    ];
    foreach ($dirs as $dir) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

session_start();

// ── Tracking visiteurs (exclut l'admin et les bots) ──
if (!isset($_SESSION['admin_id']) && strpos($url ?? '', 'admin') === false) {
    try {
        $db = Database::getInstance();
        $ipHash = hash('sha256', $_SERVER['REMOTE_ADDR'] ?? 'unknown');
        $page   = '/' . ($url ?? '');
        // 1 enregistrement par IP par heure max (anti-spam)
        $check  = $db->prepare("SELECT id FROM visitor_logs WHERE ip_hash=:h AND visited_date=CURDATE() AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR) LIMIT 1");
        $check->execute([':h' => $ipHash]);
        if (!$check->fetch()) {
            $db->prepare("INSERT INTO visitor_logs (visited_date, ip_hash, page_url) VALUES (CURDATE(),:h,:p)")
               ->execute([':h' => $ipHash, ':p' => substr($page, 0, 255)]);
        }
    } catch (Exception $e) { /* silencieux */ }
}

// Récupération de l'URL
$url = isset($_GET['url']) ? trim($_GET['url'], '/') : '';
$segments = array_filter(explode('/', $url), fn($s) => $s !== '');
$segments = array_values($segments);

$seg0 = $segments[0] ?? '';
$seg1 = $segments[1] ?? '';
$seg2 = $segments[2] ?? '';
$seg3 = $segments[3] ?? '';

// ---- Résolution du contrôleur et de l'action ----
$controllerClass = 'HomeController';
$action = 'index';
$param  = null;

switch ($seg0) {
    case '':
    case 'accueil':
        $controllerClass = 'HomeController';
        $action = 'index';
        break;

    case 'vehicules':
        $controllerClass = 'VehicleController';
        $action = 'catalog';
        break;

    case 'vehicule':
        $controllerClass = 'VehicleController';
        $action = 'detail';
        $param  = $seg1;
        break;

    case 'reserver':
        $controllerClass = 'ReservationController';
        $action = 'index';
        $param  = $seg1;
        if ($seg1 === 'submit' && $seg2) {
            $action = 'submit';
            $param  = $seg2;
        }
        break;

    case 'contact':
        $controllerClass = 'ContactController';
        $action = ($seg1 !== '') ? $seg1 : 'index';
        break;

    case 'favoris':
        $controllerClass = 'FavoritesController';
        $action = 'index';
        break;

    case 'vendre':
        $controllerClass = 'SellingController';
        $action = ($seg1 !== '') ? $seg1 : 'index';
        break;

    case 'admin':
        switch ($seg1) {
            case '':
            case 'login':
                $controllerClass = 'AdminAuthController';
                $action = 'index';
                break;
            case 'connexion':
                $controllerClass = 'AdminAuthController';
                $action = 'login';
                break;
            case 'deconnexion':
                $controllerClass = 'AdminAuthController';
                $action = 'logout';
                break;
            case 'dashboard':
                $controllerClass = 'AdminDashboardController';
                $action = 'index';
                break;
            case 'reservations':
                $controllerClass = 'AdminReservationController';
                $action = $seg2 !== '' ? $seg2 : 'index';
                $param  = $seg3 !== '' ? $seg3 : null;
                break;
            case 'vehicules':
                $controllerClass = 'AdminVehicleController';
                $action = $seg2 !== '' ? $seg2 : 'index';
                $param  = $seg3 !== '' ? $seg3 : null;
                break;
            case 'demandes':
                $controllerClass = 'AdminRequestController';
                $action = $seg2 !== '' ? $seg2 : 'index';
                $param  = $seg3 !== '' ? $seg3 : null;
                break;
            case 'temoignages':
                $controllerClass = 'AdminTestimonialController';
                $action = $seg2 !== '' ? $seg2 : 'index';
                $param  = $seg3 !== '' ? $seg3 : null;
                break;
            case 'parametres':
                $controllerClass = 'AdminSettingsController';
                $action = $seg2 !== '' ? $seg2 : 'index';
                break;
            default:
                $controllerClass = 'AdminAuthController';
                $action = 'index';
        }
        break;

    default:
        $controllerClass = 'HomeController';
        $action = 'notFound';
}

// Chargement
$dirs = [
    ROOT_PATH . '/controllers/' . $controllerClass . '.php',
    ROOT_PATH . '/controllers/admin/' . $controllerClass . '.php',
];
$loaded = false;
foreach ($dirs as $file) {
    if (file_exists($file)) {
        require_once $file;
        $loaded = true;
        break;
    }
}

if ($loaded && class_exists($controllerClass)) {
    $ctrl = new $controllerClass();
    if (method_exists($ctrl, $action)) {
        $ctrl->$action($param);
    } else {
        $ctrl->index($param);
    }
} else {
    http_response_code(404);
    echo '<h1>404 - Page introuvable</h1>';
}
