<?php
require_once ROOT_PATH . '/controllers/BaseController.php';

class ReservationController extends BaseController {

    // Affiche la page de réservation avec conditions
    public function index($slug = null): void {
        if (!$slug) { $this->redirect('/vehicules'); return; }

        $vehicleModel = new Vehicle();
        $vehicle = $vehicleModel->getBySlug($slug);
        if (!$vehicle || $vehicle['status'] !== 'available') {
            $this->redirect('/vehicule/' . $slug);
            return;
        }

        $settings = new SiteSettings();
        $conditions         = $settings->get('reservation_conditions');
        $holdHours          = $settings->get('reservation_hold_hours', '72');
        $deliveryConditions = $settings->get('delivery_conditions', '');
        $images             = $vehicleModel->getImages($vehicle['id']);

        $this->render('reservation/index', [
            'title'      => 'Réserver — ' . ($vehicle['brand_name']??'') . ' ' . $vehicle['model'],
            'metaDesc'   => 'Réservez ce véhicule en ligne. Conditions de réservation claires et transparentes.',
            'vehicle'    => $vehicle,
            'images'     => $images,
            'conditions' => $conditions,
            'holdHours'  => $holdHours,
            'deliveryConditions' => $deliveryConditions,
            'flash'      => $this->getFlash(),
            'csrf'       => $this->csrfToken(),
        ]);
    }

    // Traite la soumission du formulaire
    public function submit($slug = null): void {
        if (!$this->isPost()) { $this->redirect('/vehicules'); return; }
        if (!$this->verifyCsrf()) {
            $this->flashMessage('error', 'Token de sécurité invalide.');
            $this->redirect('/reserver/' . $slug);
            return;
        }

        $vehicleModel = new Vehicle();
        $vehicle = $vehicleModel->getBySlug($slug ?? '');
        if (!$vehicle || $vehicle['status'] !== 'available') {
            $this->flashMessage('error', 'Ce véhicule n\'est plus disponible à la réservation.');
            $this->redirect('/vehicule/' . $slug);
            return;
        }

        // Validation
        $errors = $this->validate();
        if ($errors) {
            $this->flashMessage('error', implode(' ', $errors));
            $this->redirect('/reserver/' . $slug);
            return;
        }

        // Vérification conditions acceptées
        if (empty($_POST['conditions_accepted'])) {
            $this->flashMessage('error', 'Vous devez accepter les conditions de réservation pour continuer.');
            $this->redirect('/reserver/' . $slug);
            return;
        }
        if (empty($_POST['delivery_accepted'])) {
            $this->flashMessage('error', 'Vous devez accepter les conditions de livraison pour continuer.');
            $this->redirect('/reserver/' . $slug);
            return;
        }

        // Création de la réservation
        $model = new Reservation();
        $id = $model->create([
            'vehicle_id'          => $vehicle['id'],
            'first_name'          => $this->sanitize($_POST['first_name']),
            'last_name'           => $this->sanitize($_POST['last_name']),
            'email'               => filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL),
            'phone'               => $this->sanitize($_POST['phone']),
            'message'             => ($this->sanitize($_POST['city'] ?? '') ? 'Ville : ' . $this->sanitize($_POST['city']) . "\n\n" : '') . $this->sanitize($_POST['message'] ?? ''),
            'conditions_accepted' => true,
        ]);

        // Notification admin
        $this->notifyAdmin($vehicle, $id);
        // Confirmation client
        $this->confirmClient($vehicle);

        // Passer le véhicule en "réservé"
        $vehicleModel->updateStatus($vehicle['id'], 'reserved');

        $this->flashMessage('success',
            'Votre réservation a bien été enregistrée ! Nous vous contacterons dans les 24h pour confirmer les détails.'
        );
        $this->redirect('/vehicule/' . $slug);
    }

    // ── Validation ──────────────────────────────────────────────────
    private function validate(): array {
        $errors = [];
        if (empty($_POST['first_name'])) $errors[] = 'Le prénom est requis.';
        if (empty($_POST['last_name']))  $errors[] = 'Le nom est requis.';
        if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
            $errors[] = 'Adresse e-mail invalide.';
        if (empty($_POST['phone']))      $errors[] = 'Le téléphone est requis.';
        return $errors;
    }

    private function notifyAdmin(array $vehicle, int $reservationId): void {
        $html = Mailer::reservationAdmin($vehicle, $reservationId, $_POST);
        Mailer::send(
            cfg_email(),
            "Réservation #{$reservationId} — " . ($vehicle['brand_name'] ?? '') . ' ' . $vehicle['model'],
            $html
        );
    }

    private function confirmClient(array $vehicle): void {
        $to = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        if (!$to) return;
        $html = Mailer::reservationClient($vehicle, $_POST);
        Mailer::send(
            $to,
            "Confirmation de votre réservation — La Bonne Occaz",
            $html,
            cfg_email()
        );
    }
}
