USE `labonneoccaz`;

-- ============================================================
-- Données initiales
-- ============================================================

-- Admin par défaut — MOT DE PASSE : Admin2024 (à changer après la première connexion)
INSERT IGNORE INTO `admins` (`username`, `email`, `password`, `role`, `first_name`, `last_name`)
VALUES (
    'admin',
    'admin@labonneoccaz.fr',
    '$2y$12$bP5jYcsNbCo2LO0BJYp6P.7IBihamDjpxIUawWjUU0lmhjXMVpLfK',
    'super_admin',
    'Admin',
    'LBO'
);

-- Si la table existe déjà, forcer la mise à jour du mot de passe
UPDATE `admins` SET
    `password` = '$2y$12$bP5jYcsNbCo2LO0BJYp6P.7IBihamDjpxIUawWjUU0lmhjXMVpLfK'
WHERE `username` = 'admin';

-- Carburants
INSERT IGNORE INTO `fuel_types` (`name`, `slug`) VALUES
('Essence',         'essence'),
('Diesel',          'diesel'),
('Hybride',         'hybride'),
('Hybride rechargeable', 'hybride-rechargeable'),
('Électrique',      'electrique'),
('GPL',             'gpl'),
('Hydrogène',       'hydrogene');

-- Transmissions
INSERT IGNORE INTO `transmissions` (`name`, `slug`) VALUES
('Manuelle',        'manuelle'),
('Automatique',     'automatique'),
('Semi-automatique','semi-automatique');

-- Carrosseries
INSERT IGNORE INTO `body_types` (`name`, `slug`, `icon`) VALUES
('Citadine',       'citadine',       'fa-car'),
('Berline',        'berline',        'fa-car-side'),
('Break',          'break',          'fa-car'),
('SUV / 4x4',      'suv',            'fa-truck-monster'),
('Crossover',      'crossover',      'fa-car'),
('Monospace',      'monospace',      'fa-van-shuttle'),
('Coupé',          'coupe',          'fa-car-side'),
('Cabriolet',      'cabriolet',      'fa-car-side'),
('Utilitaire',     'utilitaire',     'fa-truck'),
('Pick-up',        'pickup',         'fa-truck-pickup');

-- Marques
INSERT IGNORE INTO `brands` (`name`, `slug`, `sort_order`) VALUES
('Abarth',      'abarth',      1),
('Alfa Romeo',  'alfa-romeo',  2),
('Audi',        'audi',        3),
('BMW',         'bmw',         4),
('Chevrolet',   'chevrolet',   5),
('Citroën',     'citroen',     6),
('Dacia',       'dacia',       7),
('DS',          'ds',          8),
('Fiat',        'fiat',        9),
('Ford',        'ford',        10),
('Honda',       'honda',       11),
('Hyundai',     'hyundai',     12),
('Jeep',        'jeep',        13),
('Kia',         'kia',         14),
('Land Rover',  'land-rover',  15),
('Lexus',       'lexus',       16),
('Mazda',       'mazda',       17),
('Mercedes-Benz','mercedes-benz',18),
('Mini',        'mini',        19),
('Mitsubishi',  'mitsubishi',  20),
('Nissan',      'nissan',      21),
('Opel',        'opel',        22),
('Peugeot',     'peugeot',     23),
('Porsche',     'porsche',     24),
('Renault',     'renault',     25),
('Seat',        'seat',        26),
('Skoda',       'skoda',       27),
('Suzuki',      'suzuki',      28),
('Tesla',       'tesla',       29),
('Toyota',      'toyota',      30),
('Volkswagen',  'volkswagen',  31),
('Volvo',       'volvo',       32);

-- Véhicules de démonstration
INSERT INTO `vehicles`
    (`slug`,`brand_id`,`model`,`version`,`year`,`mileage`,`fuel_type_id`,`transmission_id`,`body_type_id`,
     `color`,`doors`,`seats`,`power_hp`,`price`,`is_featured`,`is_new_badge`,`status`,`condition_state`,
     `description`,`has_service_history`,`has_warranty`,`warranty_months`,`has_control_tech`,`published_at`)
