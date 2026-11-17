<?php
class ContactRequest {
    private PDO $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function create(array $d): int {
        $sql = "INSERT INTO contact_requests (type,vehicle_id,first_name,last_name,email,phone,message,ip_address)
                VALUES (:type,:vid,:fn,:ln,:email,:phone,:msg,:ip)";
        $s = $this->db->prepare($sql);
        $s->execute([
            ':type'  => $d['type']       ?? 'contact',
            ':vid'   => $d['vehicle_id'] ?? null,
            ':fn'    => $d['first_name'],
            ':ln'    => $d['last_name'],
            ':email' => $d['email'],
            ':phone' => $d['phone']      ?? null,
            ':msg'   => $d['message'],
            ':ip'    => $d['ip']         ?? $_SERVER['REMOTE_ADDR'],
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function getAll(int $page=1, int $per=20, string $type='', string $status=''): array {
        $where = "WHERE 1=1";
        $p = [];
        if ($type)   { $where .= " AND cr.type=:type";     $p[':type']   = $type; }
        if ($status) { $where .= " AND cr.status=:status"; $p[':status'] = $status; }
        $offset = ($page-1)*$per;
        $sql = "SELECT cr.*, v.model AS vehicle_model, b.name AS vehicle_brand
                FROM contact_requests cr
                LEFT JOIN vehicles v ON cr.vehicle_id=v.id
                LEFT JOIN brands b ON v.brand_id=b.id
                $where ORDER BY cr.created_at DESC LIMIT :l OFFSET :o";
        $s = $this->db->prepare($sql);
        foreach ($p as $k=>$v) $s->bindValue($k,$v);
        $s->bindValue(':l',$per,PDO::PARAM_INT);
        $s->bindValue(':o',$offset,PDO::PARAM_INT);
        $s->execute();
        return $s->fetchAll();
    }

    public function countAll(string $type='', string $status=''): int {
        $where = "WHERE 1=1";
        $p = [];
        if ($type)   { $where .= " AND type=:type";     $p[':type']   = $type; }
        if ($status) { $where .= " AND status=:status"; $p[':status'] = $status; }
        $s = $this->db->prepare("SELECT COUNT(*) FROM contact_requests $where");
        foreach ($p as $k=>$v) $s->bindValue($k,$v);
        $s->execute();
        return (int)$s->fetchColumn();
    }

    public function getById(int $id): ?array {
        $s = $this->db->prepare("SELECT cr.*, v.model AS vehicle_model, b.name AS vehicle_brand
                FROM contact_requests cr
                LEFT JOIN vehicles v ON cr.vehicle_id=v.id
                LEFT JOIN brands b ON v.brand_id=b.id
                WHERE cr.id=:id");
        $s->execute([':id'=>$id]);
        return $s->fetch() ?: null;
    }

    public function updateStatus(int $id, string $status, string $note=''): bool {
        $s = $this->db->prepare("UPDATE contact_requests SET status=:s, admin_note=:n WHERE id=:id");
        return $s->execute([':s'=>$status,':n'=>$note,':id'=>$id]);
    }

    public function countNew(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM contact_requests WHERE status='new'")->fetchColumn();
    }
}
