<?php
$mainImg = null;
foreach ($images as $img) { if ($img['is_main']) { $mainImg = $img; break; } }
if (!$mainImg && !empty($images)) $mainImg = $images[0];
$imgUrl = $mainImg ? UPLOADS_URL.'/vehicles/'.$mainImg['filename'] : BASE_URL.'/public/images/no-vehicle.svg';
?>

<section class="page-hero page-hero--detail">
    <div class="container">
        <nav aria-label="breadcrumb" class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Accueil</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/vehicules">Véhicules</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/vehicule/<?= htmlspecialchars($vehicle['slug']) ?>"><?= htmlspecialchars(($vehicle['brand_name']??'').' '.$vehicle['model']) ?></a></li>
                <li class="breadcrumb-item active">Réservation</li>
            </ol>
        </nav>
        <h1 class="page-hero__title"><i class="fas fa-calendar-check me-2"></i>Réserver ce véhicule</h1>
    </div>
</section>

<section class="section-padding-sm">
<div class="container">
<div class="row g-5">

    <!-- ── Colonne gauche : aperçu véhicule + conditions ── -->
    <div class="col-lg-7">

        <!-- Aperçu véhicule -->
        <div class="reservation-vehicle-card mb-4">
            <div class="reservation-vehicle-card__img">
                <img src="<?= htmlspecialchars($imgUrl) ?>"
                     alt="<?= htmlspecialchars(($vehicle['brand_name']??'').' '.$vehicle['model']) ?>">
            </div>
            <div class="reservation-vehicle-card__info">
                <div class="reservation-vehicle-card__brand"><?= htmlspecialchars($vehicle['brand_name']??'') ?></div>
                <h2 class="reservation-vehicle-card__title"><?= htmlspecialchars($vehicle['model'].' '.($vehicle['version']??'')) ?></h2>
                <div class="reservation-vehicle-card__specs">
                    <span><i class="fas fa-calendar-alt me-1"></i><?= $vehicle['year'] ?></span>
                    <span><i class="fas fa-tachometer-alt me-1"></i><?= number_format($vehicle['mileage'],0,',',' ') ?> km</span>
                    <span><i class="fas fa-gas-pump me-1"></i><?= htmlspecialchars($vehicle['fuel_name']??'') ?></span>
                </div>
                <div class="reservation-vehicle-card__price">
                    <?= number_format((float)$vehicle['price'],0,',',' ') ?> €
                    <small class="text-muted">Prix TTC</small>
                </div>
            </div>
        </div>

        <!-- Conditions de réservation -->
        <div class="reservation-conditions-card mb-4">
            <div class="reservation-conditions-card__header">
                <i class="fas fa-file-contract me-2"></i>
                Conditions de réservation
            </div>
            <div class="reservation-conditions-card__body">
                <div class="reservation-conditions-text">
                    <?= nl2br(htmlspecialchars($conditions ?? '')) ?>
                </div>

                <?php if (!empty($deposit)): ?>
                <div class="reservation-deposit-badge mt-3">
                    <i class="fas fa-euro-sign me-2"></i>
                    Acompte requis : <strong><?= htmlspecialchars($deposit) ?> €</strong>
                    <span class="text-muted small ms-2">(non remboursable en cas de désistement)</span>
                </div>
                <?php endif; ?>

                <?php if (!empty($holdHours)): ?>
                <div class="reservation-hold-badge mt-2">
                    <i class="fas fa-clock me-2"></i>
                    Véhicule réservé pendant <strong><?= htmlspecialchars($holdHours) ?> heures</strong> après confirmation
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($deliveryConditions)): ?>
        <!-- Conditions de livraison -->
        <div class="reservation-conditions-card">
            <div class="reservation-conditions-card__header" style="background:#1d4ed8">
                <i class="fas fa-truck me-2"></i>
                Conditions de livraison
            </div>
            <div class="reservation-conditions-card__body">
                <div class="reservation-conditions-text">
                    <?= nl2br(htmlspecialchars($deliveryConditions)) ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <!-- ── Colonne droite : formulaire ── -->
    <div class="col-lg-5">
        <div class="reservation-form-card">
            <div class="reservation-form-card__header">
                <i class="fas fa-user-check me-2"></i>
                Vos coordonnées
            </div>
            <div class="reservation-form-card__body">

                <?php if (!empty($flash)): ?>
                <div class="alert alert-<?= $flash['type']==='success'?'success':'danger' ?> mb-3">
                    <?= htmlspecialchars($flash['message']) ?>
                </div>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>/reserver/submit/<?= htmlspecialchars($vehicle['slug']) ?>"
                      method="POST" id="reservationForm" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf ?? '') ?>">

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Prénom <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" required autofocus
                                   value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" required
                                   value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Téléphone <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control" required
                                   value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Message <small class="text-muted">(facultatif)</small></label>
                            <textarea name="message" class="form-control" rows="3"
                                      placeholder="Questions, disponibilités, financement..."><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                        </div>

                        <!-- Acceptation des conditions de réservation -->
                        <div class="col-12">
                            <div class="reservation-accept-block">
                                <label class="reservation-accept-label">
                                    <input type="checkbox" name="conditions_accepted" value="1" id="conditionsCheck" required>
                                    <span class="reservation-accept-text">
                                        J'ai lu et j'accepte les <strong>conditions de réservation</strong> ci-contre,
                                        notamment le versement d'un acompte de
                                        <strong><?= htmlspecialchars($deposit ?? '300') ?> €</strong>
                                        pour confirmer ma réservation.
                                    </span>
                                </label>
                                <div class="reservation-accept-warning" id="conditionsWarning" style="display:none">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Vous devez accepter les conditions pour continuer.
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($deliveryConditions)): ?>
                        <!-- Acceptation des conditions de livraison -->
                        <div class="col-12">
                            <div class="reservation-accept-block" style="border-color:#bfdbfe;background:#eff6ff;">
                                <label class="reservation-accept-label">
                                    <input type="checkbox" name="delivery_accepted" value="1" id="deliveryCheck" required>
                                    <span class="reservation-accept-text" style="color:#1e3a5f">
                                        J'ai lu et j'accepte les <strong>conditions de livraison</strong> ci-contre,
                                        notamment les frais de livraison entre <strong>150 € et 300 €</strong>
                                        selon la distance, réglés par virement bancaire.
                                    </span>
                                </label>
                                <div class="reservation-accept-warning" id="deliveryWarning" style="display:none">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Vous devez accepter les conditions de livraison pour continuer.
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary-custom btn-lg w-100" id="submitReservation">
                                <i class="fas fa-calendar-check me-2"></i>
                                Confirmer ma réservation
                            </button>
                            <p class="text-muted small text-center mt-2">
                                <i class="fas fa-lock me-1"></i>
                                Réservation sans paiement immédiat — notre équipe vous contactera sous 24h
                            </p>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lien retour -->
        <div class="text-center mt-3">
            <a href="<?= BASE_URL ?>/vehicule/<?= htmlspecialchars($vehicle['slug']) ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour à la fiche
            </a>
        </div>
    </div>

</div>
</div>
</section>

<script>
document.getElementById('reservationForm')?.addEventListener('submit', function(e) {
    let blocked = false;

    const cb   = document.getElementById('conditionsCheck');
    const warn = document.getElementById('conditionsWarning');
    if (cb && !cb.checked) {
        warn.style.display = 'block';
        cb.closest('.reservation-accept-block').scrollIntoView({ behavior: 'smooth', block: 'center' });
        blocked = true;
    }

    const cb2   = document.getElementById('deliveryCheck');
    const warn2 = document.getElementById('deliveryWarning');
    if (cb2 && !cb2.checked) {
        warn2.style.display = 'block';
        if (!blocked) cb2.closest('.reservation-accept-block').scrollIntoView({ behavior: 'smooth', block: 'center' });
        blocked = true;
    }

    if (blocked) e.preventDefault();
});

document.getElementById('conditionsCheck')?.addEventListener('change', function() {
    document.getElementById('conditionsWarning').style.display = this.checked ? 'none' : 'block';
});
document.getElementById('deliveryCheck')?.addEventListener('change', function() {
    document.getElementById('deliveryWarning').style.display = this.checked ? 'none' : 'block';
});
</script>
