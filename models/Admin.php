<?php
class Admin {
    private PDO $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function getByUsername(string $username): ?array {
        $s = $this->db->prepare("SELECT * FROM admins WHERE username=:u AND is_active=1");
        $s->execute([':u'=>$username]);
        return $s->fetch() ?: null;
    }

    public function getById(int $id): ?array {
        $s = $this->db->prepare("SELECT * FROM admins WHERE id=:id");
        $s->execute([':id'=>$id]);
        return $s->fetch() ?: null;
    }

    public function getAll(): array {
        return $this->db->query("SELECT id,username,email,role,first_name,last_name,is_active,last_login,created_at FROM admins ORDER BY created_at")->fetchAll();
    }

    public function updateLastLogin(int $id): void {
        $this->db->prepare("UPDATE admins SET last_login=NOW() WHERE id=:id")->execute([':id'=>$id]);
    }

    public function create(array $d): int {
        $s = $this->db->prepare("INSERT INTO admins (username,email,password,role,first_name,last_name) VALUES (:u,:e,:p,:r,:fn,:ln)");
        $s->execute([':u'=>$d['username'],':e'=>$d['email'],':p'=>password_hash($d['password'],PASSWORD_BCRYPT,['cost'=>12]),':r'=>$d['role']??'admin',':fn'=>$d['first_name']??'',':ln'=>$d['last_name']??'']);
        return (int)$this->db->lastInsertId();
    }

    public function updatePassword(int $id, string $password): bool {
        $s = $this->db->prepare("UPDATE admins SET password=:p WHERE id=:id");
        return $s->execute([':p'=>password_hash($password,PASSWORD_BCRYPT,['cost'=>12]),':id'=>$id]);
    }

    public function verify(string $username, string $password): ?array {
        $admin = $this->getByUsername($username);
        if ($admin && password_verify($password, $admin['password'])) {
            $this->updateLastLogin($admin['id']);
            return $admin;
        }
        return null;
    }
}
