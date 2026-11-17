<?php
/**
 * Carte véhicule réutilisable
 * @var array $v - données du véhicule
 */
$imgUrl   = !empty($v['main_image'])
    ? UPLOADS_URL . '/vehicles/' . $v['main_image']
    : BASE_URL . '/public/images/no-vehicle.svg';

$isFav    = false; // géré en JS
$priceDisplay = !empty($v['price_promo']) ? $v['price_promo'] : $v['price'];
$hasPromo     = !empty($v['price_promo']) && $v['price_promo'] < $v['price'];
?>
<div class="col-xl-4 col-md-6">
    <article class="vehicle-card" data-vehicle-id="<?= $v['id'] ?>">

        <!-- Image -->
        <div class="vehicle-card__image-wrapper">
            <a href="<?= BASE_URL ?>/vehicule/<?= htmlspecialchars($v['slug']) ?>">
                <img src="<?= htmlspecialchars($imgUrl) ?>"
                     alt="<?= htmlspecialchars(($v['brand_name'] ?? '') . ' ' . $v['model']) ?>"
                     class="vehicle-card__image"
                     loading="lazy"
                     >
            </a>

            <!-- Badges -->
            <div class="vehicle-card__badges">
                <?php if ($v['status'] === 'reserved'): ?>
                    <span class="badge-vehicle badge-reserved">Réservé</span>
                <?php elseif (!empty($v['is_new_badge'])): ?>
                    <span class="badge-vehicle badge-new">Nouveau</span>
                <?php endif; ?>
                <?php if ($hasPromo): ?>
                    <span class="badge-vehicle badge-promo">Promo</span>
                <?php endif; ?>
            </div>

            <!-- Favori -->
            <button class="vehicle-card__fav-btn btn-favorite"
                    data-id="<?= $v['id'] ?>"
                    aria-label="Ajouter aux favoris"
                    title="Ajouter aux favoris">
                <i class="fas fa-heart"></i>
            </button>
        </div>

        <!-- Infos -->
        <div class="vehicle-card__body">
            <div class="vehicle-card__brand"><?= htmlspecialchars($v['brand_name'] ?? '') ?></div>
            <h3 class="vehicle-card__model">
                <a href="<?= BASE_URL ?>/vehicule/<?= htmlspecialchars($v['slug']) ?>">
                    <?= htmlspecialchars($v['model'] . ' ' . ($v['version'] ?? '')) ?>
                </a>
            </h3>

            <!-- Specs -->
            <div class="vehicle-card__specs">
                <span class="spec-item" title="Année">
                    <i class="fas fa-calendar-alt"></i><?= $v['year'] ?>
                </span>
                <span class="spec-item" title="Kilométrage">
                    <i class="fas fa-tachometer-alt"></i><?= number_format($v['mileage'], 0, ',', ' ') ?> km
                </span>
                <span class="spec-item" title="Carburant">
                    <i class="fas fa-gas-pump"></i><?= htmlspecialchars($v['fuel_name'] ?? '') ?>
                </span>
                <span class="spec-item" title="Transmission">
                    <i class="fas fa-cogs"></i><?= htmlspecialchars($v['transmission_name'] ?? '') ?>
                </span>
            </div>

            <!-- Prix -->
            <div class="vehicle-card__price-row">
                <div class="vehicle-card__price">
                    <?php if ($hasPromo): ?>
                        <span class="price-old"><?= number_format((float)$v['price'], 0, ',', ' ') ?> €</span>
                        <span class="price-current"><?= number_format((float)$priceDisplay, 0, ',', ' ') ?> €</span>
                    <?php else: ?>
                        <span class="price-current"><?= number_format((float)$v['price'], 0, ',', ' ') ?> €</span>
                    <?php endif; ?>
                </div>
                <a href="<?= BASE_URL ?>/vehicule/<?= htmlspecialchars($v['slug']) ?>" class="btn btn-card-cta">
                    Voir <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </article>
</div>

