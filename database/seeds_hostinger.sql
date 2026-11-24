-- ============================================================
-- La Bonne Occaz - Données initiales pour HOSTINGER
-- ⚠️  PAS de USE ni CREATE DATABASE — sélectionnez votre BDD dans phpMyAdmin
-- Importez ce fichier APRÈS schema_hostinger.sql
-- ============================================================

SET NAMES utf8mb4;

-- ============================================================
-- Admin — MOT DE PASSE : Admin2024 (changez-le en premier !)
-- ============================================================
INSERT IGNORE INTO `admins` (`username`, `email`, `password`, `role`, `first_name`, `last_name`)
VALUES (
    'admin',
    'admin@labonneoccaz.fr',
    '$2y$12$bP5jYcsNbCo2LO0BJYp6P.7IBihamDjpxIUawWjUU0lmhjXMVpLfK',
    'super_admin',
    'Admin',
    'LBO'
);
UPDATE `admins` SET `password` = '$2y$12$bP5jYcsNbCo2LO0BJYp6P.7IBihamDjpxIUawWjUU0lmhjXMVpLfK'
WHERE `username` = 'admin';

-- ============================================================
-- Carburants
-- ============================================================
INSERT IGNORE INTO `fuel_types` (`name`, `slug`) VALUES
('Essence',              'essence'),
('Diesel',               'diesel'),
('Hybride',              'hybride'),
('Hybride rechargeable', 'hybride-rechargeable'),
('Électrique',           'electrique'),
('GPL',                  'gpl'),
('Hydrogène',            'hydrogene');

-- ============================================================
-- Transmissions
-- ============================================================
INSERT IGNORE INTO `transmissions` (`name`, `slug`) VALUES
('Manuelle',         'manuelle'),
('Automatique',      'automatique'),
('Semi-automatique', 'semi-automatique');

-- ============================================================
-- Carrosseries
-- ============================================================
INSERT IGNORE INTO `body_types` (`name`, `slug`, `icon`) VALUES
('Citadine',   'citadine',   'fa-car'),
('Berline',    'berline',    'fa-car-side'),
('Break',      'break',      'fa-car'),
('SUV / 4x4',  'suv',        'fa-truck-monster'),
('Crossover',  'crossover',  'fa-car'),
('Monospace',  'monospace',  'fa-van-shuttle'),
('Coupé',      'coupe',      'fa-car-side'),
('Cabriolet',  'cabriolet',  'fa-car-side'),
('Utilitaire', 'utilitaire', 'fa-truck'),
('Pick-up',    'pickup',     'fa-truck-pickup');

-- ============================================================
-- Marques
-- ============================================================
INSERT IGNORE INTO `brands` (`name`, `slug`, `sort_order`) VALUES
('Abarth','abarth',1),('Alfa Romeo','alfa-romeo',2),('Audi','audi',3),
('BMW','bmw',4),('Chevrolet','chevrolet',5),('Citroën','citroen',6),
('Dacia','dacia',7),('DS','ds',8),('Fiat','fiat',9),('Ford','ford',10),
('Honda','honda',11),('Hyundai','hyundai',12),('Jeep','jeep',13),
('Kia','kia',14),('Land Rover','land-rover',15),('Lexus','lexus',16),
('Mazda','mazda',17),('Mercedes-Benz','mercedes-benz',18),('Mini','mini',19),
('Mitsubishi','mitsubishi',20),('Nissan','nissan',21),('Opel','opel',22),
('Peugeot','peugeot',23),('Porsche','porsche',24),('Renault','renault',25),
('Seat','seat',26),('Skoda','skoda',27),('Suzuki','suzuki',28),
('Tesla','tesla',29),('Toyota','toyota',30),('Volkswagen','volkswagen',31),
('Volvo','volvo',32);

-- ============================================================
-- Témoignages
-- ============================================================
INSERT INTO `testimonials` (`author`,`location`,`rating`,`content`,`vehicle_ref`,`is_active`,`sort_order`) VALUES
('Thomas M.','Annemasse',5,'Service impeccable, véhicule exactement tel que décrit. L''équipe a pris le temps de tout expliquer. Je recommande vivement La Bonne Occaz.','Volkswagen Golf 8',1,1),
('Sophie L.','Genève',5,'Très bon accueil, dossier traité rapidement. La voiture était prête à temps, propre et vérifiée. Un vrai plaisir.','BMW Série 3',1,2),
('Marc D.','Annecy',4,'Bonne expérience dans l''ensemble. Véhicule bien présenté, vendeurs à l''écoute. Je reviendrai pour mon prochain achat.','Peugeot 3008',1,3),
('Julie R.','Thonon-les-Bains',5,'Première voiture achetée en dépôt-vente, tout s''est passé parfaitement. Merci pour l''accompagnement.','Renault Clio 5',1,4),
('Nicolas P.','Bonneville',5,'Excellent rapport qualité/prix. La voiture a été inspectée et révisée avant remise.','Audi A3 Sportback',1,5);

