<?php
require_once ROOT_PATH . '/controllers/BaseController.php';

class ContactController extends BaseController {

    public function index($param = null): void {
        $settings = new SiteSettings();
        $this->render('contact/index', [
            'title'    => 'Contact - La Bonne Occaz',
            'metaDesc' => 'Contactez La Bonne Occaz pour toute question ou pour prendre rendez-vous.',
            'settings' => $settings,
            'flash'    => $this->getFlash(),
            'csrf'     => $this->csrfToken(),
        ]);
    }

    public function send($param = null): void {
        if (!$this->isPost()) { $this->redirect('/contact'); return; }
        if (!$this->verifyCsrf()) { $this->flashMessage('error', 'Token de sécurité invalide.'); $this->redirect('/contact'); return; }

        $errors = [];
        $fn     = $this->sanitize($_POST['first_name'] ?? '');
        $ln     = $this->sanitize($_POST['last_name']  ?? '');
        $email  = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $phone  = $this->sanitize($_POST['phone']   ?? '');
        $city   = $this->sanitize($_POST['city']    ?? '');
        $msg    = $this->sanitize($_POST['message'] ?? '');

        if (!$fn)    $errors[] = 'Le prénom est requis.';
        if (!$ln)    $errors[] = 'Le nom est requis.';
        if (!$email) $errors[] = 'Adresse e-mail invalide.';
        if (!$city)  $errors[] = 'La ville est requise.';
        if (strlen($msg) < 10) $errors[] = 'Le message doit comporter au moins 10 caractères.';

        if ($errors) {
            $this->flashMessage('error', implode(' ', $errors));
            $this->redirect('/contact');
            return;
        }

        $model = new ContactRequest();
        $model->create([
            'type'       => 'contact',
            'first_name' => $fn,
            'last_name'  => $ln,
            'email'      => $email,
            'phone'      => $phone,
            'message'    => ($city ? "Ville : $city\n\n" : '') . $msg,
        ]);

        $this->sendNotificationEmail($fn, $ln, $email, $phone, $city, $msg);

        $this->flashMessage('success', 'Votre message a bien été envoyé. Nous vous répondrons dans les plus brefs délais.');
        $this->redirect('/contact');
    }

    /** Formulaire de contact depuis une fiche véhicule */
    public function vehicle($param = null): void {
        if (!$this->isPost()) { $this->redirect('/vehicules'); return; }
        if (!$this->verifyCsrf()) { $this->flashMessage('error', 'Token invalide.'); $this->redirect('/vehicules'); return; }

        $vehicleId = (int)($_POST['vehicle_id'] ?? 0);
        $type      = $_POST['contact_type'] ?? 'vehicle_info';
        $fn        = $this->sanitize($_POST['first_name'] ?? '');
        $ln        = $this->sanitize($_POST['last_name']  ?? '');
        $email     = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $phone     = $this->sanitize($_POST['phone']   ?? '');
        $msg       = $this->sanitize($_POST['message'] ?? '');

        if (!$fn || !$ln || !$email || strlen($msg) < 5) {
            $this->flashMessage('error', 'Veuillez remplir tous les champs obligatoires.');
        } else {
            $model = new ContactRequest();
            $model->create([
                'type'       => $type,
                'vehicle_id' => $vehicleId ?: null,
                'first_name' => $fn,
                'last_name'  => $ln,
                'email'      => $email,
                'phone'      => $phone,
                'message'    => $msg,
            ]);
            $this->flashMessage('success', 'Votre demande a bien été enregistrée. Nous vous contacterons rapidement.');
        }

        // Retour sur la fiche véhicule
        $slug = $_POST['vehicle_slug'] ?? '';
        $this->redirect($slug ? '/vehicule/' . $slug : '/vehicules');
    }

    private function sendNotificationEmail(string $fn, string $ln, string $email, string $phone, string $city, string $msg): void {
        $to      = cfg_email();
        $subject = "Nouveau message - La Bonne Occaz";
        $body    = "Nouveau message reçu :\n\nNom : $fn $ln\nEmail : $email\nTéléphone : $phone\nVille : $city\n\nMessage :\n$msg";
        $headers = "From: noreply@labonneoccaz.fr\r\nReply-To: $email";
        @mail($to, $subject, $body, $headers);
    }
}
