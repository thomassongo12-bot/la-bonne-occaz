<?php
require_once ROOT_PATH . '/controllers/BaseController.php';

class AdminAuthController extends BaseController {

    public function index($param = null): void {
        // Si déjà connecté, rediriger vers dashboard
        if (!empty($_SESSION['admin_id'])) {
            $this->redirect('/admin/dashboard');
        }
        $this->render('admin/login', [
            'title' => 'Administration - Connexion',
            'flash' => $this->getFlash(),
            'csrf'  => $this->csrfToken(),
        ], 'none');
    }

    public function login($param = null): void {
        if (!$this->isPost()) { $this->redirect('/admin'); return; }
        if (!$this->verifyCsrf()) {
            $this->flashMessage('error','Token de sécurité invalide.');
            $this->redirect('/admin');
            return;
        }

        $username = $this->sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$username || !$password) {
            $this->flashMessage('error', 'Identifiants requis.');
            $this->redirect('/admin');
            return;
        }

        // Anti-brute force : max 5 tentatives par IP sur 15 min
        $ip      = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $key     = 'bf_' . md5($ip);
        $attempts = (int)($_SESSION[$key . '_count'] ?? 0);
        $lastTry  = (int)($_SESSION[$key . '_time']  ?? 0);

        // Reset si fenêtre expirée
        if (time() - $lastTry > 900) {
            $attempts = 0;
            $_SESSION[$key . '_count'] = 0;
        }

        if ($attempts >= 5) {
            $wait = max(0, 900 - (time() - $lastTry));
            $this->flashMessage('error', "Trop de tentatives. Réessayez dans " . ceil($wait/60) . " min.");
            $this->redirect('/admin');
            return;
        }

        $adminModel = new Admin();
        $admin      = $adminModel->verify($username, $password);

        if ($admin) {
            // Succès — reset compteur, nouvelle session
            unset($_SESSION[$key . '_count'], $_SESSION[$key . '_time']);
            session_regenerate_id(true);
            $_SESSION['admin_id']        = $admin['id'];
            $_SESSION['admin_role']      = $admin['role'];
            $_SESSION['admin_name']      = $admin['first_name'] . ' ' . $admin['last_name'];
            $_SESSION['_last_activity']  = time();
            $_SESSION['_last_regen']     = time();
            $_SESSION['_ip']             = md5($ip); // vérification IP
            $this->redirect('/admin/dashboard');
        } else {
            $_SESSION[$key . '_count'] = $attempts + 1;
            $_SESSION[$key . '_time']  = time();
            // Délai progressif anti-timing
            usleep(500000 + ($attempts * 200000));
            $this->flashMessage('error', 'Identifiant ou mot de passe incorrect.');
            $this->redirect('/admin');
        }
    }

    public function logout($param = null): void {
        $_SESSION = [];
        session_destroy();
        $this->redirect('/admin');
    }
}
