<?php
require_once ROOT_PATH . '/controllers/BaseController.php';

class SellingController extends BaseController {

    public function index($param = null): void {
        $db = Database::getInstance();
        $fuel_types    = $db->query("SELECT * FROM fuel_types ORDER BY name")->fetchAll();
        $transmissions = $db->query("SELECT * FROM transmissions ORDER BY name")->fetchAll();
        $brands        = (new Brand())->getAll();

        $this->render('selling/index', [
            'title'        => 'Vendre / Déposer mon véhicule - La Bonne Occaz',
            'metaDesc'     => 'Proposez votre véhicule en dépôt-vente ou vendez-le directement à La Bonne Occaz.',
            'fuel_types'   => $fuel_types,
            'transmissions'=> $transmissions,
            'brands'       => $brands,
            'flash'        => $this->getFlash(),
            'csrf'         => $this->csrfToken(),
        ]);
    }

    public function submit($param = null): void {
        if (!$this->isPost()) { $this->redirect('/vendre'); return; }
        if (!$this->verifyCsrf()) {
            $this->flashMessage('error', 'Token de sécurité invalide.');
            $this->redirect('/vendre');
            return;
        }

        $errors = $this->validateForm();
        if ($errors) {
            $this->flashMessage('error', implode(' ', $errors));
            $this->redirect('/vendre');
            return;
        }

        $data = [
            'first_name'      => $this->sanitize($_POST['first_name']),
            'last_name'       => $this->sanitize($_POST['last_name']),
            'email'           => filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL),
            'phone'           => $this->sanitize($_POST['phone']),
            'city'            => $this->sanitize($_POST['city'] ?? ''),
            'brand'           => $this->sanitize($_POST['brand']),
            'model'           => $this->sanitize($_POST['model']),
            'version'         => $this->sanitize($_POST['version']          ?? ''),
            'year'            => (int)($_POST['year']                       ?? 0),
            'mileage'         => (int)($_POST['mileage']                    ?? 0),
            'fuel_type'       => $this->sanitize($_POST['fuel_type']),
            'transmission'    => $this->sanitize($_POST['transmission']),
            'color'           => $this->sanitize($_POST['color']            ?? ''),
            'condition_state' => $this->sanitize($_POST['condition_state']  ?? ''),
            'desired_price'   => !empty($_POST['desired_price']) ? (float)$_POST['desired_price'] : null,
            'description'     => $this->sanitize($_POST['description']      ?? ''),
        ];

        $model  = new SellingRequest();
        $reqId  = $model->create($data);

        // Gestion des photos
        $uploadedFiles = [];
        if (!empty($_FILES['photos']['name'][0])) {
            foreach ($_FILES['photos']['name'] as $i => $name) {
                if ($_FILES['photos']['error'][$i] === UPLOAD_ERR_OK) {
                    $file = [
                        'name'     => $_FILES['photos']['name'][$i],
                        'type'     => $_FILES['photos']['type'][$i],
                        'tmp_name' => $_FILES['photos']['tmp_name'][$i],
                        'error'    => $_FILES['photos']['error'][$i],
                        'size'     => $_FILES['photos']['size'][$i],
                    ];
                    $fn = $this->uploadImage($file, 'selling');
                    if ($fn) $uploadedFiles[] = $fn;
                }
            }
            if ($uploadedFiles) $model->addImages($reqId, $uploadedFiles);
        }

        // Notification admin
        $this->notifyAdmin($data, $reqId);

        $this->flashMessage('success', 'Votre demande a bien été enregistrée ! Notre équipe vous contactera sous 48h pour évaluer votre véhicule.');
        $this->redirect('/vendre');
    }

    private function validateForm(): array {
        $errors = [];
        if (empty($_POST['first_name'])) $errors[] = 'Le prénom est requis.';
        if (empty($_POST['last_name']))  $errors[] = 'Le nom est requis.';
        if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide.';
        if (empty($_POST['phone']))      $errors[] = 'Le téléphone est requis.';
        if (empty($_POST['city']))       $errors[] = 'La ville est requise.';
        if (empty($_POST['brand']))      $errors[] = 'La marque est requise.';
        if (empty($_POST['model']))      $errors[] = 'Le modèle est requis.';
        if (empty($_POST['year']) || (int)$_POST['year'] < 1990 || (int)$_POST['year'] > (int)date('Y') + 1)
            $errors[] = 'Année invalide.';
        if (!isset($_POST['mileage']) || (int)$_POST['mileage'] < 0) $errors[] = 'Kilométrage invalide.';
        if (empty($_POST['fuel_type']))      $errors[] = 'Le carburant est requis.';
        if (empty($_POST['transmission']))   $errors[] = 'La transmission est requise.';
        return $errors;
    }

    private function notifyAdmin(array $d, int $id): void {
        $to      = cfg_email();
        $subject = "Nouvelle demande de vente/dépôt #$id - La Bonne Occaz";
        $body    = "Nouvelle demande de dépôt-vente :\n\n"
            . "Client : {$d['first_name']} {$d['last_name']}\n"
            . "Email  : {$d['email']}\nTéléphone : {$d['phone']}\nVille : " . ($d['city'] ?? 'Non renseignée') . "\n\n"
            . "Véhicule : {$d['brand']} {$d['model']} {$d['version']} ({$d['year']})\n"
            . "Kilométrage : {$d['mileage']} km\nCarburant : {$d['fuel_type']}\n"
            . "Prix souhaité : " . ($d['desired_price'] ? number_format($d['desired_price'],0,',',' ') . ' €' : 'Non renseigné') . "\n\n"
            . "Voir dans l'administration : " . BASE_URL . "/admin/demandes";
        @mail($to, $subject, $body, "From: noreply@labonneoccaz.fr");
    }
}
