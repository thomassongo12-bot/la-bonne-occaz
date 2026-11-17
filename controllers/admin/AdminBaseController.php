<?php
require_once ROOT_PATH . '/controllers/BaseController.php';

abstract class AdminBaseController extends BaseController {

    // Délai max d'inactivité session admin (30 min)
    private const SESSION_TIMEOUT = 1800;

    public function __construct() {
        $this->requireAuth();
        $this->checkSessionTimeout();
        $this->regenerateSessionPeriodically();
        $this->setSecurityHeaders();
    }

    // ── Authentification ────────────────────────────────────────────
    protected function requireAuth(): void {
        if (empty($_SESSION['admin_id'])) {
            $this->flashMessage('error', 'Connexion requise.');
            $this->redirect('/admin');
            exit;
        }
        // Vérifier que l'admin existe toujours en BDD
        try {
            $adminModel = new Admin();
            $admin = $adminModel->getById((int)$_SESSION['admin_id']);
            if (!$admin || !$admin['is_active']) {
                $this->forceLogout('Votre compte a été désactivé.');
            }
        } catch (Exception $e) { /* BDD indisponible — on laisse passer */ }
    }

    // ── Timeout de session ───────────────────────────────────────────
    private function checkSessionTimeout(): void {
        $lastActivity = $_SESSION['_last_activity'] ?? time();
        if (time() - $lastActivity > self::SESSION_TIMEOUT) {
            $this->forceLogout('Session expirée. Reconnectez-vous.');
        }
        $_SESSION['_last_activity'] = time();
    }

    // ── Régénération périodique de l'ID de session (anti-fixation) ──
    private function regenerateSessionPeriodically(): void {
        $lastRegen = $_SESSION['_last_regen'] ?? 0;
        if (time() - $lastRegen > 300) { // toutes les 5 min
            session_regenerate_id(true);
            $_SESSION['_last_regen'] = time();
        }
    }

    // ── En-têtes de sécurité ─────────────────────────────────────────
    private function setSecurityHeaders(): void {
        if (!headers_sent()) {
            header('X-Frame-Options: DENY');
            header('X-Content-Type-Options: nosniff');
            header('X-XSS-Protection: 1; mode=block');
            header('Referrer-Policy: strict-origin-when-cross-origin');
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');
        }
    }

    // ── Rôles ────────────────────────────────────────────────────────
    protected function requireRole(string $role): void {
        $roles = ['editor' => 1, 'admin' => 2, 'super_admin' => 3];
        $current  = $roles[$_SESSION['admin_role'] ?? 'editor'] ?? 1;
        $required = $roles[$role] ?? 1;
        if ($current < $required) {
            $this->flashMessage('error', 'Accès refusé. Droits insuffisants.');
            $this->redirect('/admin/dashboard');
            exit;
        }
    }

    // ── Déconnexion forcée ───────────────────────────────────────────
    protected function forceLogout(string $reason = ''): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        if ($reason) {
            session_start();
            $_SESSION['flash'] = ['type' => 'error', 'message' => $reason];
        }
        header('Location: ' . BASE_URL . '/admin');
        exit;
    }

    protected function renderAdmin(string $view, array $data = []): void {
        extract($data);
        $adminName = $_SESSION['admin_name'] ?? 'Admin';
        $adminRole = $_SESSION['admin_role'] ?? 'admin';
        $viewFile  = ROOT_PATH . '/views/admin/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewFile)) die("Vue admin introuvable : $viewFile");
        require ROOT_PATH . '/views/admin/layouts/header.php';
        require $viewFile;
        require ROOT_PATH . '/views/admin/layouts/footer.php';
    }
}
