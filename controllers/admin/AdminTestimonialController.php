<?php
require_once ROOT_PATH . '/controllers/admin/AdminBaseController.php';

class AdminTestimonialController extends AdminBaseController {

    private Testimonial $model;
    public function __construct() { parent::__construct(); $this->model = new Testimonial(); }

    public function index($param = null): void {
        $page  = max(1, (int)($_GET['page'] ?? 1));
        $items = $this->model->getAll($page, ADMIN_PER_PAGE);
        $this->renderAdmin('testimonials/list', [
            'title' => 'Gestion des témoignages',
            'items' => $items,
            'flash' => $this->getFlash(),
        ]);
    }

    public function add($param = null): void {
        $this->renderAdmin('testimonials/form', [
            'title' => 'Ajouter un témoignage',
            'item'  => null,
            'flash' => $this->getFlash(),
            'csrf'  => $this->csrfToken(),
        ]);
    }

    public function create($param = null): void {
        if (!$this->isPost() || !$this->verifyCsrf()) { $this->redirect('/admin/temoignages'); return; }
        $data = $this->extractData();
        $data['photo_url'] = $this->handlePhotoUpload(null);
        $this->model->create($data);
        $this->flashMessage('success', 'Témoignage ajouté.');
        $this->redirect('/admin/temoignages');
    }

    public function edit($id = null): void {
        $item = $this->model->getById((int)$id);
        if (!$item) { $this->redirect('/admin/temoignages'); return; }
        $this->renderAdmin('testimonials/form', [
            'title' => 'Modifier le témoignage',
            'item'  => $item,
            'flash' => $this->getFlash(),
            'csrf'  => $this->csrfToken(),
        ]);
    }

    public function update($id = null): void {
        if (!$this->isPost() || !$this->verifyCsrf()) { $this->redirect('/admin/temoignages'); return; }
        $item = $this->model->getById((int)$id);
        $data = $this->extractData();

        // Gestion photo
        $newPhoto = $this->handlePhotoUpload($item['photo_url'] ?? null);
        $data['photo_url'] = $newPhoto;

        // Suppression demandée
        if (!empty($_POST['remove_photo']) && $item['photo_url']) {
            $this->deletePhotoFile($item['photo_url']);
            $data['photo_url'] = null;
        }

        $this->model->update((int)$id, $data);
        $this->flashMessage('success', 'Témoignage modifié.');
        $this->redirect('/admin/temoignages');
    }

    public function delete($id = null): void {
        $item = $this->model->getById((int)$id);
        if ($item && !empty($item['photo_url'])) {
            $this->deletePhotoFile($item['photo_url']);
        }
        $this->model->delete((int)$id);
        $this->flashMessage('success', 'Témoignage supprimé.');
        $this->redirect('/admin/temoignages');
    }

    public function toggle($id = null): void {
        $this->model->toggle((int)$id);
        $this->redirect('/admin/temoignages');
    }

    // ------------------------------------------------------------------
    private function extractData(): array {
        return [
            'author'      => $this->sanitize($_POST['author']      ?? ''),
            'location'    => $this->sanitize($_POST['location']     ?? ''),
            'rating'      => min(5, max(1, (int)($_POST['rating']   ?? 5))),
            'content'     => $this->sanitize($_POST['content']      ?? ''),
            'vehicle_ref' => $this->sanitize($_POST['vehicle_ref']  ?? ''),
            'is_active'   => isset($_POST['is_active']),
            'sort_order'  => (int)($_POST['sort_order']             ?? 0),
        ];
    }

    private function handlePhotoUpload(?string $existing): ?string {
        if (empty($_FILES['photo']['name']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            return $existing; // rien uploadé → garder l'existante
        }
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','webp'])) return $existing;
        if ($_FILES['photo']['size'] > 2 * 1024 * 1024) return $existing;

        $dir = UPLOADS_PATH . '/testimonials/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $filename = 'testimonial_' . uniqid() . '.' . $ext;
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $dir . $filename)) return $existing;

        // Supprimer l'ancienne photo
        if ($existing) $this->deletePhotoFile($existing);

        return UPLOADS_URL . '/testimonials/' . $filename;
    }

    private function deletePhotoFile(string $url): void {
        $filename = basename($url);
        $path = UPLOADS_PATH . '/testimonials/' . $filename;
        if (file_exists($path)) @unlink($path);
    }
}
