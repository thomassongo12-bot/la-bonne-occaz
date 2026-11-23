<?php
class Reservation {
    private PDO $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function create(array $d): int {
        $sql = "INSERT INTO reservations
            (vehicle_id, first_name, last_name, email, phone, message, conditions_accepted, ip_address)
            VALUES (:vid,:fn,:ln,:email,:phone,:msg,:cond,:ip)";
        $s = $this->db->prepare($sql);
        $s->execute([
            ':vid'   => (int)$d['vehicle_id'],
            ':fn'    => $d['first_name'],
            ':ln'    => $d['last_name'],
            ':email' => $d['email'],
            ':phone' => $d['phone'],
            ':msg'   => $d['message'] ?? null,
            ':cond'  => $d['conditions_accepted'] ? 1 : 0,
            ':ip'    => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function getAll(int $page = 1, int $per = 20, string $status = ''): array {
        $where = $status ? "WHERE r.status = :status" : "WHERE 1=1";
        $p     = $status ? [':status' => $status] : [];
        $offset = ($page - 1) * $per;
        $sql = "SELECT r.*, v.model, v.year, b.name AS brand_name, v.slug AS vehicle_slug
                FROM reservations r
                JOIN vehicles v ON r.vehicle_id = v.id
                JOIN brands b ON v.brand_id = b.id
                $where
                ORDER BY r.created_at DESC
                LIMIT :l OFFSET :o";
        $s = $this->db->prepare($sql);
        foreach ($p as $k => $v) $s->bindValue($k, $v);
        $s->bindValue(':l', $per, PDO::PARAM_INT);
        $s->bindValue(':o', $offset, PDO::PARAM_INT);
        $s->execute();
        return $s->fetchAll();
    }

    public function countAll(string $status = ''): int {
        $where = $status ? "WHERE status = :status" : "";
        $p     = $status ? [':status' => $status] : [];
        $s = $this->db->prepare("SELECT COUNT(*) FROM reservations $where");
        foreach ($p as $k => $v) $s->bindValue($k, $v);
        $s->execute();
        return (int)$s->fetchColumn();
    }

    public function getById(int $id): ?array {
        $s = $this->db->prepare(
            "SELECT r.*, v.model, v.year, v.price, v.slug AS vehicle_slug,
                    b.name AS brand_name, vi.filename AS main_image
             FROM reservations r
             JOIN vehicles v ON r.vehicle_id = v.id
             JOIN brands b ON v.brand_id = b.id
             LEFT JOIN vehicle_images vi ON vi.vehicle_id = v.id AND vi.is_main = 1
             WHERE r.id = :id"
        );
        $s->execute([':id' => $id]);
        return $s->fetch() ?: null;
    }

    public function updateStatus(int $id, string $status, string $note = ''): bool {
        $s = $this->db->prepare("UPDATE reservations SET status=:s, admin_note=:n WHERE id=:id");
        return $s->execute([':s' => $status, ':n' => $note, ':id' => $id]);
    }

    public function countNew(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM reservations WHERE status='new'")->fetchColumn();
    }

    public function getByVehicle(int $vehicleId): array {
        $s = $this->db->prepare("SELECT * FROM reservations WHERE vehicle_id=:id ORDER BY created_at DESC LIMIT 10");
        $s->execute([':id' => $vehicleId]);
        return $s->fetchAll();
    }
}
