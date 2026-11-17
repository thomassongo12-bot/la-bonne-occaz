<?php
require_once ROOT_PATH . '/controllers/admin/AdminBaseController.php';

class AdminRequestController extends AdminBaseController {

    private ContactRequest $contactModel;
    private SellingRequest $sellingModel;

    public function __construct() {
        parent::__construct();
        $this->contactModel = new ContactRequest();
        $this->sellingModel = new SellingRequest();
    }

    public function index($param = null): void {
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $type   = $_GET['type']   ?? '';
        $status = $_GET['status'] ?? '';

        $contacts = $this->contactModel->getAll($page, ADMIN_PER_PAGE, $type, $status);
        $total    = $this->contactModel->countAll($type, $status);
        $selling  = $this->sellingModel->getAll(1, 10);

        $this->renderAdmin('requests/list', [
            'title'    => 'Gestion des demandes',
            'contacts' => $contacts,
            'selling'  => $selling,
            'total'    => $total,
            'page'     => $page,
            'pages'    => (int)ceil($total / ADMIN_PER_PAGE),
            'type'     => $type,
            'status'   => $status,
            'flash'    => $this->getFlash(),
        ]);
    }

    public function view($id = null): void {
        $item = $this->contactModel->getById((int)$id);
        if (!$item) { $this->redirect('/admin/demandes'); return; }
        // Marquer comme en cours si new
        if ($item['status'] === 'new') {
            $this->contactModel->updateStatus((int)$id, 'processing');
        }
        $this->renderAdmin('requests/view', [
            'title' => 'Demande #' . $id,
            'item'  => $item,
            'csrf'  => $this->csrfToken(),
        ]);
    }

    public function updateStatus($id = null): void {
        if (!$this->isPost()) { $this->redirect('/admin/demandes'); return; }
        $status = $this->sanitize($_POST['status'] ?? '');
        $note   = $this->sanitize($_POST['admin_note'] ?? '');
        $this->contactModel->updateStatus((int)$id, $status, $note);
        $this->flashMessage('success', 'Statut mis à jour.');
        $this->redirect('/admin/demandes/view/' . $id);
    }

    public function viewSelling($id = null): void {
        $item = $this->sellingModel->getById((int)$id);
        if (!$item) { $this->redirect('/admin/demandes'); return; }
        if ($item['status'] === 'new') $this->sellingModel->updateStatus((int)$id, 'evaluating');
        $this->renderAdmin('requests/view_selling', [
            'title' => 'Dépôt-vente #' . $id,
            'item'  => $item,
            'csrf'  => $this->csrfToken(),
        ]);
    }

    public function updateSellingStatus($id = null): void {
        if (!$this->isPost()) { $this->redirect('/admin/demandes'); return; }
        $status = $this->sanitize($_POST['status'] ?? '');
        $note   = $this->sanitize($_POST['admin_note'] ?? '');
        $this->sellingModel->updateStatus((int)$id, $status, $note);
        $this->flashMessage('success', 'Statut mis à jour.');
        $this->redirect('/admin/demandes/viewSelling/' . $id);
    }
}
