<?php
class Testimonial {
    private PDO $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function getActive(): array {
        return $this->db->query("SELECT * FROM testimonials WHERE is_active=1 ORDER BY sort_order, id DESC")->fetchAll();
    }

    public function getAll(int $page=1, int $per=20): array {
        $offset = ($page-1)*$per;
        $s = $this->db->prepare("SELECT * FROM testimonials ORDER BY sort_order, created_at DESC LIMIT :l OFFSET :o");
        $s->bindValue(':l',$per,PDO::PARAM_INT);
        $s->bindValue(':o',$offset,PDO::PARAM_INT);
        $s->execute();
        return $s->fetchAll();
    }

    public function getById(int $id): ?array {
        $s = $this->db->prepare("SELECT * FROM testimonials WHERE id=:id");
        $s->execute([':id'=>$id]);
        return $s->fetch() ?: null;
    }

    public function create(array $d): int {
        $s = $this->db->prepare("INSERT INTO testimonials (author,photo_url,location,rating,content,vehicle_ref,is_active,sort_order) VALUES (:a,:ph,:l,:r,:c,:v,:ia,:so)");
        $s->execute([':a'=>$d['author'],':ph'=>$d['photo_url']??null,':l'=>$d['location']??null,':r'=>(int)$d['rating'],':c'=>$d['content'],':v'=>$d['vehicle_ref']??null,':ia'=>isset($d['is_active'])?1:0,':so'=>$d['sort_order']??0]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $d): bool {
        $s = $this->db->prepare("UPDATE testimonials SET author=:a,photo_url=:ph,location=:l,rating=:r,content=:c,vehicle_ref=:v,is_active=:ia,sort_order=:so WHERE id=:id");
        return $s->execute([':a'=>$d['author'],':ph'=>$d['photo_url']??null,':l'=>$d['location']??null,':r'=>(int)$d['rating'],':c'=>$d['content'],':v'=>$d['vehicle_ref']??null,':ia'=>isset($d['is_active'])?1:0,':so'=>$d['sort_order']??0,':id'=>$id]);
    }

    public function delete(int $id): bool {
        return $this->db->prepare("DELETE FROM testimonials WHERE id=:id")->execute([':id'=>$id]);
    }

    public function toggle(int $id): bool {
        return $this->db->prepare("UPDATE testimonials SET is_active=NOT is_active WHERE id=:id")->execute([':id'=>$id]);
    }
}
