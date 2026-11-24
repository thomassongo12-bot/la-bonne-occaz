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
        $conditions      = $settings->get('reservation_conditions');
        $deposit         = $settings->get('reservation_deposit', '300');
        $holdHours       = $settings->get('reservation_hold_hours', '72');
        $deliveryConditions = $settings->get('delivery_conditions', '');
        $images          = $vehicleModel->getImages($vehicle['id']);

        $this->render('reservation/index', [
            'title'      => 'Réserver — ' . ($vehicle['brand_name']??'') . ' ' . $vehicle['model'],
            'metaDesc'   => 'Réservez ce véhicule en ligne. Conditions de réservation claires et transparentes.',
            'vehicle'    => $vehicle,
            'images'     => $images,
            'conditions' => $conditions,
            'deposit'    => $deposit,
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
            'message'             => $this->sanitize($_POST['message'] ?? ''),
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

    // ── Notifications ────────────────────────────────────────────────
    private function notifyAdmin(array $vehicle, int $reservationId): void {
        $to      = cfg_email();
        $subject = "🔒 Nouvelle réservation #{$reservationId} — " . ($vehicle['brand_name']??'') . ' ' . $vehicle['model'];
        $fn = $this->sanitize($_POST['first_name']);
        $ln = $this->sanitize($_POST['last_name']);
        $em = htmlspecialchars($_POST['email']);
        $ph = htmlspecialchars($_POST['phone']);
        $body =
            "Nouvelle réservation reçue :\n\n" .
            "Client : $fn $ln\nEmail : $em\nTéléphone : $ph\n\n" .
            "Véhicule : " . ($vehicle['brand_name']??'') . " " . $vehicle['model'] . " ({$vehicle['year']})\n" .
            "Prix : " . number_format($vehicle['price'],0,',',' ') . " €\n\n" .
            "Voir dans l'administration : " . BASE_URL . "/admin/reservations/view/{$reservationId}";
        @mail($to, $subject, $body, "From: noreply@labonneoccaz.fr\r\nContent-Type: text/plain; charset=UTF-8");
    }

    private function confirmClient(array $vehicle): void {
        $to      = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
        if (!$to) return;
        $fn      = $this->sanitize($_POST['first_name']);
        $subject = "Confirmation de votre réservation — La Bonne Occaz";
        $body    =
            "Bonjour {$fn},\n\n" .
            "Votre demande de réservation pour le véhicule suivant a bien été enregistrée :\n\n" .
            "→ " . ($vehicle['brand_name']??'') . " " . $vehicle['model'] . " (" . $vehicle['year'] . ")\n" .
            "→ Prix : " . number_format($vehicle['price'],0,',',' ') . " €\n\n" .
            "Notre équipe vous contactera dans les 24h pour confirmer les détails et vous communiquer les modalités de paiement de l'acompte.\n\n" .
            "Merci de votre confiance,\nL'équipe La Bonne Occaz\n" . cfg_phone();
        @mail($to, $subject, $body, "From: " . cfg_email() . "\r\nContent-Type: text/plain; charset=UTF-8");
    }
}
