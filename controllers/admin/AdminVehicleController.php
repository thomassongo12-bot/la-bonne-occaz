<?php
require_once ROOT_PATH . '/controllers/admin/AdminBaseController.php';

class AdminVehicleController extends AdminBaseController {

    private Vehicle $model;
    private Brand $brandModel;

    public function __construct() {
        parent::__construct();
        $this->model      = new Vehicle();
        $this->brandModel = new Brand();
    }

    public function index($param = null): void {
        $page   = max(1, (int)($_GET['page']   ?? 1));
        $search = $this->sanitize($_GET['search'] ?? '');

        $vehicles = $this->model->getAll($page, ADMIN_PER_PAGE, $search);
        $total    = $this->model->countAll($search);

        $this->renderAdmin('vehicles/list', [
            'title'    => 'Gestion des véhicules',
            'vehicles' => $vehicles,
            'total'    => $total,
            'page'     => $page,
            'pages'    => (int)ceil($total / ADMIN_PER_PAGE),
            'search'   => $search,
            'flash'    => $this->getFlash(),
        ]);
    }

    public function add($param = null): void {
        $db = Database::getInstance();
        $this->renderAdmin('vehicles/form', [
            'title'        => 'Ajouter un véhicule',
            'vehicle'      => null,
            'images'       => [],
            'brands'       => $this->brandModel->getAll(false),
            'fuel_types'   => $db->query("SELECT * FROM fuel_types ORDER BY name")->fetchAll(),
            'transmissions'=> $db->query("SELECT * FROM transmissions ORDER BY name")->fetchAll(),
            'body_types'   => $db->query("SELECT * FROM body_types ORDER BY name")->fetchAll(),
            'csrf'         => $this->csrfToken(),
            'flash'        => $this->getFlash(),
        ]);
    }

    public function create($param = null): void {
        if (!$this->isPost() || !$this->verifyCsrf()) {
            $this->redirect('/admin/vehicules/add'); return;
        }
        $data = $this->extractVehicleData();

        // Génération du slug
        $db = Database::getInstance();
        $brand = $db->query("SELECT name FROM brands WHERE id=" . (int)$data['brand_id'])->fetchColumn();
        $baseSlug = Vehicle::generateSlug($brand, $data['model'], $data['version'] ?? '', (int)$data['year']);
        $data['slug'] = $this->makeUniqueSlug($baseSlug);

        $id = $this->model->create($data);

        // Upload images
        if (!empty($_FILES['main_image']['name'])) {
            $this->handleMainImageUpload($id);
        }
        if (!empty($_FILES['gallery_images']['name'][0])) {
            $this->handleGalleryUploads($id);
        }

        $this->flashMessage('success', 'Véhicule ajouté avec succès.');
        $this->redirect('/admin/vehicules/edit/' . $id);
    }

    public function edit($id = null): void {
        $vehicle = $this->model->getById((int)$id);
        if (!$vehicle) { $this->redirect('/admin/vehicules'); return; }
        $db = Database::getInstance();
        $this->renderAdmin('vehicles/form', [
            'title'        => 'Modifier : ' . ($vehicle['brand_name'] ?? '') . ' ' . $vehicle['model'],
            'vehicle'      => $vehicle,
            'images'       => $this->model->getImages((int)$id),
            'brands'       => $this->brandModel->getAll(false),
            'fuel_types'   => $db->query("SELECT * FROM fuel_types ORDER BY name")->fetchAll(),
            'transmissions'=> $db->query("SELECT * FROM transmissions ORDER BY name")->fetchAll(),
            'body_types'   => $db->query("SELECT * FROM body_types ORDER BY name")->fetchAll(),
            'csrf'         => $this->csrfToken(),
            'flash'        => $this->getFlash(),
        ]);
    }

