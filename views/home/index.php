<!-- ================================================================
     HERO — Slider automatique
================================================================ -->
<section class="hero-section" id="hero">

    <!-- Slides background -->
    <div class="hero-slides" aria-hidden="true">
        <div class="hero-slide hero-slide--active" style="background-image:url('<?= BASE_URL ?>/public/images/hero.jpg')"></div>
        <div class="hero-slide" style="background-image:url('<?= BASE_URL ?>/public/images/hero2.jpg')"></div>
        <div class="hero-slide" style="background-image:url('<?= BASE_URL ?>/public/images/hero3.webp')"></div>
        <div class="hero-slide" style="background-image:url('<?= BASE_URL ?>/public/images/hero4.webp')"></div>
        <div class="hero-slide" style="background-image:url('<?= BASE_URL ?>/public/images/hero5.webp')"></div>
        <div class="hero-slide" style="background-image:url('<?= BASE_URL ?>/public/images/hero6.webp')"></div>
        <div class="hero-slide" style="background-image:url('<?= BASE_URL ?>/public/images/hero7.webp')"></div>
        <div class="hero-slide" style="background-image:url('<?= BASE_URL ?>/public/images/hero8.jpg')"></div>
    </div>

    <div class="hero-bg-overlay"></div>

    <!-- Contenu -->
    <div class="container">
        <div class="hero-content">
            <div class="hero-badge">
                <i class="fas fa-star me-2"></i>Spécialiste véhicules d'occasion
            </div>
            <h1 class="hero-title">Votre prochaine voiture<br><span class="text-gradient">vous attend ici</span></h1>
            <p class="hero-subtitle">
                Découvrez notre sélection de véhicules d'occasion soigneusement contrôlés.<br>
                Prix transparents · Qualité garantie · Service personnalisé
            </p>
            <div class="hero-actions">
                <a href="<?= BASE_URL ?>/vehicules" class="btn btn-hero-primary">
                    <i class="fas fa-th-large me-2"></i>Découvrir nos véhicules
                    <span class="btn-arrow"><i class="fas fa-arrow-right"></i></span>
                </a>
                <a href="<?= BASE_URL ?>/contact" class="btn btn-hero-secondary">
                    <i class="fas fa-phone-alt me-2"></i>Nous contacter
                </a>
            </div>
            <div class="hero-stats">
                <div class="hero-stat">
                    <span class="stat-num"><?= count($featured ?? []) ?>+</span>
                    <span class="stat-label">Véhicules disponibles</span>
                </div>
                <div class="hero-stat">
                    <span class="stat-num">100%</span>
                    <span class="stat-label">Véhicules contrôlés</span>
                </div>
                <div class="hero-stat">
                    <span class="stat-num">6-18</span>
                    <span class="stat-label">Mois de garantie</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Indicateurs de slides -->
    <div class="hero-dots" aria-label="Navigation diaporama">
        <?php for ($i = 0; $i < 8; $i++): ?>
        <button class="hero-dot<?= $i === 0 ? ' hero-dot--active' : '' ?>"
                data-slide="<?= $i ?>" aria-label="Slide <?= $i + 1 ?>"></button>
        <?php endfor; ?>
    </div>

    <div class="hero-scroll-indicator">
        <div class="scroll-line"></div>
        <span>Défiler</span>
    </div>
</section>

<!-- ================================================================
     RECHERCHE RAPIDE