VALUES
(
    'bmw-serie-3-320d-xdrive',
    (SELECT id FROM brands WHERE slug='bmw'),
    'Série 3','320d xDrive Luxury Line',2021,42000,
    (SELECT id FROM fuel_types WHERE slug='diesel'),
    (SELECT id FROM transmissions WHERE slug='automatique'),
    (SELECT id FROM body_types WHERE slug='berline'),
    'Blanc Alpinweiss',4,5,190,
    29900.00,1,0,'available','excellent',
    'Superbe BMW Série 3 320d xDrive en excellent état. Carnet d\'entretien complet, première main. Équipée du pack Luxury Line avec cuirs, navigation professionnelle, caméra de recul, toit ouvrant.',
    1,1,12,1,NOW()
),
(
    'volkswagen-golf-8-tsi-style',
    (SELECT id FROM brands WHERE slug='volkswagen'),
    'Golf 8','1.5 TSI 150 Style',2022,18500,
    (SELECT id FROM fuel_types WHERE slug='essence'),
    (SELECT id FROM transmissions WHERE slug='automatique'),
    (SELECT id FROM body_types WHERE slug='citadine'),
    'Gris Nardo',5,5,150,
    26500.00,1,1,'available','excellent',
    'Volkswagen Golf 8 dans le finition Style. Très faible kilométrage, véhicule de direction. Navigation Discover Pro, ACC, Lane Assist, Digital Cockpit Pro.',
    1,1,12,1,NOW()
),
(
    'audi-a3-sportback-tfsi-sline',
    (SELECT id FROM brands WHERE slug='audi'),
    'A3 Sportback','35 TFSI 150 S-Line',2021,35000,
    (SELECT id FROM fuel_types WHERE slug='essence'),
    (SELECT id FROM transmissions WHERE slug='automatique'),
    (SELECT id FROM body_types WHERE slug='citadine'),
    'Gris Daytona',5,5,150,
    27900.00,1,0,'available','excellent',
    'Audi A3 Sportback finition S-Line. Jantes 18\", Virtual Cockpit, MMI Navigation, sièges sport, pack lumières, radars avant-arrière.',
    1,1,6,1,NOW()
),
(
    'peugeot-3008-hybrid-allure-pack',
    (SELECT id FROM brands WHERE slug='peugeot'),
    '3008','Hybrid 225 e-EAT8 Allure Pack',2022,22000,
    (SELECT id FROM fuel_types WHERE slug='hybride'),
    (SELECT id FROM transmissions WHERE slug='automatique'),
    (SELECT id FROM body_types WHERE slug='suv'),
    'Blanc Nacré',5,5,225,
    32500.00,1,1,'available','excellent',
    'Peugeot 3008 Hybrid 225 ch. i-Cockpit 3D, Full LED, caméra 360°, hayon électrique. Consommation mixte 1.3L/100km.',
    1,1,12,1,NOW()
),
(
    'renault-clio-5-tce-zen',
    (SELECT id FROM brands WHERE slug='renault'),
    'Clio V','1.0 TCe 90 Zen',2021,28000,
    (SELECT id FROM fuel_types WHERE slug='essence'),
    (SELECT id FROM transmissions WHERE slug='manuelle'),
    (SELECT id FROM body_types WHERE slug='citadine'),
    'Rouge Flamme',5,5,90,
    13900.00,0,0,'available','very_good',
    'Renault Clio 5 en très bon état. Carnet suivi. Navigation MediaNav, climatisation automatique, régulateur de vitesse.',
    1,0,0,1,NOW()
),
(
    'toyota-yaris-hybrid-iconic',
    (SELECT id FROM brands WHERE slug='toyota'),
    'Yaris','Hybrid 116h Iconic',2022,15000,
    (SELECT id FROM fuel_types WHERE slug='hybride'),
    (SELECT id FROM transmissions WHERE slug='automatique'),
    (SELECT id FROM body_types WHERE slug='citadine'),
    'Bleu Bi-ton',5,5,116,
    18900.00,1,1,'available','excellent',
    'Toyota Yaris Hybride dans la finition Iconic. Toyota Safety Sense, Apple CarPlay/Android Auto, chargeur induction. Consommation 3.2L/100km.',
    1,1,24,1,NOW()
),
(
    'mercedes-benz-classe-c-220d-avantgarde',
    (SELECT id FROM brands WHERE slug='mercedes-benz'),
    'Classe C','220d Avantgarde',2020,55000,
    (SELECT id FROM fuel_types WHERE slug='diesel'),
    (SELECT id FROM transmissions WHERE slug='automatique'),
    (SELECT id FROM body_types WHERE slug='berline'),
    'Gris Sélénite',4,5,194,
    31900.00,1,0,'available','very_good',
    'Mercedes Classe C 220d Avantgarde. Pack AMG Line extérieur, MBUX Navigation, ambient light 64 couleurs, sièges cuir. Historique entretien Mercedes.',
    1,1,6,1,NOW()
),
(
    'ford-puma-ecoboost-st-line',
    (SELECT id FROM brands WHERE slug='ford'),
    'Puma','1.0 EcoBoost 125 mHEV ST-Line',2022,12000,
    (SELECT id FROM fuel_types WHERE slug='essence'),
    (SELECT id FROM transmissions WHERE slug='manuelle'),
    (SELECT id FROM body_types WHERE slug='crossover'),
    'Noir Agate',5,5,125,
    22500.00,0,1,'available','excellent',
    'Ford Puma ST-Line mild-hybrid. B&O Sound, toit panoramique, MegaBox, full LED, SYNC 4A, caméra recul.',
    1,1,12,1,NOW()
),
(
    'skoda-octavia-combi-tdi-style',
    (SELECT id FROM brands WHERE slug='skoda'),
    'Octavia Combi','2.0 TDI 150 Style DSG',2021,38000,
    (SELECT id FROM fuel_types WHERE slug='diesel'),
    (SELECT id FROM transmissions WHERE slug='automatique'),
    (SELECT id FROM body_types WHERE slug='break'),
    'Gris Quartz',5,5,150,
    24900.00,0,0,'available','excellent',
    'Skoda Octavia Combi idéale pour les familles. 640L de coffre, Virtual Cockpit, navigation Amundsen, ACC, Lane Assist. Révisée récemment.',
    1,0,0,1,NOW()
),
(
    'kia-sportage-hybrid-gt-line',
    (SELECT id FROM brands WHERE slug='kia'),
    'Sportage','1.6 T-GDi Hybrid GT-Line',2023,8000,
    (SELECT id FROM fuel_types WHERE slug='hybride'),
    (SELECT id FROM transmissions WHERE slug='automatique'),
    (SELECT id FROM body_types WHERE slug='suv'),
    'Vert Forêt',5,5,230,
    38500.00,1,1,'available','excellent',
    'Kia Sportage Hybride GT-Line dernière génération. Garantie constructeur restante. Double écran incurvé, Meridian Sound, toit panoramique, ACC.',
    1,1,36,1,NOW()
),
(
    'citroen-c5-aircross-shine-plug-in',
    (SELECT id FROM brands WHERE slug='citroen'),
    'C5 Aircross','Hybrid 225 Plug-in Shine',2022,19000,
    (SELECT id FROM fuel_types WHERE slug='hybride-rechargeable'),
    (SELECT id FROM transmissions WHERE slug='automatique'),
    (SELECT id FROM body_types WHERE slug='suv'),
    'Bleu Céleste',5,5,225,
    30500.00,0,0,'available','very_good',
    'Citroën C5 Aircross PHEV. Autonomie électrique ~55km. Sièges Advanced Comfort, suspension hydraulique Progressive, Grip Control.',
    1,1,6,1,NOW()
),
(
    'seat-leon-fr-etsi',
    (SELECT id FROM brands WHERE slug='seat'),
    'León','FR 1.5 eTSI 150 DSG',2022,14500,
    (SELECT id FROM fuel_types WHERE slug='essence'),
    (SELECT id FROM transmissions WHERE slug='automatique'),
    (SELECT id FROM body_types WHERE slug='berline'),
    'Rouge Desire',5,5,150,
    23900.00,1,1,'available','excellent',
    'Seat León FR mild-hybrid. Jantes 18" Copper, Digital Cockpit 10.25", navigation Full-Link, ACC, Full LED.',
    1,1,12,1,NOW()
);

