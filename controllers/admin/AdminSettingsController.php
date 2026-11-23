<?php
require_once ROOT_PATH . '/controllers/admin/AdminBaseController.php';

class AdminSettingsController extends AdminBaseController {

    public function index($param = null): void {
        $model  = new SiteSettings();
        $groups = [];
        foreach ($model->getAll() as $s) {
            $groups[$s['group_name']][] = $s;
        }
        $this->renderAdmin('settings/index', [
            'title'  => 'Paramètres du site',
            'groups' => $groups,
            'flash'  => $this->getFlash(),
            'csrf'   => $this->csrfToken(),
        ]);
    }

    public function save($param = null): void {
        if (!$this->isPost() || !$this->verifyCsrf()) {
            $this->redirect('/admin/parametres');
            return;
        }

        $model   = new SiteSettings();
        $allowed = $model->getAll();

        foreach ($allowed as $s) {
            $key  = $s['setting_key'];
            $type = $s['setting_type'];

            // Champs image : traitement upload
            if ($type === 'image') {
                $fileKey = 'upload_' . $key;

                if (!empty($_FILES[$fileKey]['name']) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
                    $uploaded = $this->uploadBranding($_FILES[$fileKey], $key);
                    if ($uploaded) {
                        // Supprimer l'ancien fichier si existant
                        $old = $model->get($key);
                        if ($old && $old !== $uploaded) {
                            $oldPath = UPLOADS_PATH . '/branding/' . basename($old);
                            if (file_exists($oldPath)) @unlink($oldPath);
                        }
                        $model->set($key, $uploaded);
                    } else {
                        $this->flashMessage('error', 'Erreur upload pour ' . $key . '. Vérifiez le format (JPG, PNG, SVG, ICO) et la taille (max 2 Mo).');
                        $this->redirect('/admin/parametres');
                        return;
                    }
                } elseif (!empty($_FILES[$fileKey]['error']) && $_FILES[$fileKey]['error'] !== UPLOAD_ERR_NO_FILE) {
                    // Erreur PHP upload (quota, taille serveur, etc.)
                    $phpErrors = [
                        UPLOAD_ERR_INI_SIZE   => 'Fichier trop lourd (limite serveur PHP).',
                        UPLOAD_ERR_FORM_SIZE  => 'Fichier trop lourd (limite formulaire).',
                        UPLOAD_ERR_PARTIAL    => 'Upload interrompu, réessayez.',
                        UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire PHP introuvable.',
                        UPLOAD_ERR_CANT_WRITE => 'Impossible d\'écrire le fichier (permissions serveur).',
                        UPLOAD_ERR_EXTENSION  => 'Extension PHP bloquée.',
                    ];
                    $msg = $phpErrors[$_FILES[$fileKey]['error']] ?? 'Erreur inconnue (code ' . $_FILES[$fileKey]['error'] . ').';
                    $this->flashMessage('error', 'Upload ' . $key . ' : ' . $msg);
                    $this->redirect('/admin/parametres');
                    return;
                } elseif (isset($_POST['remove_' . $key]) && $_POST['remove_' . $key] === '1') {
                    // Suppression demandée
                    $old = $model->get($key);
                    if ($old) {
                        $oldPath = UPLOADS_PATH . '/branding/' . basename($old);
                        if (file_exists($oldPath)) @unlink($oldPath);
                    }
                    $model->set($key, '');
                }
                // Si ni upload ni suppression → on ne touche pas la valeur
                continue;
            }

            // Champs texte/textarea/boolean classiques
            if (isset($_POST[$key])) {
                // strip_tags uniquement, PAS htmlspecialchars — les valeurs sont
                // stockées en texte brut et encodées à l'affichage côté vue
                $value = strip_tags(trim($_POST[$key]));
                $model->set($key, $value);
            }
        }

        $this->flashMessage('success', 'Paramètres enregistrés avec succès.');
        $this->redirect('/admin/parametres');
    }

    public function password($param = null): void {
        if (!$this->isPost()) { $this->redirect('/admin/parametres'); return; }

        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password']     ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        $adminModel = new Admin();
        $admin      = $adminModel->getById((int)$_SESSION['admin_id']);

        if (!password_verify($current, $admin['password'])) {
            $this->flashMessage('error', 'Mot de passe actuel incorrect.');
        } elseif (strlen($new) < 8) {
            $this->flashMessage('error', 'Le nouveau mot de passe doit comporter au moins 8 caractères.');
        } elseif ($new !== $confirm) {
            $this->flashMessage('error', 'Les mots de passe ne correspondent pas.');
        } else {
            $adminModel->updatePassword((int)$_SESSION['admin_id'], $new);
            $this->flashMessage('success', 'Mot de passe modifié avec succès.');
        }
        $this->redirect('/admin/parametres');
    }

    // ------------------------------------------------------------------
    // Upload logo / favicon
    // ------------------------------------------------------------------
    private function uploadBranding(array $file, string $key): string|false {
        $maxSize = 2 * 1024 * 1024; // 2 Mo
        if ($file['size'] > $maxSize) return false;

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        // Extensions autorisées selon le type
        $allowed = $key === 'favicon_url'
            ? ['ico', 'png', 'svg']
            : ['jpg', 'jpeg', 'png', 'svg', 'webp', 'gif'];

        if (!in_array($ext, $allowed)) return false;

        // Vérification MIME (sécurité)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowedMimes = [
            'image/jpeg', 'image/png', 'image/svg+xml',
            'image/webp', 'image/gif', 'image/x-icon',
            'image/vnd.microsoft.icon',
        ];
        if (!in_array($mime, $allowedMimes)) return false;

        $dir = UPLOADS_PATH . '/branding/';

        // Créer le dossier si absent, avec permissions larges
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) return false;
        }

        // Vérifier que le dossier est accessible en écriture
        if (!is_writable($dir)) {
            @chmod($dir, 0755);
            if (!is_writable($dir)) return false;
        }

        // Nom fixe selon la clé pour faciliter la mise à jour
        $filename = $key . '.' . $ext;
        $dest     = $dir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) return false;

        return UPLOADS_URL . '/branding/' . $filename;
    }
}
