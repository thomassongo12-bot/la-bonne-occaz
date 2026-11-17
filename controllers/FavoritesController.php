<?php
require_once ROOT_PATH . '/controllers/BaseController.php';

class FavoritesController extends BaseController {

    public function index($param = null): void {
        // Les favoris sont gérés côté client (localStorage)
        // Cette page sert juste de conteneur
        $this->render('favorites/index', [
            'title'    => 'Mes favoris - La Bonne Occaz',
            'metaDesc' => 'Retrouvez tous vos véhicules favoris sauvegardés.',
        ]);
    }

    /** API : récupère les données de plusieurs véhicules par IDs */
    public function api($param = null): void {
        header('Content-Type: application/json; charset=utf-8');
        $ids = $_GET['ids'] ?? '';
        if (!$ids) { echo json_encode([]); exit; }

        $idList = array_filter(array_map('intval', explode(',', $ids)));
        if (empty($idList)) { echo json_encode([]); exit; }

        // Sécurité : limiter à 50 favoris max
        $idList = array_slice($idList, 0, 50);
        $placeholders = implode(',', array_fill(0, count($idList), '?'));

        $db = Database::getInstance();
        $sql = "SELECT v.id, v.slug, v.model, v.version, v.year, v.mileage, v.price, v.price_promo,
                       b.name AS brand_name, f.name AS fuel_name, t.name AS transmission_name,
                       vi.filename AS main_image
                FROM vehicles v
                JOIN brands b ON v.brand_id=b.id
                JOIN fuel_types f ON v.fuel_type_id=f.id
                JOIN transmissions t ON v.transmission_id=t.id
                LEFT JOIN vehicle_images vi ON vi.vehicle_id=v.id AND vi.is_main=1
                WHERE v.id IN ($placeholders) AND v.status='available'";
        $stmt = $db->prepare($sql);
        $stmt->execute($idList);
        $vehicles = $stmt->fetchAll();

        // Ajout de l'URL de l'image
        foreach ($vehicles as &$v) {
            $v['image_url'] = $v['main_image']
                ? UPLOADS_URL . '/vehicles/' . $v['main_image']
                : BASE_URL . '/public/images/no-vehicle.jpg';
            $v['url'] = BASE_URL . '/vehicule/' . $v['slug'];
        }

        echo json_encode($vehicles, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
