<?php
/**
 * Contrôleur de base - La Bonne Occaz
 */
abstract class BaseController {

    protected function render(string $view, array $data = [], string $layout = 'default'): void {
        extract($data);
        // Chargement des settings globaux
        try {
            $settings = new SiteSettings();
        } catch (Exception $e) {
            $settings = null;
        }
        $viewFile = VIEWS_PATH . '/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewFile)) {
            die("Vue introuvable : $viewFile");
        }
        if ($layout === 'none') {
            require $viewFile;
        } else {
            require VIEWS_PATH . '/layouts/header.php';
            require $viewFile;
            require VIEWS_PATH . '/layouts/footer.php';
        }
    }

    protected function renderAdmin(string $view, array $data = []): void {
        extract($data);
        $viewFile = VIEWS_PATH . '/admin/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewFile)) {
            die("Vue admin introuvable : $viewFile");
        }
        require VIEWS_PATH . '/admin/layouts/header.php';
        require $viewFile;
        require VIEWS_PATH . '/admin/layouts/footer.php';
    }

    protected function redirect(string $url): void {
        header('Location: ' . BASE_URL . $url);
        exit;
    }

    protected function redirectAbsolute(string $url): void {
        header('Location: ' . $url);
        exit;
    }

    protected function json(mixed $data, int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function sanitize(string $input): string {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }

    protected function isPost(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isAjax(): bool {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    protected function csrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function verifyCsrf(): bool {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }

    protected function flashMessage(string $type, string $message): void {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    protected function getFlash(): ?array {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }

    /** Upload d'image - retourne le nom du fichier ou false */
    protected function uploadImage(array $file, string $subfolder = 'vehicles'): string|false {
        if ($file['error'] !== UPLOAD_ERR_OK) return false;
        if ($file['size'] > MAX_FILE_SIZE) return false;

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ALLOWED_EXTENSIONS)) return false;

        $dir = UPLOADS_PATH . '/' . $subfolder . '/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $filename = uniqid('vehicle_', true) . '.' . $ext;
        if (move_uploaded_file($file['tmp_name'], $dir . $filename)) {
            return $filename;
        }
        return false;
    }
}
