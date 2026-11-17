<?php
require_once ROOT_PATH . '/controllers/admin/AdminBaseController.php';

class AdminDashboardController extends AdminBaseController {

    public function index($param = null): void {
        $vehicleModel  = new Vehicle();
        $contactModel  = new ContactRequest();
        $sellingModel  = new SellingRequest();
        $db            = Database::getInstance();

        $stats = $vehicleModel->getStats();
        $recent = $vehicleModel->getRecent(5);

        $newContacts = $contactModel->countNew();
        $newSelling  = $sellingModel->countNew();

        // Données trafic visiteurs — 30 derniers jours
        $trafficData = $db->query(
            "SELECT visited_date AS jour, COUNT(*) AS visites
             FROM visitor_logs
             WHERE visited_date >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)
             GROUP BY visited_date
             ORDER BY visited_date ASC"
        )->fetchAll();

        // Remplir les jours manquants avec 0
        $trafficFull = [];
        for ($i = 29; $i >= 0; $i--) {
            $trafficFull[date('Y-m-d', strtotime("-{$i} days"))] = 0;
        }
        foreach ($trafficData as $row) {
            $trafficFull[$row['jour']] = (int)$row['visites'];
        }

        // Stats trafic
        $totalVisiteurs = array_sum($trafficFull);
        $moyVisiteurs   = $totalVisiteurs > 0 ? round($totalVisiteurs / 30) : 0;

        $this->renderAdmin('dashboard', [
            'title'        => 'Tableau de bord - La Bonne Occaz Admin',
            'stats'        => $stats,
            'recent'       => $recent,
            'newContacts'  => $newContacts,
            'newSelling'   => $newSelling,
            'trafficFull'  => $trafficFull,
            'totalVisiteurs' => $totalVisiteurs,
            'moyVisiteurs'   => $moyVisiteurs,
            'flash'        => $this->getFlash(),
        ]);
    }
}