-- Images de démonstration : NE PAS insérer de faux noms de fichiers
-- Les véhicules s'afficheront avec l'image placeholder SVG tant qu'aucune vraie photo n'est uploadée
-- Pour ajouter des photos : Administration > Véhicules > Modifier > section Photos

-- Témoignages
INSERT INTO `testimonials` (`author`, `location`, `rating`, `content`, `vehicle_ref`, `is_active`, `sort_order`) VALUES
('Thomas M.', 'Annemasse', 5, 'Service impeccable, véhicule exactement tel que décrit. L\'équipe a pris le temps de tout expliquer. Je recommande vivement La Bonne Occaz pour l\'achat d\'un véhicule d\'occasion.', 'Volkswagen Golf 8', 1, 1),
('Sophie L.', 'Genève', 5, 'Très bon accueil, dossier traité rapidement. La voiture était prête à temps, propre et vérifiée. Un vrai plaisir de faire affaire avec des professionnels sérieux.', 'BMW Série 3', 1, 2),
('Marc D.', 'Annecy', 4, 'Bonne expérience dans l\'ensemble. Véhicule bien présenté, vendeurs à l\'écoute. Je reviendrai pour mon prochain achat.', 'Peugeot 3008', 1, 3),
('Julie R.', 'Thonon-les-Bains', 5, 'Première voiture achetée en dépôt-vente, tout s\'est passé parfaitement. Merci pour l\'accompagnement dans les démarches administratives.', 'Renault Clio 5', 1, 4),
('Nicolas P.', 'Bonneville', 5, 'Excellent rapport qualité/prix. La voiture a été inspectée et révisée avant remise. Exactement ce que je cherchais.', 'Audi A3 Sportback', 1, 5);

