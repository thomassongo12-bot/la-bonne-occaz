<?php
/**
 * Modèle Vehicle - La Bonne Occaz
 */
class Vehicle {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // ------------------------------------------------------------------
    // LECTURE
    // ------------------------------------------------------------------

    /** Récupère les véhicules en vedette pour la homepage */
    public function getFeatured(int $limit = 6): array {
        $sql = "SELECT v.*, b.name AS brand_name, b.slug AS brand_slug,
                       f.name AS fuel_name, t.name AS transmission_name,
                       bt.name AS body_type_name,
                       vi.filename AS main_image
                FROM vehicles v
                JOIN brands b ON v.brand_id = b.id
                JOIN fuel_types f ON v.fuel_type_id = f.id
                JOIN transmissions t ON v.transmission_id = t.id
                LEFT JOIN body_types bt ON v.body_type_id = bt.id
                LEFT JOIN vehicle_images vi ON vi.vehicle_id = v.id AND vi.is_main = 1
                WHERE v.status = 'available' AND v.is_featured = 1
                ORDER BY v.created_at DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Catalogue avec filtres et pagination */
    public function getCatalog(array $filters = [], int $page = 1, int $perPage = 12, string $sort = 'newest'): array {
        [$where, $params] = $this->buildWhereClause($filters);

        $orderBy = match($sort) {
            'price_asc'  => 'v.price ASC',
            'price_desc' => 'v.price DESC',
            'year_desc'  => 'v.year DESC, v.created_at DESC',
            'year_asc'   => 'v.year ASC',
            'mileage'    => 'v.mileage ASC',
            default      => 'v.created_at DESC',
        };

        $offset = ($page - 1) * $perPage;

        $sql = "SELECT v.*, b.name AS brand_name, b.slug AS brand_slug,
                       f.name AS fuel_name, t.name AS transmission_name,
                       bt.name AS body_type_name,
                       vi.filename AS main_image
                FROM vehicles v
                JOIN brands b ON v.brand_id = b.id
                JOIN fuel_types f ON v.fuel_type_id = f.id
                JOIN transmissions t ON v.transmission_id = t.id
                LEFT JOIN body_types bt ON v.body_type_id = bt.id
                LEFT JOIN vehicle_images vi ON vi.vehicle_id = v.id AND vi.is_main = 1
                WHERE v.status IN ('available','reserved') $where
                ORDER BY $orderBy
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) $stmt->bindValue($key, $val);
        $stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Nombre total de véhicules pour la pagination */
    public function countCatalog(array $filters = []): int {
        [$where, $params] = $this->buildWhereClause($filters);
        $sql = "SELECT COUNT(*) FROM vehicles v
                JOIN brands b ON v.brand_id = b.id
                JOIN fuel_types f ON v.fuel_type_id = f.id
                JOIN transmissions t ON v.transmission_id = t.id
                WHERE v.status IN ('available','reserved') $where";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) $stmt->bindValue($key, $val);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    /** Véhicule par slug */
    public function getBySlug(string $slug): ?array {
        $sql = "SELECT v.*, b.name AS brand_name, b.slug AS brand_slug,
                       f.name AS fuel_name, f.slug AS fuel_slug,
                       t.name AS transmission_name,
                       bt.name AS body_type_name
                FROM vehicles v
                JOIN brands b ON v.brand_id = b.id
                JOIN fuel_types f ON v.fuel_type_id = f.id
                JOIN transmissions t ON v.transmission_id = t.id
                LEFT JOIN body_types bt ON v.body_type_id = bt.id
                WHERE v.slug = :slug";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':slug' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Véhicule par ID */
    public function getById(int $id): ?array {
        $sql = "SELECT v.*, b.name AS brand_name, b.slug AS brand_slug,
                       f.name AS fuel_name, f.slug AS fuel_slug,
                       t.name AS transmission_name,
                       bt.name AS body_type_name
                FROM vehicles v
                JOIN brands b ON v.brand_id = b.id
                JOIN fuel_types f ON v.fuel_type_id = f.id
                JOIN transmissions t ON v.transmission_id = t.id
                LEFT JOIN body_types bt ON v.body_type_id = bt.id
                WHERE v.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Images d'un véhicule */
    public function getImages(int $vehicleId): array {
        $stmt = $this->db->prepare("SELECT * FROM vehicle_images WHERE vehicle_id = :id ORDER BY sort_order ASC, is_main DESC");
        $stmt->execute([':id' => $vehicleId]);
        return $stmt->fetchAll();
    }

    /** Véhicules similaires */
    public function getSimilar(int $vehicleId, int $brandId, int $bodyTypeId, float $price, int $limit = 4): array {
        $sql = "SELECT v.*, b.name AS brand_name, f.name AS fuel_name,
                       t.name AS transmission_name, vi.filename AS main_image
                FROM vehicles v
                JOIN brands b ON v.brand_id = b.id
                JOIN fuel_types f ON v.fuel_type_id = f.id
                JOIN transmissions t ON v.transmission_id = t.id
                LEFT JOIN vehicle_images vi ON vi.vehicle_id = v.id AND vi.is_main = 1
                WHERE v.status = 'available'
                  AND v.id != :id
                  AND (v.brand_id = :brand OR v.body_type_id = :body)
                  AND v.price BETWEEN :price_min AND :price_max
                ORDER BY RAND()
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id',        $vehicleId,        PDO::PARAM_INT);
        $stmt->bindValue(':brand',     $brandId,          PDO::PARAM_INT);
        $stmt->bindValue(':body',      $bodyTypeId,       PDO::PARAM_INT);
        $stmt->bindValue(':price_min', $price * 0.6);
        $stmt->bindValue(':price_max', $price * 1.4);
        $stmt->bindValue(':limit',     $limit,            PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Recherche rapide homepage */
    public function quickSearch(array $params): array {
        return $this->getCatalog($params, 1, 12);
    }

    // ------------------------------------------------------------------
    // ADMIN CRUD
    // ------------------------------------------------------------------

    public function getAll(int $page = 1, int $perPage = 20, string $search = ''): array {
        $where  = "WHERE 1=1";
        $params = [];
        if ($search !== '') {
            $where .= " AND (b.name LIKE :s OR v.model LIKE :s2 OR v.version LIKE :s3)";
            $params[':s']  = "%$search%";
            $params[':s2'] = "%$search%";
            $params[':s3'] = "%$search%";
        }
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT v.*, b.name AS brand_name, f.name AS fuel_name,
                       t.name AS transmission_name, vi.filename AS main_image
                FROM vehicles v
                JOIN brands b ON v.brand_id = b.id
                JOIN fuel_types f ON v.fuel_type_id = f.id
                JOIN transmissions t ON v.transmission_id = t.id
                LEFT JOIN vehicle_images vi ON vi.vehicle_id = v.id AND vi.is_main = 1
                $where
                ORDER BY v.created_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countAll(string $search = ''): int {
        $where  = "WHERE 1=1";
        $params = [];
        if ($search !== '') {
            $where .= " AND (b.name LIKE :s OR v.model LIKE :s2)";
            $params[':s']  = "%$search%";
            $params[':s2'] = "%$search%";
        }
        $sql = "SELECT COUNT(*) FROM vehicles v JOIN brands b ON v.brand_id = b.id $where";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function create(array $data): int {
        $sql = "INSERT INTO vehicles
            (slug, brand_id, model, version, year, mileage, fuel_type_id, transmission_id,
             body_type_id, color, doors, seats, power_hp, power_kw, engine_cc, co2_emission,
             energy_rating, price, price_promo, is_featured, is_new_badge, status, condition_state,
             description, has_service_history, has_warranty, warranty_months, has_control_tech,
             equipment, meta_title, meta_description, published_at)
            VALUES
            (:slug,:brand_id,:model,:version,:year,:mileage,:fuel_type_id,:transmission_id,
             :body_type_id,:color,:doors,:seats,:power_hp,:power_kw,:engine_cc,:co2_emission,
             :energy_rating,:price,:price_promo,:is_featured,:is_new_badge,:status,:condition_state,
             :description,:has_service_history,:has_warranty,:warranty_months,:has_control_tech,
             :equipment,:meta_title,:meta_description,:published_at)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($this->bindVehicleData($data));
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $sql = "UPDATE vehicles SET
            slug=:slug, brand_id=:brand_id, model=:model, version=:version, year=:year,
            mileage=:mileage, fuel_type_id=:fuel_type_id, transmission_id=:transmission_id,
            body_type_id=:body_type_id, color=:color, doors=:doors, seats=:seats,
            power_hp=:power_hp, power_kw=:power_kw, engine_cc=:engine_cc, co2_emission=:co2_emission,
            energy_rating=:energy_rating, price=:price, price_promo=:price_promo,
            is_featured=:is_featured, is_new_badge=:is_new_badge, status=:status,
            condition_state=:condition_state, description=:description,
            has_service_history=:has_service_history, has_warranty=:has_warranty,
            warranty_months=:warranty_months, has_control_tech=:has_control_tech,
            equipment=:equipment, meta_title=:meta_title, meta_description=:meta_description,
            published_at=:published_at
            WHERE id=:id";
        $params = $this->bindVehicleData($data);
        $params[':id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM vehicles WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function updateStatus(int $id, string $status): bool {
        $stmt = $this->db->prepare("UPDATE vehicles SET status=:s WHERE id=:id");
        return $stmt->execute([':s' => $status, ':id' => $id]);
    }

    public function toggleFeatured(int $id): bool {
        $stmt = $this->db->prepare("UPDATE vehicles SET is_featured = NOT is_featured WHERE id=:id");
        return $stmt->execute([':id' => $id]);
    }

    // Images
    public function addImage(int $vehicleId, string $filename, string $altText = '', bool $isMain = false, int $sort = 0): int {
        if ($isMain) {
            $this->db->prepare("UPDATE vehicle_images SET is_main=0 WHERE vehicle_id=:vid")
                     ->execute([':vid' => $vehicleId]);
        }
        $stmt = $this->db->prepare("INSERT INTO vehicle_images (vehicle_id,filename,alt_text,is_main,sort_order) VALUES (:vid,:fn,:alt,:main,:sort)");
        $stmt->execute([':vid'=>$vehicleId,':fn'=>$filename,':alt'=>$altText,':main'=>(int)$isMain,':sort'=>$sort]);
        return (int)$this->db->lastInsertId();
    }

    public function deleteImage(int $imageId): ?string {
        $stmt = $this->db->prepare("SELECT filename FROM vehicle_images WHERE id=:id");
        $stmt->execute([':id' => $imageId]);
        $row = $stmt->fetch();
        if (!$row) return null;
        $this->db->prepare("DELETE FROM vehicle_images WHERE id=:id")->execute([':id' => $imageId]);
        return $row['filename'];
    }

    public function setMainImage(int $vehicleId, int $imageId): void {
        $this->db->prepare("UPDATE vehicle_images SET is_main=0 WHERE vehicle_id=:vid")->execute([':vid'=>$vehicleId]);
        $this->db->prepare("UPDATE vehicle_images SET is_main=1 WHERE id=:id")->execute([':id'=>$imageId]);
    }

    // Stats admin
    public function getStats(): array {
        $stats = [];
        $rows = $this->db->query("SELECT status, COUNT(*) AS cnt FROM vehicles GROUP BY status")->fetchAll();
        $stats['total'] = 0;
        foreach ($rows as $row) {
            $stats[$row['status']] = (int)$row['cnt'];
            $stats['total'] += (int)$row['cnt'];
        }
        $stats['available'] = $stats['available'] ?? 0;
        $stats['sold']      = $stats['sold']      ?? 0;
        $stats['reserved']  = $stats['reserved']  ?? 0;
        return $stats;
    }

    public function getRecent(int $limit = 5): array {
        $sql = "SELECT v.*, b.name AS brand_name, vi.filename AS main_image
                FROM vehicles v JOIN brands b ON v.brand_id=b.id
                LEFT JOIN vehicle_images vi ON vi.vehicle_id=v.id AND vi.is_main=1
                ORDER BY v.created_at DESC LIMIT :l";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':l', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ------------------------------------------------------------------
    // HELPERS
    // ------------------------------------------------------------------

    private function buildWhereClause(array $f): array {
        $where  = '';
        $params = [];

        if (!empty($f['brand_id'])) {
            $where .= " AND v.brand_id = :brand_id";
            $params[':brand_id'] = (int)$f['brand_id'];
        }
        if (!empty($f['brand_slug'])) {
            $where .= " AND b.slug = :brand_slug";
            $params[':brand_slug'] = $f['brand_slug'];
        }
        if (!empty($f['fuel_type_id'])) {
            $where .= " AND v.fuel_type_id = :fuel_type_id";
            $params[':fuel_type_id'] = (int)$f['fuel_type_id'];
        }
        if (!empty($f['transmission_id'])) {
            $where .= " AND v.transmission_id = :transmission_id";
            $params[':transmission_id'] = (int)$f['transmission_id'];
        }
        if (!empty($f['body_type_id'])) {
            $where .= " AND v.body_type_id = :body_type_id";
            $params[':body_type_id'] = (int)$f['body_type_id'];
        }
        if (!empty($f['price_min'])) {
            $where .= " AND v.price >= :price_min";
            $params[':price_min'] = (float)$f['price_min'];
        }
        if (!empty($f['price_max'])) {
            $where .= " AND v.price <= :price_max";
            $params[':price_max'] = (float)$f['price_max'];
        }
        if (!empty($f['year_min'])) {
            $where .= " AND v.year >= :year_min";
            $params[':year_min'] = (int)$f['year_min'];
        }
        if (!empty($f['year_max'])) {
            $where .= " AND v.year <= :year_max";
            $params[':year_max'] = (int)$f['year_max'];
        }
        if (!empty($f['mileage_max'])) {
            $where .= " AND v.mileage <= :mileage_max";
            $params[':mileage_max'] = (int)$f['mileage_max'];
        }
        if (!empty($f['power_min'])) {
            $where .= " AND v.power_hp >= :power_min";
            $params[':power_min'] = (int)$f['power_min'];
        }
        if (!empty($f['color'])) {
            $where .= " AND v.color LIKE :color";
            $params[':color'] = '%' . $f['color'] . '%';
        }
        if (!empty($f['search'])) {
            $where .= " AND (b.name LIKE :s OR v.model LIKE :s2 OR v.version LIKE :s3)";
            $q = '%' . $f['search'] . '%';
            $params[':s']  = $q;
            $params[':s2'] = $q;
            $params[':s3'] = $q;
        }
        return [$where, $params];
    }

    private function bindVehicleData(array $d): array {
        return [
            ':slug'                => $d['slug']                ?? '',
            ':brand_id'            => $d['brand_id']            ?? null,
            ':model'               => $d['model']               ?? '',
            ':version'             => $d['version']             ?? null,
            ':year'                => $d['year']                ?? date('Y'),
            ':mileage'             => $d['mileage']             ?? 0,
            ':fuel_type_id'        => $d['fuel_type_id']        ?? null,
            ':transmission_id'     => $d['transmission_id']     ?? null,
            ':body_type_id'        => $d['body_type_id']        ?? null,
            ':color'               => $d['color']               ?? null,
            ':doors'               => $d['doors']               ?? null,
            ':seats'               => $d['seats']               ?? null,
            ':power_hp'            => $d['power_hp']            ?? null,
            ':power_kw'            => $d['power_kw']            ?? null,
            ':engine_cc'           => $d['engine_cc']           ?? null,
            ':co2_emission'        => $d['co2_emission']        ?? null,
            ':energy_rating'       => $d['energy_rating']       ?? null,
            ':price'               => $d['price']               ?? 0,
            ':price_promo'         => $d['price_promo']         ?? null,
            ':is_featured'         => isset($d['is_featured'])  ? 1 : 0,
            ':is_new_badge'        => isset($d['is_new_badge']) ? 1 : 0,
            ':status'              => $d['status']              ?? 'draft',
            ':condition_state'     => $d['condition_state']     ?? 'good',
            ':description'         => $d['description']         ?? null,
            ':has_service_history' => isset($d['has_service_history']) ? 1 : 0,
            ':has_warranty'        => isset($d['has_warranty'])  ? 1 : 0,
            ':warranty_months'     => $d['warranty_months']     ?? null,
            ':has_control_tech'    => isset($d['has_control_tech']) ? 1 : 0,
            ':equipment'           => isset($d['equipment'])    ? json_encode($d['equipment']) : null,
            ':meta_title'          => $d['meta_title']          ?? null,
            ':meta_description'    => $d['meta_description']    ?? null,
            ':published_at'        => $d['status'] === 'available' ? date('Y-m-d H:i:s') : null,
        ];
    }

    public static function generateSlug(string $brand, string $model, string $version = '', int $year = 0): string {
        $str = $brand . '-' . $model . ($version ? '-' . $version : '') . ($year ? '-' . $year : '');
        $str = strtolower($str);
        $str = preg_replace('/[àáâãäå]/u', 'a', $str);
        $str = preg_replace('/[èéêë]/u',   'e', $str);
        $str = preg_replace('/[ìíîï]/u',   'i', $str);
        $str = preg_replace('/[òóôõö]/u',  'o', $str);
        $str = preg_replace('/[ùúûü]/u',   'u', $str);
        $str = preg_replace('/[ç]/u',      'c', $str);
        $str = preg_replace('/[^a-z0-9\-]/', '-', $str);
        $str = preg_replace('/-+/', '-', $str);
        return trim($str, '-');
    }
}
