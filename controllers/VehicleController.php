<?php
require_once ROOT_PATH . '/controllers/BaseController.php';

class VehicleController extends BaseController {

    private Vehicle $vehicleModel;
    private Brand $brandModel;

    public function __construct() {
        $this->vehicleModel = new Vehicle();
        $this->brandModel   = new Brand();
    }

    /** Page catalogue */
    public function catalog($param = null): void {
        $db = Database::getInstance();

        // Filtres depuis GET
        $filters = $this->extractFilters();
        $sort    = $_GET['sort']  ?? 'newest';
        $page    = max(1, (int)($_GET['page'] ?? 1));

        $vehicles = $this->vehicleModel->getCatalog($filters, $page, VEHICLES_PER_PAGE, $sort);
        $total    = $this->vehicleModel->countCatalog($filters);
        $pages    = (int)ceil($total / VEHICLES_PER_PAGE);

        $brands       = $this->brandModel->getAllWithCount();
        $fuel_types   = $db->query("SELECT * FROM fuel_types ORDER BY name")->fetchAll();
        $transmissions = $db->query("SELECT * FROM transmissions ORDER BY name")->fetchAll();
        $body_types   = $db->query("SELECT * FROM body_types ORDER BY name")->fetchAll();

        // Si requête AJAX (filtres dynamiques)
        if ($this->isAjax()) {
            ob_start();
            require VIEWS_PATH . '/vehicles/_cards.php';
            $html = ob_get_clean();
            $this->json(['html' => $html, 'total' => $total, 'pages' => $pages]);
        }

        $this->render('vehicles/catalog', [
            'title'        => 'Catalogue véhicules - La Bonne Occaz',
            'metaDesc'     => 'Parcourez notre catalogue de véhicules d\'occasion. Filtres avancés, prix transparents.',
            'vehicles'     => $vehicles,
            'total'        => $total,
            'page'         => $page,
            'pages'        => $pages,
            'sort'         => $sort,
            'filters'      => $filters,
            'brands'       => $brands,
            'fuel_types'   => $fuel_types,
            'transmissions'=> $transmissions,
            'body_types'   => $body_types,
        ]);
    }

    /** Page détail d'un véhicule */
    public function detail($slug = null): void {
        if (!$slug) { $this->notFound(); return; }

        $vehicle = $this->vehicleModel->getBySlug($slug);
        if (!$vehicle) { $this->notFound(); return; }

        $images  = $this->vehicleModel->getImages($vehicle['id']);
        $similar = $this->vehicleModel->getSimilar(
            $vehicle['id'],
            $vehicle['brand_id'],
            $vehicle['body_type_id'] ?? 0,
            $vehicle['price'],
            4
        );

        $equipment = !empty($vehicle['equipment']) ? json_decode($vehicle['equipment'], true) : [];

        $title   = ($vehicle['brand_name'] ?? '') . ' ' . $vehicle['model'] . ' ' . ($vehicle['version'] ?? '') . ' - La Bonne Occaz';
        $metaDesc = !empty($vehicle['meta_description'])
            ? $vehicle['meta_description']
            : substr(strip_tags($vehicle['description'] ?? ''), 0, 160);

        $this->render('vehicles/detail', [
            'title'    => $title,
            'metaDesc' => $metaDesc,
            'vehicle'  => $vehicle,
            'images'   => $images,
            'similar'  => $similar,
            'equipment'=> $equipment,
            'flash'    => $this->getFlash(),
        ]);
    }

    private function notFound(): void {
        http_response_code(404);
        $this->render('errors/404', ['title' => 'Véhicule introuvable']);
    }

    private function extractFilters(): array {
        $allowed = ['brand_id','brand_slug','fuel_type_id','transmission_id','body_type_id',
                    'price_min','price_max','year_min','year_max','mileage_max','power_min','color','search'];
        $f = [];
        foreach ($allowed as $k) {
            if (!empty($_GET[$k])) $f[$k] = $_GET[$k];
        }
        return $f;
    }
}