-- Paramètres du site
INSERT IGNORE INTO `site_settings` (`setting_key`, `setting_val`, `setting_type`, `label`, `group_name`) VALUES
('site_name',         'La Bonne Occaz',                         'text',     'Nom du site',              'general'),
('site_tagline',      'Votre spécialiste en véhicules d\'occasion de qualité', 'text', 'Slogan du site', 'general'),
('contact_phone',     '04 50 XX XX XX',                         'text',     'Téléphone',                'contact'),
('contact_email',     'contact@labonneoccaz.fr',                'text',     'Email de contact',         'contact'),
('contact_address',   '12 Rue de l\'Industrie, 74100 Annemasse','text',     'Adresse',                  'contact'),
('contact_hours',     'Lun-Sam : 9h00 - 18h30',                 'text',     'Horaires',                 'contact'),
('whatsapp_number',   '33450XXXXXX',                            'text',     'Numéro WhatsApp',          'contact'),
('facebook_url',      '#',                                      'text',     'Facebook',                 'social'),
('instagram_url',     '#',                                      'text',     'Instagram',                'social'),
('youtube_url',       '',                                       'text',     'YouTube',                  'social'),
('hero_title',        'Votre prochaine voiture vous attend',    'text',     'Titre Hero',               'homepage'),
('hero_subtitle',     'Découvrez notre sélection de véhicules d\'occasion soigneusement sélectionnés', 'textarea', 'Sous-titre Hero', 'homepage'),
('about_text',        'La Bonne Occaz est votre partenaire de confiance pour l\'achat et la vente de véhicules d\'occasion. Notre équipe de professionnels passionnés vous accompagne à chaque étape.', 'textarea', 'Texte À propos', 'about'),
('google_maps_embed',  '',                                       'textarea', 'Code embed Google Maps',   'contact'),
('meta_title_home',   'La Bonne Occaz - Véhicules d\'occasion de qualité', 'text', 'Meta title accueil', 'seo'),
('meta_desc_home',    'La Bonne Occaz, votre spécialiste en véhicules d\'occasion multimarques. Large choix, prix transparents, service de qualité.', 'textarea', 'Meta description accueil', 'seo');