    public function update($id = null): void {
        if (!$this->isPost() || !$this->verifyCsrf()) {
            $this->redirect('/admin/vehicules/edit/' . $id); return;
        }
        $data = $this->extractVehicleData();

        // Slug
        $existing = $this->model->getById((int)$id);
        if ($existing) {
            $db = Database::getInstance();
            $brand = $db->query("SELECT name FROM brands WHERE id=" . (int)$data['brand_id'])->fetchColumn();
            $baseSlug = Vehicle::generateSlug($brand, $data['model'], $data['version'] ?? '', (int)$data['year']);
            if ($baseSlug !== explode('-', $existing['slug'])[0] . '-' . $data['year']) {
                $data['slug'] = $this->makeUniqueSlug($baseSlug, (int)$id);
            } else {
                $data['slug'] = $existing['slug'];
            }
        }

        $this->model->update((int)$id, $data);

        // Image principale séparée
        if (!empty($_FILES['main_image']['name'])) {
            $this->handleMainImageUpload((int)$id);
        }

        // Photos de galerie supplémentaires
        if (!empty($_FILES['gallery_images']['name'][0])) {
            $this->handleGalleryUploads((int)$id);
        }

        // Changer l'image principale parmi les existantes
        if (!empty($_POST['set_main_image'])) {
            $this->model->setMainImage((int)$id, (int)$_POST['set_main_image']);
        }

        $this->flashMessage('success', 'Véhicule mis à jour avec succès.');
        $this->redirect('/admin/vehicules/edit/' . $id);
    }

    public function delete($id = null): void {
        $vehicle = $this->model->getById((int)$id);
        if ($vehicle) {
            // Supprimer les fichiers images
            foreach ($this->model->getImages((int)$id) as $img) {
                $path = UPLOADS_PATH . '/vehicles/' . $img['filename'];
                if (file_exists($path)) @unlink($path);
            }
            $this->model->delete((int)$id);
            $this->flashMessage('success', 'Véhicule supprimé.');
        }
        $this->redirect('/admin/vehicules');
    }

    public function status($id = null): void {
        if (!$this->isPost()) { $this->redirect('/admin/vehicules'); return; }
        $status = $this->sanitize($_POST['status'] ?? 'draft');
        $this->model->updateStatus((int)$id, $status);
        $this->json(['success' => true]);
    }

    public function featured($id = null): void {
        $this->model->toggleFeatured((int)$id);
        $this->redirect('/admin/vehicules');
    }

    public function deleteImage($id = null): void {
        $filename = $this->model->deleteImage((int)$id);
        if ($filename) {
            $path = UPLOADS_PATH . '/vehicles/' . $filename;
            if (file_exists($path)) @unlink($path);
        }
        $vehicleId = $_GET['vehicle_id'] ?? '';
        if ($vehicleId) {
            $this->redirect('/admin/vehicules/edit/' . $vehicleId);
        } else {
            $this->redirect('/admin/vehicules');
        }
    }

