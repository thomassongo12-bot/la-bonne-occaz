<?php
require_once ROOT_PATH . '/controllers/admin/AdminBaseController.php';

class AdminReservationController extends AdminBaseController {

    private Reservation $model;
    public function __construct() { parent::__construct(); $this->model = new Reservation(); }

    public function index($param = null): void {
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $status = $_GET['status'] ?? '';

        $this->renderAdmin('reservations/list', [
            'title'  => 'Réservations',
            'items'  => $this->model->getAll($page, ADMIN_PER_PAGE, $status),
            'total'  => $this->model->countAll($status),
            'page'   => $page,
            'pages'  => (int)ceil($this->model->countAll($status) / ADMIN_PER_PAGE),
            'status' => $status,
            'flash'  => $this->getFlash(),
        ]);
    }

    public function view($id = null): void {
        $item = $this->model->getById((int)$id);
        if (!$item) { $this->redirect('/admin/reservations'); return; }
        if ($item['status'] === 'new') $this->model->updateStatus((int)$id, 'confirmed');
        $this->renderAdmin('reservations/view', [
            'title' => 'Réservation #' . $id,
            'item'  => $item,
            'csrf'  => $this->csrfToken(),
            'flash' => $this->getFlash(),
        ]);
    }

    public function updateStatus($id = null): void {
        if (!$this->isPost()) { $this->redirect('/admin/reservations'); return; }
        $status = $this->sanitize($_POST['status'] ?? '');
        $note   = $this->sanitize($_POST['admin_note'] ?? '');
        $this->model->updateStatus((int)$id, $status, $note);
        $this->flashMessage('success', 'Statut mis à jour.');
        $this->redirect('/admin/reservations/view/' . $id);
    }
}
