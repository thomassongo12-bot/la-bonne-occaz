<section class="page-hero page-hero--contact">
    <div class="container">
        <nav aria-label="breadcrumb" class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Accueil</a></li>
                <li class="breadcrumb-item active">Contact</li>
            </ol>
        </nav>
        <h1 class="page-hero__title">Contactez-nous</h1>
        <p class="page-hero__subtitle">Notre équipe est à votre écoute</p>
    </div>
</section>

<section class="contact-section section-padding">
    <div class="container">
        <div class="row g-5">

            <!-- Formulaire -->
            <div class="col-lg-7">
                <div class="contact-form-card">
                    <h2 class="contact-form-title"><i class="fas fa-paper-plane me-2"></i>Envoyez-nous un message</h2>
                    <form action="<?= BASE_URL ?>/contact/send" method="POST" id="contactForm" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf ?? '') ?>">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Prénom <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control" placeholder="Votre prénom" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control" placeholder="Votre nom" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="votre@email.fr" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Téléphone</label>
                                <input type="tel" name="phone" class="form-control" placeholder="06 XX XX XX XX">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Message <span class="text-danger">*</span></label>
                                <textarea name="message" class="form-control" rows="6"
                                    placeholder="Décrivez votre demande..." required minlength="10"></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary-custom btn-lg w-100">
                                    <i class="fas fa-paper-plane me-2"></i>Envoyer le message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Infos contact -->
            <div class="col-lg-5">
                <div class="contact-info-card">
                    <h2 class="contact-info-title">Nos coordonnées</h2>

                    <div class="contact-info-list">
                        <div class="contact-info-item">
                            <div class="contact-info-icon"><i class="fas fa-map-marker-alt"></i></div>
                            <div>
                                <strong>Adresse</strong>
                                <p><?= htmlspecialchars(cfg_address()) ?></p>
                            </div>
                        </div>
                        <div class="contact-info-item">
                            <div class="contact-info-icon"><i class="fas fa-phone-alt"></i></div>
                            <div>
                                <strong>Téléphone</strong>
                                <p><a href="tel:<?= str_replace([' ','.','(',')','-'],'', cfg_phone()) ?>"><?= htmlspecialchars(cfg_phone()) ?></a></p>
                            </div>
                        </div>
                        <div class="contact-info-item">
                            <div class="contact-info-icon"><i class="fas fa-envelope"></i></div>
                            <div>
                                <strong>Email</strong>
                                <p><a href="mailto:<?= htmlspecialchars(cfg_email()) ?>"><?= htmlspecialchars(cfg_email()) ?></a></p>
                            </div>
                        </div>
                        <div class="contact-info-item">
                            <div class="contact-info-icon"><i class="fas fa-clock"></i></div>
                            <div>
                                <strong>Horaires d'ouverture</strong>
                                <p><?= htmlspecialchars(cfg_hours()) ?></p>
                                <p class="text-muted small">Fermé le dimanche</p>
                            </div>
                        </div>
                    </div>

                    <!-- Réseaux sociaux : TikTok + WhatsApp uniquement -->
                    <div class="contact-social mt-4">
                        <h4>Suivez-nous</h4>
                        <?php
                        try { $_cs = new SiteSettings(); $_ctk = $_cs->get('tiktok_url'); }
                        catch(Exception $e) { $_ctk = ''; }
                        ?>
                        <div class="social-links">
                            <?php if (!empty($_ctk)): ?>
                            <a href="<?= htmlspecialchars($_ctk) ?>" class="social-link social-link--tiktok" target="_blank" rel="noopener noreferrer" aria-label="TikTok">
                                <i class="fab fa-tiktok"></i>
                            </a>
                            <?php else: ?>
                            <span class="social-link social-link--tiktok" style="opacity:.35;cursor:default" title="TikTok non configuré">
                                <i class="fab fa-tiktok"></i>
                            </span>
                            <?php endif; ?>
                            <a href="https://wa.me/<?= htmlspecialchars(cfg_wa()) ?>" class="social-link social-link--whatsapp" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Bouton WhatsApp -->
                    <a href="https://wa.me/<?= htmlspecialchars(cfg_wa()) ?>?text=<?= urlencode('Bonjour, je souhaite un renseignement.') ?>" target="_blank" class="btn btn-whatsapp w-100 mt-3">
                        <i class="fab fa-whatsapp me-2"></i>Chatter sur WhatsApp
                    </a>
                </div>

                <!-- Carte Google Maps / OpenStreetMap embed -->
                <div class="map-card mt-4">
                    <iframe
                        src="https://www.openstreetmap.org/export/embed.html?bbox=6.2%2C46.19%2C6.3%2C46.22&layer=mapnik"
                        width="100%" height="250"
                        style="border:0; border-radius: 12px;"
                        loading="lazy"
                        title="Localisation La Bonne Occaz">
                    </iframe>
                    <small class="text-muted d-block mt-1">
                        <a href="https://www.openstreetmap.org/?mlat=46.2&mlon=6.25#map=14/46.2/6.25" target="_blank">
                            Voir sur OpenStreetMap
                        </a>
                    </small>
                </div>
            </div>
        </div>
    </div>
</section>