    // ------------------------------------------------------------------
    private function extractVehicleData(): array {
        return [
            'brand_id'           => (int)($_POST['brand_id']             ?? 0),
            'model'              => $this->sanitize($_POST['model']       ?? ''),
            'version'            => $this->sanitize($_POST['version']     ?? ''),
            'year'               => (int)($_POST['year']                  ?? date('Y')),
            'mileage'            => (int)($_POST['mileage']               ?? 0),
            'fuel_type_id'       => (int)($_POST['fuel_type_id']          ?? 0),
            'transmission_id'    => (int)($_POST['transmission_id']       ?? 0),
            'body_type_id'       => (int)($_POST['body_type_id']          ?? 0) ?: null,
            'color'              => $this->sanitize($_POST['color']        ?? ''),
            'doors'              => !empty($_POST['doors'])   ? (int)$_POST['doors']   : null,
            'seats'              => !empty($_POST['seats'])   ? (int)$_POST['seats']   : null,
            'power_hp'           => !empty($_POST['power_hp']) ? (int)$_POST['power_hp'] : null,
            'power_kw'           => !empty($_POST['power_kw']) ? (int)$_POST['power_kw'] : null,
            'engine_cc'          => !empty($_POST['engine_cc']) ? (int)$_POST['engine_cc'] : null,
            'co2_emission'       => !empty($_POST['co2_emission']) ? (int)$_POST['co2_emission'] : null,
            'energy_rating'      => $this->sanitize($_POST['energy_rating'] ?? ''),
            'price'              => (float)($_POST['price']               ?? 0),
            'price_promo'        => !empty($_POST['price_promo']) ? (float)$_POST['price_promo'] : null,
            'is_featured'        => isset($_POST['is_featured']),
            'is_new_badge'       => isset($_POST['is_new_badge']),
            'status'             => $this->sanitize($_POST['status']      ?? 'draft'),
            'condition_state'    => $this->sanitize($_POST['condition_state'] ?? 'good'),
            'description'        => strip_tags($_POST['description']      ?? '', '<br><p><ul><li><strong><em>'),
            'has_service_history'=> isset($_POST['has_service_history']),
            'has_warranty'       => isset($_POST['has_warranty']),
            'warranty_months'    => !empty($_POST['warranty_months']) ? (int)$_POST['warranty_months'] : null,
            'has_control_tech'   => isset($_POST['has_control_tech']),
            'equipment'          => isset($_POST['equipment']) ? (array)$_POST['equipment'] : [],
            'meta_title'         => $this->sanitize($_POST['meta_title']       ?? ''),
            'meta_description'   => $this->sanitize($_POST['meta_description'] ?? ''),
        ];
    }

    private function handleMainImageUpload(int $vehicleId): void {
        if ($_FILES['main_image']['error'] !== UPLOAD_ERR_OK) return;
        $filename = $this->uploadImage($_FILES['main_image'], 'vehicles');
        if ($filename) {
            // Désactiver l'image principale existante
            $this->model->addImage($vehicleId, $filename, $_FILES['main_image']['name'], true, 0);
        }
    }

    private function handleGalleryUploads(int $vehicleId): void {
        $existingCount = count($this->model->getImages($vehicleId));
        foreach ($_FILES['gallery_images']['name'] as $i => $name) {
            if ($_FILES['gallery_images']['error'][$i] !== UPLOAD_ERR_OK) continue;
            $file = [
                'name'     => $name,
                'type'     => $_FILES['gallery_images']['type'][$i],
                'tmp_name' => $_FILES['gallery_images']['tmp_name'][$i],
                'error'    => $_FILES['gallery_images']['error'][$i],
                'size'     => $_FILES['gallery_images']['size'][$i],
            ];
            $filename = $this->uploadImage($file, 'vehicles');
            if ($filename) {
                // is_main = false car c'est la galerie
                $this->model->addImage($vehicleId, $filename, $name, false, $existingCount + $i + 1);
            }
        }
    }

    private function handleImageUploads(int $vehicleId, bool $firstAsMain): void {
        $isFirst = $firstAsMain;
        foreach ($_FILES['images']['name'] as $i => $name) {
            if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) continue;
            $file = [
                'name'     => $name,
                'type'     => $_FILES['images']['type'][$i],
                'tmp_name' => $_FILES['images']['tmp_name'][$i],
                'error'    => $_FILES['images']['error'][$i],
                'size'     => $_FILES['images']['size'][$i],
            ];
            $filename = $this->uploadImage($file, 'vehicles');
            if ($filename) {
                $this->model->addImage($vehicleId, $filename, $name, $isFirst, $i);
                $isFirst = false;
            }
        }
    }

    private function makeUniqueSlug(string $base, int $excludeId = 0): string {
        $db   = Database::getInstance();
        $slug = $base;
        $i    = 1;
        while (true) {
            $q = "SELECT id FROM vehicles WHERE slug=:s" . ($excludeId ? " AND id!=:id" : "");
            $s = $db->prepare($q);
            $s->bindValue(':s', $slug);
            if ($excludeId) $s->bindValue(':id', $excludeId, PDO::PARAM_INT);
            $s->execute();
            if (!$s->fetchColumn()) break;
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
