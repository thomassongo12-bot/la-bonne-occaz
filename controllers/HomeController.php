<?php
require_once ROOT_PATH . '/controllers/BaseController.php';

class HomeController extends BaseController {

    public function index($param = null): void {
        $vehicleModel    = new Vehicle();
        $testimonialModel = new Testimonial();
        $brandModel      = new Brand();
        $fuelModel       = $this->getFuelTypes();
        $transmModel     = $this->getTransmissions();

        $featured     = $vehicleModel->getFeatured(6);
        $testimonials = $testimonialModel->getActive(5);
        $brands       = $brandModel->getAllWithCount();

        // Conditions de réservation pour la section accueil
        $settings = new SiteSettings();
        $reservationConditions = $settings->get('reservation_conditions', '');
        $reservationDeposit    = $settings->get('reservation_deposit', '300');
        $reservationHoldHours  = $settings->get('reservation_hold_hours', '72');

        $this->render('home/index', [
            'title'        => 'La Bonne Occaz - Véhicules d\'occasion de qualité',
            'metaDesc'     => 'La Bonne Occaz, spécialiste en véhicules d\'occasion multimarques. Large choix, prix transparents, service professionnel.',
            'featured'     => $featured,
            'testimonials' => $testimonials,
            'brands'       => $brands,
            'fuel_types'   => $fuelModel,
            'transmissions'=> $transmModel,
            'flash'        => $this->getFlash(),
            'reservationConditions' => $reservationConditions,
            'reservationDeposit'    => $reservationDeposit,
            'reservationHoldHours'  => $reservationHoldHours,
        ]);
    }

    public function notFound($param = null): void {
        http_response_code(404);
        $this->render('errors/404', ['title' => '404 - Page introuvable']);
    }

    private function getFuelTypes(): array {
        return Database::getInstance()->query("SELECT * FROM fuel_types ORDER BY name")->fetchAll();
    }

    private function getTransmissions(): array {
        return Database::getInstance()->query("SELECT * FROM transmissions ORDER BY name")->fetchAll();
    }
}
