<?php
// Pré-extraire les valeurs branding
$logoUrl    = '';
$faviconUrl = '';
$logoText   = 'La Bonne Occaz';
foreach ($groups['branding'] ?? [] as $s) {
    if ($s['setting_key'] === 'logo_url')    $logoUrl    = $s['setting_val'] ?? '';
    if ($s['setting_key'] === 'favicon_url') $faviconUrl = $s['setting_val'] ?? '';
    if ($s['setting_key'] === 'logo_text')   $logoText   = $s['setting_val'] ?? 'La Bonne Occaz';
}
?>

<form action="<?= BASE_URL ?>/admin/parametres/save" method="POST"
      id="settingsForm" enctype="multipart/form-data" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf ?? '') ?>">

    <!-- ================================================================
         LOGO & FAVICON
    ================================================================ -->
    <div class="admin-card mb-4">
        <div class="admin-card__header">
            <h3><i class="fas fa-image me-2 text-primary"></i>Logo &amp; Favicon</h3>
        </div>
        <div class="admin-card__body">
            <div class="row g-4">

                <!-- ── Logo ── -->
                <div class="col-lg-6">
                    <div class="settings-upload-block">
                        <div class="settings-upload-block__title">
                            <i class="fas fa-image me-2"></i>Logo du site
                        </div>
                        <p class="settings-upload-block__hint">PNG, SVG, WebP — max 2 Mo · Idéal : 300×80 px fond transparent</p>

                        <!-- Aperçu existant -->
                        <?php if ($logoUrl): ?>
                        <div class="settings-current-file" id="logoCurrentBox">
                            <div class="settings-current-file__preview settings-current-file__preview--logo">
                                <img src="<?= htmlspecialchars($logoUrl) ?>" alt="Logo actuel">
                            </div>
                            <div class="settings-current-file__info">
                                <span class="settings-current-file__tag">
                                    <i class="fas fa-check-circle text-success me-1"></i>Logo actuel
                                </span>
                                <label class="settings-current-file__remove">
                                    <input type="checkbox" name="remove_logo_url" value="1" id="removeLogo">
                                    <span><i class="fas fa-trash me-1"></i>Supprimer</span>
                                </label>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Zone d'upload -->
                        <div class="settings-dropzone <?= $logoUrl ? 'settings-dropzone--compact' : '' ?>"
                             id="logoDropzone">
                            <i class="fas fa-cloud-upload-alt settings-dropzone__icon"></i>
                            <div class="settings-dropzone__text">
                                Glissez ou
                                <label for="upload_logo_url" class="settings-dropzone__link">parcourir</label>
                            </div>
                            <input type="file" id="upload_logo_url" name="upload_logo_url"
                                   accept=".jpg,.jpeg,.png,.svg,.webp" class="d-none">
                        </div>

                        <!-- Aperçu nouveau fichier -->
                        <div class="settings-new-preview d-none" id="logoNewPreview">
                            <img src="" alt="Aperçu logo" class="settings-new-preview__img--logo">
                            <div>
                                <div class="settings-new-preview__name" id="logoNewName"></div>
                                <small class="text-success"><i class="fas fa-check me-1"></i>Prêt à uploader</small>
                                <button type="button" class="settings-new-preview__cancel" id="logoCancelBtn">
                                    <i class="fas fa-times me-1"></i>Annuler
                                </button>
                            </div>
                        </div>

                        <!-- Texte de fallback -->
                        <div class="mt-3">
                            <label class="form-label small text-muted">
                                <i class="fas fa-font me-1"></i>Texte affiché si pas d'image
                            </label>
                            <input type="text" name="logo_text" class="form-control form-control-sm"
                                   value="<?= htmlspecialchars($logoText) ?>" placeholder="La Bonne Occaz">
                        </div>
                    </div>
                </div>

                <!-- ── Favicon ── -->
                <div class="col-lg-6">
                    <div class="settings-upload-block">
                        <div class="settings-upload-block__title">
                            <i class="fas fa-star me-2"></i>Favicon
                        </div>
                        <p class="settings-upload-block__hint">ICO, PNG, SVG — max 2 Mo · Idéal : 32×32 ou 64×64 px</p>

                        <?php if ($faviconUrl): ?>
                        <div class="settings-current-file" id="faviconCurrentBox">
                            <div class="settings-current-file__preview settings-current-file__preview--favicon">
                                <img src="<?= htmlspecialchars($faviconUrl) ?>" alt="Favicon actuel">
                            </div>
                            <div class="settings-current-file__info">
                                <span class="settings-current-file__tag">
                                    <i class="fas fa-check-circle text-success me-1"></i>Favicon actuel
                                </span>
                                <label class="settings-current-file__remove">
                                    <input type="checkbox" name="remove_favicon_url" value="1" id="removeFavicon">
                                    <span><i class="fas fa-trash me-1"></i>Supprimer</span>
                                </label>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="settings-dropzone settings-dropzone--favicon <?= $faviconUrl ? 'settings-dropzone--compact' : '' ?>"
                             id="faviconDropzone">
                            <i class="fas fa-star settings-dropzone__icon"></i>
                            <div class="settings-dropzone__text">
                                Glissez ou
                                <label for="upload_favicon_url" class="settings-dropzone__link">parcourir</label>
                            </div>
                            <input type="file" id="upload_favicon_url" name="upload_favicon_url"
                                   accept=".ico,.png,.svg" class="d-none">
                        </div>

                        <div class="settings-new-preview d-none" id="faviconNewPreview">
                            <img src="" alt="Aperçu favicon" class="settings-new-preview__img--favicon">
                            <div>
                                <div class="settings-new-preview__name" id="faviconNewName"></div>
                                <small class="text-success"><i class="fas fa-check me-1"></i>Prêt à uploader</small>
                                <button type="button" class="settings-new-preview__cancel" id="faviconCancelBtn">
                                    <i class="fas fa-times me-1"></i>Annuler
                                </button>
                            </div>
                        </div>

                        <div class="settings-info-box mt-3">
                            <i class="fas fa-info-circle me-1"></i>
                            Apparaît dans l'onglet du navigateur. Videz le cache après upload.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- ================================================================
         AUTRES GROUPES
    ================================================================ -->
    <?php
    $groupConfig = [
        'general'     => ['fa-cog',          '#6366f1', 'Général'],
        'contact'     => ['fa-phone-alt',    '#10b981', 'Coordonnées'],
        'homepage'    => ['fa-home',         '#f59e0b', 'Page d\'accueil'],
        'reservation' => ['fa-calendar-check','#e63946', 'Réservation'],
        'social'      => ['fa-share-alt',    '#ec4899', 'Réseaux sociaux'],
        'about'       => ['fa-info-circle',  '#3b82f6', 'À propos'],
        'seo'         => ['fa-search',       '#8b5cf6', 'SEO'],
    ];
    $socialIcons = [
        'facebook_url'    => ['fab fa-facebook-f',  '#1877f2'],
        'instagram_url'   => ['fab fa-instagram',   '#e1306c'],
        'youtube_url'     => ['fab fa-youtube',     '#ff0000'],
        'tiktok_url'      => ['fab fa-tiktok',      '#010101'],
        'whatsapp_number' => ['fab fa-whatsapp',    '#25d366'],
    ];

    foreach ($groups as $groupName => $settings):
        if ($groupName === 'branding') continue;
        $cfg   = $groupConfig[$groupName] ?? ['fa-cog', '#6b7280', ucfirst($groupName)];
        $icon  = $cfg[0]; $color = $cfg[1]; $label = $cfg[2];
    ?>
    <div class="admin-card mb-4">
        <div class="admin-card__header">
            <h3>
                <span class="settings-group-icon" style="background:<?= $color ?>20;color:<?= $color ?>">
                    <i class="fas <?= $icon ?>"></i>
                </span>
                <?= $label ?>
            </h3>
        </div>
        <div class="admin-card__body">
            <div class="row g-3">
                <?php
                // Clés à masquer (supprimées du site)
                $hiddenKeys = ['reservation_deposit', 'reservation_hold_hours'];
                foreach ($settings as $s):
                    if (in_array($s['setting_key'], $hiddenKeys)) continue;
                    $si = $socialIcons[$s['setting_key']] ?? null;
                ?>
                <div class="col-md-6">
                    <label class="form-label d-flex align-items-center gap-2 mb-1">
                        <?php if ($si): ?>
                        <span class="settings-social-dot" style="background:<?= $si[1] ?>">
                            <i class="<?= $si[0] ?>"></i>
                        </span>
                        <?php endif; ?>
                        <span><?= htmlspecialchars($s['label']) ?></span>
                    </label>

                    <?php if ($s['setting_type'] === 'textarea'): ?>
                    <textarea name="<?= htmlspecialchars($s['setting_key']) ?>"
                              class="form-control" rows="3"><?= htmlspecialchars($s['setting_val'] ?? '') ?></textarea>

                    <?php elseif ($s['setting_type'] === 'boolean'): ?>
                    <div class="form-check form-switch mt-1">
                        <input class="form-check-input" type="checkbox"
                               name="<?= htmlspecialchars($s['setting_key']) ?>"
                               id="s_<?= $s['setting_key'] ?>"
                               <?= !empty($s['setting_val']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="s_<?= $s['setting_key'] ?>">Activer</label>
                    </div>

                    <?php elseif ($s['setting_type'] === 'image'): ?>
                        <!-- géré dans la section Branding -->

                    <?php elseif ($groupName === 'social'): ?>
                    <div class="input-group input-group-sm">
                        <?php if ($si): ?>
                        <span class="input-group-text" style="background:<?= $si[1] ?>15;border-color:<?= $si[1] ?>40">
                            <i class="<?= $si[0] ?>" style="color:<?= $si[1] ?>"></i>
                        </span>
                        <?php endif; ?>
                        <input type="url" name="<?= htmlspecialchars($s['setting_key']) ?>"
                               class="form-control"
                               placeholder="https://..."
                               value="<?= htmlspecialchars($s['setting_val'] ?? '') ?>">
                    </div>

                    <?php else: ?>
                    <input type="text" name="<?= htmlspecialchars($s['setting_key']) ?>"
                           class="form-control"
                           value="<?= htmlspecialchars($s['setting_val'] ?? '') ?>">
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- ================================================================
         MOT DE PASSE
    ================================================================ -->
    <div class="admin-card mb-4">
        <div class="admin-card__header">
            <h3>
                <span class="settings-group-icon" style="background:#ef444420;color:#ef4444">
                    <i class="fas fa-lock"></i>
                </span>
                Changer le mot de passe
            </h3>
        </div>
        <div class="admin-card__body">
            <form action="<?= BASE_URL ?>/admin/parametres/password" method="POST" id="passwordForm">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf ?? '') ?>">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Mot de passe actuel</label>
                        <div class="input-group">
                            <input type="password" name="current_password" class="form-control" required id="pwdCurrent">
                            <button type="button" class="input-group-text bg-white" onclick="togglePwd('pwdCurrent',this)">
                                <i class="fas fa-eye text-muted"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nouveau mot de passe</label>
                        <div class="input-group">
                            <input type="password" name="new_password" class="form-control" minlength="8" required id="pwdNew">
                            <button type="button" class="input-group-text bg-white" onclick="togglePwd('pwdNew',this)">
                                <i class="fas fa-eye text-muted"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Confirmer le nouveau</label>
                        <div class="input-group">
                            <input type="password" name="confirm_password" class="form-control" required id="pwdConfirm">
                            <button type="button" class="input-group-text bg-white" onclick="togglePwd('pwdConfirm',this)">
                                <i class="fas fa-eye text-muted"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" form="passwordForm" class="btn btn-warning">
                            <i class="fas fa-key me-2"></i>Changer le mot de passe
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bouton save sticky -->
    <div class="sticky-bottom-actions">
        <button type="submit" form="settingsForm" class="btn btn-primary-custom btn-lg">
            <i class="fas fa-save me-2"></i>Enregistrer tous les paramètres
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {

    /* ── Upload générique logo/favicon ── */
    function initUpload({ dropzoneId, inputId, previewId, nameId, cancelId, removeId }) {
        const zone    = document.getElementById(dropzoneId);
        const input   = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        const nameEl  = document.getElementById(nameId);
        const cancel  = document.getElementById(cancelId);
        const remove  = document.getElementById(removeId);
        if (!zone || !input) return;

        // Clic sur zone
        zone.addEventListener('click', () => input.click());

        // Drag & drop
        zone.addEventListener('dragover',  e => { e.preventDefault(); zone.classList.add('dz-active'); });
        zone.addEventListener('dragleave', ()  => zone.classList.remove('dz-active'));
        zone.addEventListener('drop', e => {
            e.preventDefault(); zone.classList.remove('dz-active');
            if (e.dataTransfer.files.length) {
                const dt = new DataTransfer();
                dt.items.add(e.dataTransfer.files[0]);
                input.files = dt.files;
                showPreview(input.files[0]);
            }
        });

        // Changement fichier
        input.addEventListener('change', () => { if (input.files.length) showPreview(input.files[0]); });

        // Annuler nouvel upload
        cancel?.addEventListener('click', () => {
            input.value = '';
            if (preview) preview.classList.add('d-none');
        });

        // Case supprimer → désactive le dropzone
        remove?.addEventListener('change', function() {
            input.disabled = this.checked;
            zone.style.opacity = this.checked ? '.4' : '1';
            zone.style.pointerEvents = this.checked ? 'none' : '';
        });

        // Validation taille
        input.addEventListener('change', () => {
            if (input.files.length && input.files[0].size > 2 * 1024 * 1024) {
                alert('Fichier trop volumineux (max 2 Mo)');
                input.value = '';
            }
        });

        function showPreview(file) {
            if (!preview) return;
            const img = preview.querySelector('img');
            if (!img) return;
            const reader = new FileReader();
            reader.onload = e => {
                img.src = e.target.result;
                if (nameEl) nameEl.textContent = file.name + ' (' + (file.size/1024).toFixed(0) + ' Ko)';
                preview.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        }
    }

    initUpload({
        dropzoneId: 'logoDropzone',    inputId: 'upload_logo_url',
        previewId:  'logoNewPreview',  nameId:  'logoNewName',
        cancelId:   'logoCancelBtn',   removeId: 'removeLogo'
    });
    initUpload({
        dropzoneId: 'faviconDropzone',    inputId: 'upload_favicon_url',
        previewId:  'faviconNewPreview',  nameId:  'faviconNewName',
        cancelId:   'faviconCancelBtn',   removeId: 'removeFavicon'
    });
});

/* ── Show/hide password ── */
function togglePwd(inputId, btn) {
    const inp  = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (inp.type === 'password') {
        inp.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        inp.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
