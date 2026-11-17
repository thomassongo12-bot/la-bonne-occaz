<?php
class Brand {
    private PDO $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function getAll(bool $activeOnly = true): array {
        $where = $activeOnly ? "WHERE is_active=1" : "";
        return $this->db->query("SELECT * FROM brands $where ORDER BY sort_order, name")->fetchAll();
    }
    public function getById(int $id): ?array {
        $s = $this->db->prepare("SELECT * FROM brands WHERE id=:id");
        $s->execute([':id'=>$id]);
        return $s->fetch() ?: null;
    }
    public function getBySlug(string $slug): ?array {
        $s = $this->db->prepare("SELECT * FROM brands WHERE slug=:slug");
        $s->execute([':slug'=>$slug]);
        return $s->fetch() ?: null;
    }
    public function getAllWithCount(): array {
        return $this->db->query(
            "SELECT b.*, COUNT(v.id) AS vehicle_count
             FROM brands b
             LEFT JOIN vehicles v ON v.brand_id=b.id AND v.status='available'
             WHERE b.is_active=1
             GROUP BY b.id
             HAVING vehicle_count > 0
             ORDER BY b.sort_order, b.name"
        )->fetchAll();
    }
    public function create(array $d): int {
        $s = $this->db->prepare("INSERT INTO brands (name,slug,sort_order) VALUES (:n,:s,:o)");
        $s->execute([':n'=>$d['name'],':s'=>$d['slug'],':o'=>$d['sort_order']??0]);
        return (int)$this->db->lastInsertId();
    }
    public function update(int $id, array $d): bool {
        $s = $this->db->prepare("UPDATE brands SET name=:n, slug=:s, sort_order=:o WHERE id=:id");
        return $s->execute([':n'=>$d['name'],':s'=>$d['slug'],':o'=>$d['sort_order']??0,':id'=>$id]);
    }
    public function delete(int $id): bool {
        $s = $this->db->prepare("DELETE FROM brands WHERE id=:id");
        return $s->execute([':id'=>$id]);
    }
}