================================================================ -->
<section class="quick-search-section">
    <div class="container">
        <div class="quick-search-card">
            <h2 class="quick-search-title"><i class="fas fa-search me-2"></i>Recherche rapide</h2>
            <form action="<?= BASE_URL ?>/vehicules" method="GET" class="quick-search-form">
                <div class="row g-3 align-items-end">
                    <div class="col-md-2 col-6">
                        <label class="form-label">Marque</label>
                        <select name="brand_id" class="form-select">
                            <option value="">Toutes</option>
                            <?php foreach ($brands as $b): ?>
                            <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 col-6">
                        <label class="form-label">Budget min.</label>
                        <select name="price_min" class="form-select">
                            <option value="">Sans limite</option>
                            <?php foreach ([5000,10000,15000,20000,25000,30000,40000,50000] as $p): ?>
                            <option value="<?= $p ?>"><?= number_format($p,0,',',' ') ?> €</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 col-6">
                        <label class="form-label">Budget max.</label>
                        <select name="price_max" class="form-select">
                            <option value="">Sans limite</option>
                            <?php foreach ([10000,15000,20000,25000,30000,40000,50000,70000] as $p): ?>
                            <option value="<?= $p ?>"><?= number_format($p,0,',',' ') ?> €</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 col-6">
                        <label class="form-label">Carburant</label>
                        <select name="fuel_type_id" class="form-select">
                            <option value="">Tous</option>
                            <?php foreach ($fuel_types as $f): ?>
                            <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 col-6">
                        <label class="form-label">Transmission</label>
                        <select name="transmission_id" class="form-select">
                            <option value="">Toutes</option>
                            <?php foreach ($transmissions as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 col-12">
                        <button type="submit" class="btn btn-search-primary w-100">
                            <i class="fas fa-search me-2"></i>Rechercher
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- ================================================================
     VÉHICULES À LA UNE
================================================================ -->
<section class="featured-section section-padding" id="featured">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-badge">Sélection du moment</span>
            <h2 class="section-title">Véhicules à la une</h2>
            <p class="section-subtitle">Une sélection soigneuse des meilleures opportunités du moment</p>
        </div>
        <div class="row g-4" id="featuredGrid">
            <?php if (!empty($featured)): ?>
                <?php foreach ($featured as $v): ?>
                    <?php include VIEWS_PATH . '/vehicles/_vehicle_card.php'; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-car fa-3x text-muted mb-3 d-block"></i>
                    <p class="text-muted">Aucun véhicule en vedette pour le moment.</p>
                    <a href="<?= BASE_URL ?>/vehicules" class="btn btn-primary-custom">Voir tout le catalogue</a>
                </div>
            <?php endif; ?>
        </div>
        <div class="text-center mt-5">
            <a href="<?= BASE_URL ?>/vehicules" class="btn btn-outline-primary-custom btn-lg">
                <i class="fas fa-th-large me-2"></i>Voir tout le catalogue
                <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- ================================================================
     COMMENT ÇA MARCHE
================================================================ -->
<section class="how-section section-padding bg-light-custom" id="comment-ca-marche">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-badge">Simple & Transparent</span>
            <h2 class="section-title">Comment ça marche ?</h2>
            <p class="section-subtitle">De la réservation au service après-vente, nous vous accompagnons à chaque étape</p>
        </div>

        <div class="row g-4 mt-2">

            <!-- Étape 1 -->
            <div class="col-lg-3 col-md-6">
                <div class="how-step">
                    <div class="how-step__num">1</div>
                    <div class="how-step__icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3 class="how-step__title">Choisissez votre véhicule</h3>
                    <p class="how-step__desc">
                        Parcourez notre catalogue en ligne ou venez directement en show-room. 
                        Chaque fiche véhicule détaille l'historique, le contrôle technique et les équipements.
                    </p>
                </div>
            </div>

            <!-- Étape 2 -->
            <div class="col-lg-3 col-md-6">
                <div class="how-step">
                    <div class="how-step__num">2</div>
                    <div class="how-step__icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3 class="how-step__title">Réservez &amp; essayez</h3>
                    <p class="how-step__desc">
                        Réservez le véhicule en ligne ou par téléphone, puis venez l'essayer à votre convenance. 
                        Notre conseiller reste disponible pour répondre à toutes vos questions.
                    </p>
                </div>
            </div>

            <!-- Étape 3 -->
            <div class="col-lg-3 col-md-6">
                <div class="how-step">
                    <div class="how-step__num">3</div>
                    <div class="how-step__icon">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <h3 class="how-step__title">Finalisez en toute sécurité</h3>
                    <p class="how-step__desc">
                        Contrat clair, paiement sécurisé, remise des clés avec tous les documents. 
                        Nous gérons les démarches administratives (carte grise, certificat de cession) pour vous.
                    </p>
                </div>
            </div>

            <!-- Étape 4 -->
            <div class="col-lg-3 col-md-6">
                <div class="how-step how-step--highlight">
                    <div class="how-step__num">4</div>
                    <div class="how-step__icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="how-step__title">Service après-vente &amp; garantie</h3>
                    <p class="how-step__desc">
                        Votre véhicule est couvert par une <strong>garantie de 6 à 12 mois</strong>. 
                        En cas de panne, notre mécanicien partenaire le plus proche est immédiatement 
                        dépêché vers vous. Votre sérénité est notre priorité.
                    </p>
                    <div class="how-step__badge">
                        <i class="fas fa-tools me-1"></i>Intervention rapide
                    </div>
                </div>
            </div>

        </div>

        <!-- Bande de réassurance -->
        <div class="how-reassurance mt-5">
            <div class="row g-3 text-center">
                <div class="col-6 col-md-3">
                    <div class="reassurance-item">
                        <i class="fas fa-clock text-primary"></i>
                        <span>Réponse sous 2h</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="reassurance-item">
                        <i class="fas fa-tools text-primary"></i>
                        <span>Mécanicien à domicile</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="reassurance-item">
                        <i class="fas fa-shield-alt text-primary"></i>
                        <span>Garantie 6 à 12 mois</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="reassurance-item">
                        <i class="fas fa-headset text-primary"></i>
                        <span>Support 7j/7</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- ================================================================
     POURQUOI NOUS CHOISIR
================================================================ -->
<section class="why-section section-padding" id="about">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="why-image-wrapper">
                    <img src="<?= BASE_URL ?>/public/images/propos.png"
                         alt="La Bonne Occaz - Notre équipe"
                         class="why-image"
                         loading="lazy">
                    <div class="why-image-badge">
                        <div class="badge-content">
                            <span class="badge-num">+500</span>
                            <span class="badge-text">clients satisfaits</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="why-content">
                    <span class="section-badge">Pourquoi nous choisir ?</span>
                    <h2 class="section-title">La confiance, notre priorité</h2>
                    <p class="lead-text">
                        Chez La Bonne Occaz, chaque véhicule est soigneusement sélectionné
                        et contrôlé avant d'être proposé à la vente.
                    </p>
                    <div class="why-list">
                        <?php
                        $whys = [
                            ['icon'=>'fa-check-circle', 'color'=>'success',
                             'title'=>'Transparence totale',
                             'desc' =>'Historique complet, rapport d\'inspection, aucune mauvaise surprise.'],
                            ['icon'=>'fa-user-tie',     'color'=>'primary',
                             'title'=>'Accompagnement personnalisé',
                             'desc' =>'Une équipe à votre écoute du premier contact à la remise des clés.'],
                            ['icon'=>'fa-tools',        'color'=>'warning',
                             'title'=>'Véhicules contrôlés',
                             'desc' =>'Chaque véhicule passe un contrôle technique multi-points avant mise en vente.'],
                            ['icon'=>'fa-shield-alt',   'color'=>'info',
                             'title'=>'Garantie 6 à 18 mois incluse',
                             'desc' =>'Toutes nos voitures sont couvertes par une garantie pièces &amp; main d\'œuvre de 6 à 18 mois selon le modèle. En cas de panne, notre mécanicien se déplace chez vous.'],
                            ['icon'=>'fa-lock',         'color'=>'danger',
                             'title'=>'Transactions sécurisées',
                             'desc' =>'Paiements sécurisés, contrats conformes à la législation en vigueur.'],
                        ];
                        foreach ($whys as $why): ?>
                        <div class="why-item">
                            <div class="why-icon text-<?= $why['color'] ?>">
                                <i class="fas <?= $why['icon'] ?>"></i>
                            </div>
                            <div class="why-text">
                                <h4><?= $why['title'] ?></h4>
                                <p><?= $why['desc'] ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================================================================
     MARQUES
================================================================ -->
<?php if (!empty($brands)): ?>
<section class="brands-section section-padding-sm bg-light-custom">
    <div class="container">
        <div class="section-header text-center mb-4">
            <h2 class="section-title-sm">Nos marques disponibles</h2>
        </div>
        <div class="brands-slider">
            <?php foreach ($brands as $b): ?>
            <a href="<?= BASE_URL ?>/vehicules?brand_id=<?= $b['id'] ?>" class="brand-item" title="<?= htmlspecialchars($b['name']) ?>">
                <span class="brand-name"><?= htmlspecialchars($b['name']) ?></span>
                <?php if ($b['vehicle_count'] > 0): ?>
                <span class="brand-count"><?= $b['vehicle_count'] ?></span>
                <?php endif; ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ================================================================
     TÉMOIGNAGES (avec photo de profil)
================================================================ -->
<?php if (!empty($testimonials)): ?>
<section class="testimonials-section section-padding">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-badge">Ce que disent nos clients</span>
            <h2 class="section-title">Témoignages</h2>
        </div>
        <div class="row g-4">
            <?php foreach ($testimonials as $t): ?>
            <div class="col-lg-4 col-md-6">
                <div class="testimonial-card">
                    <div class="testimonial-stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star<?= $i > $t['rating'] ? ' opacity-25' : '' ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <blockquote class="testimonial-text">"<?= htmlspecialchars($t['content']) ?>"</blockquote>
                    <div class="testimonial-author">
                        <!-- Photo de profil ou initiale -->
                        <?php if (!empty($t['photo_url'])): ?>
                        <div class="author-avatar author-avatar--photo">
                            <img src="<?= htmlspecialchars($t['photo_url']) ?>"
                                 alt="<?= htmlspecialchars($t['author']) ?>"
                                 loading="lazy">
                        </div>
                        <?php else: ?>
                        <div class="author-avatar">
                            <?= strtoupper(mb_substr($t['author'], 0, 1)) ?>
                        </div>
                        <?php endif; ?>
                        <div>
                            <div class="author-name"><?= htmlspecialchars($t['author']) ?></div>
                            <?php if ($t['location']): ?>
                            <div class="author-location">
                                <i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($t['location']) ?>
                            </div>
                            <?php endif; ?>
                            <?php if ($t['vehicle_ref']): ?>
                            <div class="author-vehicle">
                                <i class="fas fa-car me-1"></i><?= htmlspecialchars($t['vehicle_ref']) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ================================================================
     CONDITIONS DE RÉSERVATION
================================================================ -->
<section class="reservation-info-section section-padding bg-light-custom">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-badge"><i class="fas fa-calendar-check me-1"></i>Réservation</span>
            <h2 class="section-title">Comment réserver un véhicule ?</h2>
            <p class="section-subtitle">Tout ce que vous devez savoir avant de réserver</p>
        </div>

        <div class="row g-4 mt-2">

            <!-- Bloc acompte -->
            <div class="col-lg-4 col-md-6">
                <div class="res-info-card res-info-card--deposit">
                    <div class="res-info-card__icon">
                        <i class="fas fa-euro-sign"></i>
                    </div>
                    <h3 class="res-info-card__title">Acompte de réservation</h3>
                    <div class="res-info-card__value"><?= htmlspecialchars($reservationDeposit ?? '300') ?> €</div>
                    <p class="res-info-card__desc">
                        Un acompte est demandé pour confirmer votre réservation.
                        Il sera déduit du prix total lors de l'achat.
                    </p>
                </div>
            </div>

            <!-- Bloc durée -->
            <div class="col-lg-4 col-md-6">
                <div class="res-info-card res-info-card--duration">
                    <div class="res-info-card__icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 class="res-info-card__title">Durée de maintien</h3>
                    <div class="res-info-card__value"><?= htmlspecialchars($reservationHoldHours ?? '72') ?>h</div>
                    <p class="res-info-card__desc">
                        Le véhicule est bloqué pour vous pendant cette durée après confirmation de votre réservation.
                    </p>
                </div>
            </div>

            <!-- Bloc documents -->
            <div class="col-lg-4 col-md-12">
                <div class="res-info-card res-info-card--docs">
                    <div class="res-info-card__icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <h3 class="res-info-card__title">Documents requis</h3>
                    <div class="res-info-card__value"><i class="fas fa-check-circle text-success" style="font-size:1.6rem"></i></div>
                    <p class="res-info-card__desc">
                        Présentez-vous avec une pièce d'identité valide et confirmez votre réservation dans les 24h.
                    </p>
                </div>
            </div>

        </div>

        <?php if (!empty($reservationConditions)): ?>
        <!-- Conditions complètes (repliables) -->
        <div class="res-conditions-block mt-5">
            <div class="res-conditions-block__header" id="resConditionsToggle" role="button" aria-expanded="false">
                <span><i class="fas fa-file-alt me-2"></i>Lire les conditions complètes de réservation</span>
                <i class="fas fa-chevron-down res-conditions-block__chevron"></i>
            </div>
            <div class="res-conditions-block__body" id="resConditionsBody">
                <div class="res-conditions-text">
                    <?= nl2br(htmlspecialchars($reservationConditions)) ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- CTA réservation -->
        <div class="text-center mt-5">
            <a href="<?= BASE_URL ?>/vehicules" class="btn btn-primary-custom btn-lg">
                <i class="fas fa-search me-2"></i>Voir les véhicules disponibles
            </a>
        </div>

    </div>
</section>

<!-- ================================================================
     CALL TO ACTION (sans "Vendre le mien")
================================================================ -->
<section class="cta-section">
    <div class="container">
        <div class="cta-card">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <h2 class="cta-title">Prêt à trouver votre prochain véhicule ?</h2>
                    <p class="cta-text">Notre équipe est disponible pour vous accompagner. Contactez-nous pour un rendez-vous ou une estimation gratuite.</p>
                </div>
                <div class="col-lg-5 text-lg-end">
                    <div class="cta-actions">
                        <a href="<?= BASE_URL ?>/vehicules" class="btn btn-cta-primary">
                            <i class="fas fa-search me-2"></i>Voir les véhicules
                        </a>
                        <a href="<?= BASE_URL ?>/contact" class="btn btn-cta-secondary">
                            <i class="fas fa-envelope me-2"></i>Nous contacter
                        </a>
                        <a href="tel:<?= str_replace([' ','.','(',')','-'],'', cfg_phone()) ?>" class="btn btn-cta-phone">
                            <i class="fas fa-phone-alt me-2"></i><?= htmlspecialchars(cfg_phone()) ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
