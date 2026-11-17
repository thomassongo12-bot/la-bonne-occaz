<?php
class SiteSettings {
    private PDO $db;
    private static array $cache = [];

    public function __construct() { $this->db = Database::getInstance(); }

    public function get(string $key, string $default = ''): string {
        if (isset(self::$cache[$key])) return self::$cache[$key];
        $s = $this->db->prepare("SELECT setting_val FROM site_settings WHERE setting_key=:k");
        $s->execute([':k'=>$key]);
        $v = $s->fetchColumn();
        self::$cache[$key] = ($v !== false) ? (string)$v : $default;
        return self::$cache[$key];
    }

    public function getAll(): array {
        return $this->db->query("SELECT * FROM site_settings ORDER BY group_name, setting_key")->fetchAll();
    }

    public function getByGroup(string $group): array {
        $s = $this->db->prepare("SELECT * FROM site_settings WHERE group_name=:g ORDER BY setting_key");
        $s->execute([':g'=>$group]);
        return $s->fetchAll();
    }

    public function set(string $key, string $value): bool {
        $s = $this->db->prepare("UPDATE site_settings SET setting_val=:v WHERE setting_key=:k");
        $r = $s->execute([':v'=>$value,':k'=>$key]);
        self::$cache[$key] = $value;
        return $r;
    }

    public function saveAll(array $data): void {
        foreach ($data as $key => $value) {
            $this->set($key, (string)$value);
        }
    }
}
