<?php
// Image principale
$mainImage = null;
foreach ($images as $img) { if ($img['is_main']) { $mainImage = $img; break; } }
if (!$mainImage && !empty($images)) $mainImage = $images[0];

$imgUrl = $mainImage
    ? UPLOADS_URL . '/vehicles/' . $mainImage['filename']
    : BASE_URL . '/public/images/no-vehicle.svg';

$priceDisplay = !empty($vehicle['price_promo']) ? $vehicle['price_promo'] : $vehicle['price'];
$hasPromo     = !empty($vehicle['price_promo']) && $vehicle['price_promo'] < $vehicle['price'];

// Tri images : principale en 0, puis les autres (même ordre que vehicleImages JS)
$sortedImages = array_values(array_merge(
    array_filter($images, fn($i) => $i['is_main']),
    array_filter($images, fn($i) => !$i['is_main'])
));

// Schema.org enrichi pour les fiches véhicule
$schema = [
    '@context' => 'https://schema.org',
    '@type'    => 'Car',
    'name'     => ($vehicle['brand_name']??'').' '.$vehicle['model'].' '.($vehicle['version']??''),
    'brand'    => ['@type'=>'Brand','name'=>$vehicle['brand_name']??''],
    'modelDate'=> (string)$vehicle['year'],
    'mileageFromOdometer' => [
        '@type'    => 'QuantitativeValue',
        'value'    => (int)$vehicle['mileage'],
        'unitCode' => 'KMT',
    ],
    'fuelType'        => $vehicle['fuel_name'] ?? '',
    'vehicleTransmission' => $vehicle['transmission_name'] ?? '',
    'color'           => $vehicle['color'] ?? '',
    'numberOfDoors'   => !empty($vehicle['doors'])  ? (int)$vehicle['doors']  : null,
    'vehicleSeatingCapacity' => !empty($vehicle['seats']) ? (int)$vehicle['seats'] : null,
    'description'     => substr(strip_tags($vehicle['description'] ?? ''), 0, 300),
    'image'           => $imgUrl,
    'url'             => BASE_URL . '/vehicule/' . ($vehicle['slug'] ?? ''),
    'offers' => [
        '@type'         => 'Offer',
        'price'         => number_format((float)$vehicle['price'], 2, '.', ''),
        'priceCurrency' => 'EUR',
        'availability'  => $vehicle['status'] === 'available'
            ? 'https://schema.org/InStock'
            : 'https://schema.org/OutOfStock',
        'seller' => [
            '@type' => 'AutoDealer',
            'name'  => 'La Bonne Occaz',
            'url'   => BASE_URL,
            'telephone' => cfg_phone(),
            'address' => [
                '@type'           => 'PostalAddress',
                'streetAddress'   => cfg_address(),
                'addressCountry'  => 'FR',
            ],
        ],
    ],
];
// Nettoyer les valeurs null
$schema = array_filter($schema, fn($v) => $v !== null && $v !== '');
?>

<script type="application/ld+json"><?= json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>


<!-- Breadcrumb -->
<section class="page-hero page-hero--detail">
    <div class="container">
        <nav aria-label="breadcrumb" class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Accueil</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/vehicules">Véhicules</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars(($vehicle['brand_name']??'').' '.$vehicle['model']) ?></li>
            </ol>
        </nav>
    </div>
</section>

<!-- Contenu -->
<section class="vehicle-detail-section section-padding-sm">
<div class="container">
<div class="row g-5">

