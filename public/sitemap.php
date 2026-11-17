<?php
/**
 * Sitemap XML dynamique — La Bonne Occaz
 */
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/config/database.php';

header('Content-Type: application/xml; charset=UTF-8');
header('X-Robots-Tag: noindex');

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
$base   = $scheme . '://' . $host . '/bon-occaz';

$db = Database::getInstance();

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
         xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

// Pages statiques
$statics = [
    ['/',          '1.0',  'daily'],
    ['/vehicules', '0.95', 'daily'],
    ['/contact',   '0.7',  'monthly'],
];
foreach ($statics as [$path, $prio, $freq]) {
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($base . $path) . "</loc>\n";
    echo "    <changefreq>$freq</changefreq>\n";
    echo "    <priority>$prio</priority>\n";
    echo "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
    echo "  </url>\n";
}

// Fiches véhicules
$vehicles = $db->query(
    "SELECT v.slug, v.updated_at, vi.filename AS main_image
     FROM vehicles v
     LEFT JOIN vehicle_images vi ON vi.vehicle_id = v.id AND vi.is_main = 1
     WHERE v.status = 'available'
     ORDER BY v.updated_at DESC"
)->fetchAll();

foreach ($vehicles as $v) {
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($base . '/vehicule/' . $v['slug']) . "</loc>\n";
    echo "    <changefreq>weekly</changefreq>\n";
    echo "    <priority>0.85</priority>\n";
    echo "    <lastmod>" . date('Y-m-d', strtotime($v['updated_at'])) . "</lastmod>\n";
    if (!empty($v['main_image'])) {
        $imgUrl = $base . '/public/uploads/vehicles/' . $v['main_image'];
        echo "    <image:image>\n";
        echo "      <image:loc>" . htmlspecialchars($imgUrl) . "</image:loc>\n";
        echo "    </image:image>\n";
    }
    echo "  </url>\n";
}

echo "</urlset>\n";
