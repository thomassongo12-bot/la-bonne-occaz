<?php
class SellingRequest {
    private PDO $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function create(array $d): int {
        $sql = "INSERT INTO selling_requests
            (first_name,last_name,email,phone,brand,model,version,year,mileage,
             fuel_type,transmission,color,condition_state,desired_price,description,ip_address)
            VALUES (:fn,:ln,:email,:phone,:brand,:model,:version,:year,:mileage,
                    :fuel,:trans,:color,:cond,:price,:desc,:ip)";
        $s = $this->db->prepare($sql);
        // La ville est ajoutée en tête de description si présente
        $desc = $d['description'] ?? '';
        if (!empty($d['city'])) {
            $desc = "Ville : " . $d['city'] . ($desc ? "\n\n" . $desc : '');
        }
        $s->execute([
            ':fn'      => $d['first_name'],
            ':ln'      => $d['last_name'],
            ':email'   => $d['email'],
            ':phone'   => $d['phone'],
            ':brand'   => $d['brand'],
            ':model'   => $d['model'],
            ':version' => $d['version']       ?? null,
            ':year'    => (int)$d['year'],
            ':mileage' => (int)$d['mileage'],
            ':fuel'    => $d['fuel_type'],
            ':trans'   => $d['transmission'],
            ':color'   => $d['color']         ?? null,
            ':cond'    => $d['condition_state'] ?? null,
            ':price'   => !empty($d['desired_price']) ? (float)$d['desired_price'] : null,
            ':desc'    => $desc,
            ':ip'      => $_SERVER['REMOTE_ADDR'],
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function addImages(int $reqId, array $filenames): void {
        $s = $this->db->prepare("INSERT INTO selling_request_images (selling_request_id,filename) VALUES (:id,:fn)");
        foreach ($filenames as $fn) $s->execute([':id'=>$reqId,':fn'=>$fn]);
    }

    public function getAll(int $page=1, int $per=20, string $status=''): array {
        $where = $status ? "WHERE status=:status" : "";
        $p = $status ? [':status'=>$status] : [];
        $offset = ($page-1)*$per;
        $sql = "SELECT * FROM selling_requests $where ORDER BY created_at DESC LIMIT :l OFFSET :o";
        $s = $this->db->prepare($sql);
        foreach ($p as $k=>$v) $s->bindValue($k,$v);
        $s->bindValue(':l',$per,PDO::PARAM_INT);
        $s->bindValue(':o',$offset,PDO::PARAM_INT);
        $s->execute();
        return $s->fetchAll();
    }

    public function countAll(string $status=''): int {
        $where = $status ? "WHERE status=:status" : "";
        $p = $status ? [':status'=>$status] : [];
        $s = $this->db->prepare("SELECT COUNT(*) FROM selling_requests $where");
        foreach ($p as $k=>$v) $s->bindValue($k,$v);
        $s->execute();
        return (int)$s->fetchColumn();
    }

    public function getById(int $id): ?array {
        $s = $this->db->prepare("SELECT * FROM selling_requests WHERE id=:id");
        $s->execute([':id'=>$id]);
        $row = $s->fetch();
        if (!$row) return null;
        $imgs = $this->db->prepare("SELECT * FROM selling_request_images WHERE selling_request_id=:id");
        $imgs->execute([':id'=>$id]);
        $row['images'] = $imgs->fetchAll();
        return $row;
    }

    public function updateStatus(int $id, string $status, string $note=''): bool {
        $s = $this->db->prepare("UPDATE selling_requests SET status=:s, admin_note=:n WHERE id=:id");
        return $s->execute([':s'=>$status,':n'=>$note,':id'=>$id]);
    }

    public function countNew(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM selling_requests WHERE status='new'")->fetchColumn();
    }
}