<!-- ══ COLONNE GAUCHE ══ -->
<div class="col-lg-8">

    <!-- ── Galerie ── -->
    <div class="vehicle-gallery">

        <!-- Image principale + contrôles -->
        <div class="gallery-main" id="galleryMain">
            <img src="<?= htmlspecialchars($imgUrl) ?>"
                 alt="<?= htmlspecialchars(($vehicle['brand_name']??'').' '.$vehicle['model']) ?>"
                 id="mainGalleryImg" class="gallery-main__img">

            <?php if ($vehicle['status']==='sold'): ?>
            <div class="gallery-sold-overlay"><span>Vendu</span></div>
            <?php elseif ($vehicle['status']==='reserved'): ?>
            <div class="gallery-reserved-overlay"><span>Réservé</span></div>
            <?php endif; ?>

            <?php if (count($sortedImages) > 1): ?>
            <button class="gallery-nav gallery-nav--prev" id="galleryPrev" aria-label="Précédent">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="gallery-nav gallery-nav--next" id="galleryNext" aria-label="Suivant">
                <i class="fas fa-chevron-right"></i>
            </button>
            <div class="gallery-counter" id="galleryCounter">1 / <?= count($sortedImages) ?></div>
            <?php endif; ?>

            <button class="gallery-fullscreen-btn" id="galleryFullscreenBtn" aria-label="Plein écran">
                <i class="fas fa-expand"></i>
            </button>
        </div>

        <!-- Miniatures -->
        <?php if (count($sortedImages) > 1): ?>
        <div class="gallery-thumbs" id="galleryThumbs">
            <?php foreach ($sortedImages as $i => $img): ?>
            <div class="gallery-thumb <?= $i===0?'gallery-thumb--active':'' ?>">
                <img src="<?= UPLOADS_URL ?>/vehicles/<?= htmlspecialchars($img['filename']) ?>"
                     alt="Photo <?= $i+1 ?>" loading="<?= $i<3?'eager':'lazy' ?>">
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div><!-- /vehicle-gallery -->

    <!-- ── En-tête véhicule ── -->
    <div class="vehicle-detail__header mt-4">
        <div class="vehicle-detail__badges">
            <?php if (!empty($vehicle['is_new_badge'])): ?>
            <span class="badge-vehicle badge-new">Nouveau</span>
            <?php endif; ?>
            <?php if ($vehicle['status']==='available'): ?>
            <span class="badge-vehicle badge-available">Disponible</span>
            <?php elseif ($vehicle['status']==='reserved'): ?>
            <span class="badge-vehicle badge-reserved">Réservé</span>
            <?php endif; ?>
            <?php if ($hasPromo): ?>
            <span class="badge-vehicle badge-promo">Promotion</span>
            <?php endif; ?>
        </div>
        <span class="vehicle-detail__brand"><?= htmlspecialchars($vehicle['brand_name']??'') ?></span>
        <h1 class="vehicle-detail__title"><?= htmlspecialchars($vehicle['model'].' '.($vehicle['version']??'')) ?></h1>
    </div>

    <!-- ── Specs rapides ── -->
    <div class="vehicle-specs-grid">
        <div class="spec-box"><i class="fas fa-calendar-alt"></i><span class="spec-label">Année</span><span class="spec-value"><?= $vehicle['year'] ?></span></div>
        <div class="spec-box"><i class="fas fa-tachometer-alt"></i><span class="spec-label">Kilométrage</span><span class="spec-value"><?= number_format($vehicle['mileage'],0,',',' ') ?> km</span></div>
        <div class="spec-box"><i class="fas fa-gas-pump"></i><span class="spec-label">Carburant</span><span class="spec-value"><?= htmlspecialchars($vehicle['fuel_name']??'') ?></span></div>
        <div class="spec-box"><i class="fas fa-cogs"></i><span class="spec-label">Boîte</span><span class="spec-value"><?= htmlspecialchars($vehicle['transmission_name']??'') ?></span></div>
        <?php if ($vehicle['power_hp']): ?><div class="spec-box"><i class="fas fa-bolt"></i><span class="spec-label">Puissance</span><span class="spec-value"><?= $vehicle['power_hp'] ?> ch</span></div><?php endif; ?>
        <?php if ($vehicle['color']): ?><div class="spec-box"><i class="fas fa-palette"></i><span class="spec-label">Couleur</span><span class="spec-value"><?= htmlspecialchars($vehicle['color']) ?></span></div><?php endif; ?>
        <?php if ($vehicle['doors']): ?><div class="spec-box"><i class="fas fa-door-open"></i><span class="spec-label">Portes</span><span class="spec-value"><?= $vehicle['doors'] ?></span></div><?php endif; ?>
        <?php if ($vehicle['seats']): ?><div class="spec-box"><i class="fas fa-chair"></i><span class="spec-label">Places</span><span class="spec-value"><?= $vehicle['seats'] ?></span></div><?php endif; ?>
    </div>

    <!-- ── Description ── -->
    <?php if ($vehicle['description']): ?>
    <div class="vehicle-description mt-4">
        <h2 class="detail-section-title"><i class="fas fa-align-left me-2"></i>Description</h2>
        <div class="description-text"><?= nl2br(htmlspecialchars($vehicle['description'])) ?></div>
    </div>
    <?php endif; ?>

    <!-- ── Caractéristiques ── -->
    <div class="vehicle-tech-specs mt-4">
        <h2 class="detail-section-title"><i class="fas fa-tools me-2"></i>Caractéristiques techniques</h2>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="tech-spec-group">
                    <h4>Informations générales</h4>
                    <table class="spec-table">
                        <tr><th>Marque</th><td><?= htmlspecialchars($vehicle['brand_name']??'') ?></td></tr>
                        <tr><th>Modèle</th><td><?= htmlspecialchars($vehicle['model']) ?></td></tr>
                        <?php if ($vehicle['version']): ?><tr><th>Version</th><td><?= htmlspecialchars($vehicle['version']) ?></td></tr><?php endif; ?>
                        <tr><th>Année</th><td><?= $vehicle['year'] ?></td></tr>
                        <tr><th>Kilométrage</th><td><?= number_format($vehicle['mileage'],0,',',' ') ?> km</td></tr>
                        <?php if ($vehicle['color']): ?><tr><th>Couleur</th><td><?= htmlspecialchars($vehicle['color']) ?></td></tr><?php endif; ?>
                        <?php if ($vehicle['body_type_name']): ?><tr><th>Carrosserie</th><td><?= htmlspecialchars($vehicle['body_type_name']) ?></td></tr><?php endif; ?>
                        <?php if ($vehicle['doors']): ?><tr><th>Portes</th><td><?= $vehicle['doors'] ?></td></tr><?php endif; ?>
                        <?php if ($vehicle['seats']): ?><tr><th>Places</th><td><?= $vehicle['seats'] ?></td></tr><?php endif; ?>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
                <div class="tech-spec-group">
                    <h4>Motorisation</h4>
                    <table class="spec-table">
                        <tr><th>Carburant</th><td><?= htmlspecialchars($vehicle['fuel_name']??'') ?></td></tr>
                        <tr><th>Transmission</th><td><?= htmlspecialchars($vehicle['transmission_name']??'') ?></td></tr>
                        <?php if ($vehicle['power_hp']): ?><tr><th>Puissance</th><td><?= $vehicle['power_hp'] ?> ch<?= $vehicle['power_kw']?' / '.$vehicle['power_kw'].' kW':'' ?></td></tr><?php endif; ?>
                        <?php if ($vehicle['engine_cc']): ?><tr><th>Cylindrée</th><td><?= number_format($vehicle['engine_cc'],0,',',' ') ?> cm³</td></tr><?php endif; ?>
                        <?php if ($vehicle['co2_emission']): ?><tr><th>CO₂</th><td><?= $vehicle['co2_emission'] ?> g/km</td></tr><?php endif; ?>
                        <?php if ($vehicle['energy_rating']): ?><tr><th>Classe énergie</th><td><?= htmlspecialchars($vehicle['energy_rating']) ?></td></tr><?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Équipements ── -->
    <?php if (!empty($equipment)): ?>
    <div class="vehicle-equipment mt-4">
        <h2 class="detail-section-title"><i class="fas fa-list-check me-2"></i>Équipements &amp; options</h2>
        <div class="equipment-grid">
            <?php foreach ($equipment as $item): ?>
            <div class="equipment-item"><i class="fas fa-check-circle text-success me-2"></i><?= htmlspecialchars($item) ?></div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- ── Informations de confiance ── -->
    <div class="vehicle-trust mt-4">
        <h2 class="detail-section-title"><i class="fas fa-shield-alt me-2"></i>Informations de confiance</h2>
        <div class="trust-grid">
            <div class="trust-item trust-item--<?= $vehicle['has_control_tech']?'yes':'no' ?>">
                <i class="fas fa-<?= $vehicle['has_control_tech']?'check-circle':'times-circle' ?>"></i><span>Contrôle technique</span>
            </div>
            <div class="trust-item trust-item--<?= $vehicle['has_service_history']?'yes':'no' ?>">
                <i class="fas fa-<?= $vehicle['has_service_history']?'check-circle':'times-circle' ?>"></i><span>Carnet d'entretien</span>
            </div>
            <div class="trust-item trust-item--<?= $vehicle['has_warranty']?'yes':'no' ?>">
                <i class="fas fa-<?= $vehicle['has_warranty']?'check-circle':'times-circle' ?>"></i>
                <span>Garantie<?= ($vehicle['has_warranty']&&$vehicle['warranty_months'])?' ('.$vehicle['warranty_months'].' mois)':'' ?></span>
            </div>
            <div class="trust-item trust-item--<?= in_array($vehicle['condition_state'],['excellent','very_good'])?'yes':'neutral' ?>">
                <i class="fas fa-star"></i>
                <span>État : <?= match($vehicle['condition_state']){'excellent'=>'Excellent','very_good'=>'Très bon','good'=>'Bon',default=>'Correct'} ?></span>
            </div>
        </div>
    </div>

