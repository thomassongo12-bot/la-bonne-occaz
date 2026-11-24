</main><!-- /main-content -->

<!-- ================================================================
     FOOTER
================================================================ -->
<footer class="site-footer">
    <div class="footer-top">
        <div class="container">
            <div class="row g-5">

                <!-- Col 1 : Logo + Présentation -->
                <div class="col-lg-4">
                    <div class="footer-brand">
                        <div class="logo-wrapper mb-3">
                            <span class="logo-icon"><i class="fas fa-car-side"></i></span>
                            <div class="logo-text">
                                <span class="logo-name">La Bonne Occaz</span>
                                <span class="logo-tagline">Véhicules d'occasion</span>
                            </div>
                        </div>
                        <p class="footer-desc">
                            Votre spécialiste en véhicules d'occasion multimarques. 
                            Des véhicules soigneusement sélectionnés, contrôlés et garantis 
                            pour votre tranquillité d'esprit.
                        </p>
                        <!-- Réseaux sociaux : TikTok + WhatsApp uniquement -->
                        <?php
                        try {
                            $_fs = isset($_siteSettings) ? $_siteSettings : new SiteSettings();
                            $_tk = $_fs->get('tiktok_url');
                        } catch(Exception $e) {
                            $_tk = '';
                        }
                        ?>
                        <div class="social-links mt-3">
                            <?php if (!empty($_tk)): ?>
                            <a href="<?= htmlspecialchars($_tk) ?>" class="social-link social-link--tiktok" target="_blank" rel="noopener noreferrer" aria-label="TikTok">
                                <i class="fab fa-tiktok"></i>
                            </a>
                            <?php else: ?>
                            <span class="social-link social-link--tiktok" style="opacity:.4;cursor:default" title="TikTok non configuré">
                                <i class="fab fa-tiktok"></i>
                            </span>
                            <?php endif; ?>
                            <a href="https://wa.me/<?= htmlspecialchars(cfg_wa()) ?>" class="social-link social-link--whatsapp" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Col 2 : Navigation -->
                <div class="col-lg-2 col-md-4">
                    <h5 class="footer-title">Navigation</h5>
                    <ul class="footer-links">
                        <li><a href="<?= BASE_URL ?>/">Accueil</a></li>
                        <li><a href="<?= BASE_URL ?>/vehicules">Nos véhicules</a></li>
                        <li><a href="<?= BASE_URL ?>/vendre">Vendre / Dépôt-vente</a></li>
                        <li><a href="<?= BASE_URL ?>/favoris">Mes favoris</a></li>
                        <li><a href="<?= BASE_URL ?>/contact">Contact</a></li>
                    </ul>
                </div>

                <!-- Col 3 : Services -->
                <div class="col-lg-2 col-md-4">
                    <h5 class="footer-title">Nos services</h5>
                    <ul class="footer-links">
                        <li><a href="<?= BASE_URL ?>/vehicules">Achat de véhicule</a></li>
                        <li><a href="<?= BASE_URL ?>/vendre">Vente de véhicule</a></li>
                        <li><a href="<?= BASE_URL ?>/vendre">Dépôt-vente</a></li>
                        <li><a href="<?= BASE_URL ?>/contact">Estimation gratuite</a></li>
                        <li><a href="<?= BASE_URL ?>/contact">Accompagnement admin.</a></li>
                    </ul>
                </div>

                <!-- Col 4 : Contact -->
                <div class="col-lg-4 col-md-4">
                    <h5 class="footer-title">Nous contacter</h5>
                    <ul class="footer-contact-list">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?= htmlspecialchars(cfg_address()) ?></span>
                        </li>
                        <li>
                            <i class="fas fa-phone-alt"></i>
                            <a href="tel:<?= str_replace([' ','.','(',')','-'],'', cfg_phone()) ?>"><?= htmlspecialchars(cfg_phone()) ?></a>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:<?= htmlspecialchars(cfg_email()) ?>"><?= htmlspecialchars(cfg_email()) ?></a>
                        </li>
                        <li>
                            <i class="fas fa-clock"></i>
                            <span><?= htmlspecialchars(cfg_hours()) ?></span>
                        </li>
                    </ul>
                    <a href="https://wa.me/<?= htmlspecialchars(cfg_wa()) ?>" class="btn btn-whatsapp mt-3" target="_blank">
                        <i class="fab fa-whatsapp me-2"></i>Discuter sur WhatsApp
                    </a>
                </div>

            </div>
        </div>
    </div>

    <!-- Footer bottom -->
    <div class="footer-bottom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?= date('Y') ?> La Bonne Occaz. Tous droits réservés.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="<?= BASE_URL ?>/mentions-legales" class="footer-legal-link">Mentions légales</a>
                    <span class="mx-2">·</span>
                    <a href="<?= BASE_URL ?>/confidentialite" class="footer-legal-link">Confidentialité</a>
                    <span class="mx-2">·</span>
                    <a href="<?= BASE_URL ?>/cgv" class="footer-legal-link">CGV</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- JS principal -->
<script>
    const BASE_URL = '<?= BASE_URL ?>';
    const UPLOADS_URL = '<?= UPLOADS_URL ?>';
</script>
<script src="<?= BASE_URL ?>/public/js/main.js?v=<?= filemtime(ROOT_PATH.'/public/js/main.js') ?>"></script>
<!-- Galerie véhicule — chargée après main.js pour éviter les conflits d'opacity -->
<script src="<?= BASE_URL ?>/public/js/vehicle-detail.js?v=<?= filemtime(ROOT_PATH.'/public/js/vehicle-detail.js') ?>"></script>

<!-- Bouton retour en haut -->
<button class="back-to-top" id="backToTop" aria-label="Retour en haut">
    <i class="fas fa-chevron-up"></i>
</button>

</body>
</html>