-- ============================================================
-- Paramètres du site
-- ============================================================
INSERT IGNORE INTO `site_settings` (`setting_key`,`setting_val`,`setting_type`,`label`,`group_name`) VALUES
('site_name',              'La Bonne Occaz',                           'text',    'Nom du site',               'general'),
('logo_url',               '',                                         'image',   'Logo du site',              'branding'),
('favicon_url',            '',                                         'image',   'Favicon',                   'branding'),
('logo_text',              'La Bonne Occaz',                           'text',    'Texte logo (si pas image)',  'branding'),
('contact_phone',          '04 50 XX XX XX',                           'text',    'Téléphone',                 'contact'),
('contact_email',          'contact@labonneoccaz.fr',                  'text',    'Email de contact',          'contact'),
('contact_address',        '12 Rue de l''Industrie, 74100 Annemasse',  'text',    'Adresse',                   'contact'),
('contact_hours',          'Lun-Sam : 9h00 - 18h30',                   'text',    'Horaires',                  'contact'),
('whatsapp_number',        '33450XXXXXX',                              'text',    'Numéro WhatsApp',           'contact'),
('tiktok_url',             '',                                         'text',    'TikTok',                    'social'),
('hero_title',             'Votre prochaine voiture vous attend',       'text',    'Titre Hero',                'homepage'),
('hero_subtitle',          'Découvrez notre sélection de véhicules d''occasion soigneusement sélectionnés', 'textarea','Sous-titre Hero','homepage'),
('about_text',             'La Bonne Occaz est votre partenaire de confiance pour l''achat et la vente de véhicules d''occasion.', 'textarea','Texte À propos','about'),
('google_maps_embed',      '',                                         'textarea','Code embed Google Maps',    'contact'),
('meta_title_home',        'La Bonne Occaz - Véhicules d''occasion de qualité','text','Meta title accueil','seo'),
('meta_desc_home',         'Votre spécialiste en véhicules d''occasion multimarques. Large choix, prix transparents, service de qualité.','textarea','Meta description accueil','seo'),
('reservation_conditions', 'Pour réserver ce véhicule, vous devez :\n\n1. Verser un acompte de 300 € non remboursable en cas de désistement.\n2. Vous présenter avec une pièce d''identité valide.\n3. Confirmer votre réservation dans les 24h suivant votre demande.\n4. Le véhicule sera réservé pour vous pendant 72h après confirmation.','textarea','Conditions de réservation','reservation'),
('reservation_deposit',    '300',                                      'text',    'Montant acompte (€)',        'reservation'),
('reservation_hold_hours', '72',                                       'text',    'Durée réservation (heures)', 'reservation'),
('delivery_conditions',    'Conditions et modalités de livraison des véhicules\n\nAfin de vous garantir une expérience d''achat confortable et simplifiée, notre établissement propose un service de livraison directement à l''adresse de votre choix. L''acheminement des véhicules est confié à un prestataire logistique professionnel partenaire.\n\nDélai de livraison\nLa livraison du véhicule s''effectue généralement dans un délai de 24 à 48 heures suivant la réception du règlement des frais de livraison et la validation du dossier.\n\nFrais de livraison\nLes frais de livraison varient entre 150 € et 300 €, en fonction de la distance séparant notre établissement de l''adresse communiquée par le client. Le montant exact est précisé avant toute validation.\n\nModalités de paiement\nLe règlement des frais de livraison s''effectue exclusivement par virement bancaire.\nDès réception du paiement, le véhicule est confié à notre prestataire logistique professionnel. Un numéro de suivi est communiqué au client afin de lui permettre de suivre l''acheminement de son véhicule jusqu''à sa destination.\n\nRéception du véhicule\nLors de la livraison, le client est invité à vérifier l''état général du véhicule, sa conformité aux caractéristiques annoncées ainsi que l''ensemble des documents administratifs qui l''accompagnent, avant de confirmer sa réception.\n\nNotre engagement : vous garantir une livraison professionnelle, sécurisée et transparente, tout en vous accompagnant jusqu''à la réception de votre véhicule.', 'textarea', 'Conditions de livraison', 'reservation');