</div><!-- /col-lg-8 -->

<!-- ══ COLONNE DROITE ══ -->
<div class="col-lg-4">
<div class="vehicle-sidebar sticky-sidebar">

    <!-- Prix -->
    <div class="sidebar-price-card">
        <?php if ($hasPromo): ?>
        <div class="price-old-sidebar"><?= number_format((float)$vehicle['price'],0,',',' ') ?> €</div>
        <?php endif; ?>
        <div class="price-main"><?= number_format((float)$priceDisplay,0,',',' ') ?> <span class="price-currency">€</span></div>
        <div class="price-ttc">Prix TTC · Financement disponible</div>
        <div class="quick-actions mt-3 d-grid gap-2">
            <a href="tel:<?= str_replace([' ','.','(',')','-'],'', cfg_phone()) ?>" class="btn btn-call">
                <i class="fas fa-phone-alt me-2"></i>Appeler maintenant
            </a>
            <a href="https://wa.me/<?= htmlspecialchars(cfg_wa()) ?>?text=<?= urlencode('Bonjour, intéressé par '.($vehicle['brand_name']??'').' '.$vehicle['model']) ?>"
               target="_blank" class="btn btn-whatsapp">
                <i class="fab fa-whatsapp me-2"></i>WhatsApp
            </a>
            <button class="btn btn-favorite-detail btn-favorite" data-id="<?= $vehicle['id'] ?>">
                <i class="fas fa-heart me-2"></i>Ajouter aux favoris
            </button>
        </div>
    </div>

    <!-- Formulaire contact -->
    <div class="sidebar-contact-card mt-3">
        <h3 class="sidebar-title"><i class="fas fa-envelope me-2"></i>Renseignements</h3>
        <?php if (!empty($flash)): ?>
        <div class="alert alert-<?= $flash['type']==='success'?'success':'danger' ?> py-2">
            <?= htmlspecialchars($flash['message']) ?>
        </div>
        <?php endif; ?>
        <form action="<?= BASE_URL ?>/contact/vehicle" method="POST" novalidate>
            <input type="hidden" name="csrf_token" value="<?php if(empty($_SESSION['csrf_token']))$_SESSION['csrf_token']=bin2hex(random_bytes(32));echo $_SESSION['csrf_token'];?>">
            <input type="hidden" name="vehicle_id"   value="<?= $vehicle['id'] ?>">
            <input type="hidden" name="vehicle_slug" value="<?= htmlspecialchars($vehicle['slug']) ?>">
            <div class="row g-2">
                <div class="col-6"><input type="text"  name="first_name" class="form-control form-control-sm" placeholder="Prénom *" required></div>
                <div class="col-6"><input type="text"  name="last_name"  class="form-control form-control-sm" placeholder="Nom *" required></div>
                <div class="col-12"><input type="email" name="email" class="form-control form-control-sm" placeholder="Email *" required></div>
                <div class="col-12"><input type="tel"   name="phone" class="form-control form-control-sm" placeholder="Téléphone"></div>
                <div class="col-12"><textarea name="message" class="form-control form-control-sm" rows="3" required>Je suis intéressé par ce véhicule. Pouvez-vous me contacter ?</textarea></div>
            </div>
            <div class="contact-type-tabs mt-2">
                <label class="contact-type-tab"><input type="radio" name="contact_type" value="vehicle_info" checked><span><i class="fas fa-info-circle me-1"></i>Infos</span></label>
                <label class="contact-type-tab"><input type="radio" name="contact_type" value="appointment"><span><i class="fas fa-calendar me-1"></i>RDV</span></label>
                <label class="contact-type-tab"><input type="radio" name="contact_type" value="financing"><span><i class="fas fa-euro-sign me-1"></i>Financement</span></label>
            </div>
            <button type="submit" class="btn btn-primary-custom w-100 mt-3">
                <i class="fas fa-paper-plane me-2"></i>Envoyer ma demande
            </button>
        </form>
    </div>

    <!-- Infos vendeur -->
    <div class="sidebar-seller-card mt-3">
        <div class="seller-info">
            <div class="seller-avatar"><i class="fas fa-store"></i></div>
            <div>
                <div class="seller-name">La Bonne Occaz</div>
                <div class="seller-location"><i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars(cfg_address()) ?></div>
                <div class="seller-hours"><i class="fas fa-clock me-1"></i><?= htmlspecialchars(cfg_hours()) ?></div>
            </div>
        </div>
    </div>

</div>
</div><!-- /col-lg-4 -->

</div><!-- /row -->

<!-- Véhicules similaires -->
<?php if (!empty($similar)): ?>
<div class="similar-vehicles mt-5">
    <div class="section-header"><h2 class="section-title">Véhicules similaires</h2></div>
    <div class="row g-4">
        <?php foreach ($similar as $v): ?>
            <?php include VIEWS_PATH . '/vehicles/_vehicle_card.php'; ?>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

</div><!-- /container -->
</section>

<!-- Modal galerie -->
<div class="gallery-modal" id="galleryModal" role="dialog">
    <div class="gallery-modal__overlay" id="galleryModalOverlay"></div>
    <div class="gallery-modal__content">
        <button class="gallery-modal__close" id="galleryModalClose" aria-label="Fermer"><i class="fas fa-times"></i></button>
        <img src="" alt="" id="galleryModalImg" class="gallery-modal__img">
        <button class="gallery-modal__nav gallery-modal__nav--prev" id="modalPrev"><i class="fas fa-chevron-left"></i></button>
        <button class="gallery-modal__nav gallery-modal__nav--next" id="modalNext"><i class="fas fa-chevron-right"></i></button>
        <div class="gallery-modal__counter" id="modalCounter"></div>
    </div>
</div>

<!-- Script galerie — tableau simple d'URLs -->
<script>
window.vehicleImages = [
    <?php foreach ($sortedImages as $img): ?>
    '<?= addslashes(UPLOADS_URL . '/vehicles/' . $img['filename']) ?>',
    <?php endforeach; ?>
];
</script>
