<?php $isEdit = !empty($vehicle); ?>

<form action="<?= BASE_URL ?>/admin/vehicules/<?= $isEdit ? 'update/'.$vehicle['id'] : 'create' ?>"
      method="POST" enctype="multipart/form-data" id="vehicleForm" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

    <div class="row g-4">
        <!-- Colonne principale -->
        <div class="col-lg-8">

            <!-- Infos de base -->
            <div class="admin-card mb-4">
                <div class="admin-card__header"><h3><i class="fas fa-info-circle me-2"></i>Informations générales</h3></div>
                <div class="admin-card__body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Marque <span class="text-danger">*</span></label>
                            <select name="brand_id" class="form-select" required>
                                <option value="">Sélectionner...</option>
                                <?php foreach ($brands as $b): ?>
                                <option value="<?= $b['id'] ?>" <?= (isset($vehicle['brand_id']) && $vehicle['brand_id'] == $b['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($b['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Modèle <span class="text-danger">*</span></label>
                            <input type="text" name="model" class="form-control" required
                                   value="<?= htmlspecialchars($vehicle['model'] ?? '') ?>" placeholder="Ex : Golf, Série 3...">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Version / Finition</label>
                            <input type="text" name="version" class="form-control"
                                   value="<?= htmlspecialchars($vehicle['version'] ?? '') ?>" placeholder="Ex : 1.5 TDI 150 Comfortline DSG">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Année <span class="text-danger">*</span></label>
                            <input type="number" name="year" class="form-control" required
                                   min="1990" max="<?= date('Y')+1 ?>"
                                   value="<?= $vehicle['year'] ?? date('Y') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Kilométrage <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="mileage" class="form-control" min="0" required
                                       value="<?= $vehicle['mileage'] ?? 0 ?>">
                                <span class="input-group-text">km</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Carburant <span class="text-danger">*</span></label>
                            <select name="fuel_type_id" class="form-select" required>
                                <option value="">Choisir...</option>
                                <?php foreach ($fuel_types as $f): ?>
                                <option value="<?= $f['id'] ?>" <?= (isset($vehicle['fuel_type_id']) && $vehicle['fuel_type_id'] == $f['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($f['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Transmission <span class="text-danger">*</span></label>
                            <select name="transmission_id" class="form-select" required>
                                <option value="">Choisir...</option>
                                <?php foreach ($transmissions as $t): ?>
                                <option value="<?= $t['id'] ?>" <?= (isset($vehicle['transmission_id']) && $vehicle['transmission_id'] == $t['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Type de carrosserie</label>
                            <select name="body_type_id" class="form-select">
                                <option value="">Choisir...</option>
                                <?php foreach ($body_types as $bt): ?>
                                <option value="<?= $bt['id'] ?>" <?= (isset($vehicle['body_type_id']) && $vehicle['body_type_id'] == $bt['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($bt['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Couleur</label>
                            <input type="text" name="color" class="form-control"
                                   value="<?= htmlspecialchars($vehicle['color'] ?? '') ?>" placeholder="Ex : Blanc Alpinweiss">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Portes</label>
                            <input type="number" name="doors" class="form-control" min="2" max="6"
                                   value="<?= $vehicle['doors'] ?? '' ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Places</label>
                            <input type="number" name="seats" class="form-control" min="1" max="9"
                                   value="<?= $vehicle['seats'] ?? '' ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Motorisation -->
            <div class="admin-card mb-4">
                <div class="admin-card__header"><h3><i class="fas fa-bolt me-2"></i>Motorisation</h3></div>
                <div class="admin-card__body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Puissance (ch)</label>
                            <input type="number" name="power_hp" class="form-control" min="0"
                                   value="<?= $vehicle['power_hp'] ?? '' ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Puissance (kW)</label>
                            <input type="number" name="power_kw" class="form-control" min="0"
                                   value="<?= $vehicle['power_kw'] ?? '' ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Cylindrée (cm³)</label>
                            <input type="number" name="engine_cc" class="form-control" min="0"
                                   value="<?= $vehicle['engine_cc'] ?? '' ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">CO₂ (g/km)</label>
                            <input type="number" name="co2_emission" class="form-control" min="0"
                                   value="<?= $vehicle['co2_emission'] ?? '' ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Classe énergie</label>
                            <select name="energy_rating" class="form-select">
                                <option value="">--</option>
                                <?php foreach (['A','B','C','D','E','F','G'] as $r): ?>
                                <option value="<?= $r ?>" <?= (isset($vehicle['energy_rating']) && $vehicle['energy_rating']===$r) ? 'selected':'' ?>><?= $r ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="admin-card mb-4">
                <div class="admin-card__header"><h3><i class="fas fa-align-left me-2"></i>Description</h3></div>
                <div class="admin-card__body">
                    <textarea name="description" class="form-control" rows="8"
                              placeholder="Description détaillée du véhicule..."><?= htmlspecialchars($vehicle['description'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Équipements -->
            <div class="admin-card mb-4">
                <div class="admin-card__header d-flex justify-content-between align-items-center">
                    <h3><i class="fas fa-list-check me-2"></i>Équipements & options</h3>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addEquipmentBtn">
                        <i class="fas fa-plus me-1"></i>Ajouter
                    </button>
                </div>
                <div class="admin-card__body">
                    <div id="equipmentList">
                        <?php
                        $equip = !empty($vehicle['equipment']) ? json_decode($vehicle['equipment'], true) : [];
                        if (is_array($equip)):
                            foreach ($equip as $item): ?>
                        <div class="equipment-row d-flex gap-2 mb-2">
                            <input type="text" name="equipment[]" class="form-control" value="<?= htmlspecialchars($item) ?>">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-equipment"><i class="fas fa-times"></i></button>
                        </div>
                        <?php endforeach; endif; ?>
                    </div>
                    <p class="text-muted small mt-2">Ex : Navigation GPS, Caméra de recul, Toit panoramique...</p>
                </div>
            </div>

            <!-- SEO -->
            <div class="admin-card mb-4">
                <div class="admin-card__header"><h3><i class="fas fa-search me-2"></i>SEO</h3></div>
                <div class="admin-card__body">
                    <div class="mb-3">
                        <label class="form-label">Meta title <small class="text-muted">(max 160 car.)</small></label>
                        <input type="text" name="meta_title" class="form-control" maxlength="160"
                               value="<?= htmlspecialchars($vehicle['meta_title'] ?? '') ?>">
                    </div>
                    <div>
                        <label class="form-label">Meta description <small class="text-muted">(max 320 car.)</small></label>
                        <textarea name="meta_description" class="form-control" rows="2" maxlength="320"><?= htmlspecialchars($vehicle['meta_description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

        </div>

        <!-- Colonne latérale -->
        <div class="col-lg-4">

            <!-- Prix & statut -->
            <div class="admin-card mb-4">
                <div class="admin-card__header"><h3><i class="fas fa-euro-sign me-2"></i>Prix & statut</h3></div>
                <div class="admin-card__body">
                    <div class="mb-3">
                        <label class="form-label">Prix <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="price" class="form-control" min="0" step="100" required
                                   value="<?= $vehicle['price'] ?? '' ?>">
                            <span class="input-group-text">€</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prix promotionnel</label>
                        <div class="input-group">
                            <input type="number" name="price_promo" class="form-control" min="0" step="100"
                                   value="<?= $vehicle['price_promo'] ?? '' ?>">
                            <span class="input-group-text">€</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Statut</label>
                        <select name="status" class="form-select">
                            <?php foreach (['draft'=>'Brouillon','available'=>'Disponible','reserved'=>'Réservé','sold'=>'Vendu'] as $k=>$l): ?>
                            <option value="<?= $k ?>" <?= (isset($vehicle['status']) && $vehicle['status']===$k) ? 'selected':'' ?>><?= $l ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">État du véhicule</label>
                        <select name="condition_state" class="form-select">
                            <?php foreach (['excellent'=>'Excellent','very_good'=>'Très bon','good'=>'Bon','fair'=>'Correct'] as $k=>$l): ?>
                            <option value="<?= $k ?>" <?= (isset($vehicle['condition_state']) && $vehicle['condition_state']===$k) ? 'selected':'' ?>><?= $l ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-checks">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="isFeatured"
                                   <?= !empty($vehicle['is_featured']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isFeatured">Mettre en vedette</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_new_badge" id="isNew"
                                   <?= !empty($vehicle['is_new_badge']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isNew">Badge "Nouveau"</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents / Garantie -->
            <div class="admin-card mb-4">
                <div class="admin-card__header"><h3><i class="fas fa-shield-alt me-2"></i>Confiance & garantie</h3></div>
                <div class="admin-card__body">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="has_control_tech" id="ctCheck"
                               <?= !empty($vehicle['has_control_tech']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="ctCheck">Contrôle technique OK</label>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="has_service_history" id="shCheck"
                               <?= !empty($vehicle['has_service_history']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="shCheck">Carnet d'entretien</label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="has_warranty" id="warCheck"
                               <?= !empty($vehicle['has_warranty']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="warCheck">Garantie incluse</label>
                    </div>
                    <div id="warrantyMonthsField">
                        <label class="form-label">Durée garantie (mois)</label>
                        <input type="number" name="warranty_months" class="form-control" min="1" max="60"
                               value="<?= $vehicle['warranty_months'] ?? '' ?>">
                    </div>
                </div>
            </div>

            <!-- Images -->
            <div class="admin-card mb-4">
            <!-- ============================================================
                 PHOTOS
            ============================================================ -->
            <div class="admin-card mb-4">
                <div class="admin-card__header">
                    <h3><i class="fas fa-images me-2"></i>Photos du véhicule</h3>
                </div>
                <div class="admin-card__body">

                    <!-- ── PHOTO PRINCIPALE ─────────────────────────────── -->
                    <div class="photo-section mb-4">
                        <div class="photo-section__label">
                            <span class="photo-section__badge photo-section__badge--main">
                                <i class="fas fa-star me-1"></i>Photo principale
                            </span>
                            <small class="text-muted ms-2">Affichée en tête de fiche véhicule et dans le catalogue</small>
                        </div>

                        <?php
                        // Trouver l'image principale existante
                        $mainImg   = null;
                        $otherImgs = [];
                        foreach ($images as $img) {
                            if ($img['is_main']) $mainImg = $img;
                            else $otherImgs[] = $img;
                        }
                        ?>

                        <div class="main-photo-wrapper mt-2">
                            <!-- Aperçu existant -->
                            <?php if ($mainImg): ?>
                            <div class="main-photo-current" id="mainPhotoCurrent">
                                <div class="main-photo-preview">
                                    <img src="<?= UPLOADS_URL ?>/vehicles/<?= htmlspecialchars($mainImg['filename']) ?>"
                                         alt="Photo principale" id="mainPhotoPreviewImg">
                                    <span class="main-photo-label"><i class="fas fa-star me-1"></i>Actuelle</span>
                                </div>
                                <div class="main-photo-actions">
                                    <label class="btn btn-sm btn-outline-primary" for="main_image_input">
                                        <i class="fas fa-sync-alt me-1"></i>Remplacer
                                    </label>
                                    <a href="<?= BASE_URL ?>/admin/vehicules/deleteImage/<?= $mainImg['id'] ?>?vehicle_id=<?= $vehicle['id'] ?? '' ?>"
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Supprimer la photo principale ?')">
                                        <i class="fas fa-trash me-1"></i>Supprimer
                                    </a>
                                </div>
                            </div>
                            <?php else: ?>
                            <div id="mainPhotoEmpty" class="main-photo-empty <?= isset($newMainPreview) ? 'd-none' : '' ?>">
                                <i class="fas fa-image fa-2x text-muted mb-2 d-block"></i>
                                <span class="text-muted small">Aucune photo principale</span>
                            </div>
                            <?php endif; ?>

                            <!-- Zone de drop -->
                            <div class="main-upload-zone <?= $mainImg ? 'mt-2' : 'mt-0' ?>" id="mainUploadZone">
                                <i class="fas fa-cloud-upload-alt me-2"></i>
                                <span>Glissez ici ou
                                    <label for="main_image_input" class="text-primary fw-semibold" style="cursor:pointer">
                                        sélectionner une photo
                                    </label>
                                </span>
                                <span class="text-muted small ms-2">JPG, PNG, WebP — max 5 Mo</span>
                                <input type="file" id="main_image_input" name="main_image"
                                       accept="image/jpeg,image/png,image/webp" class="d-none">
                            </div>

                            <!-- Aperçu nouvelle photo principale (avant save) -->
                            <div id="mainNewPreview" class="main-new-preview d-none">
                                <img src="" alt="Nouvelle photo principale" id="mainNewPreviewImg">
                                <div class="main-new-preview__info">
                                    <i class="fas fa-check-circle text-success me-1"></i>
                                    <span id="mainNewPreviewName" class="small fw-semibold"></span>
                                    <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-2"
                                            id="mainNewPreviewRemove">
                                        <i class="fas fa-times"></i> Annuler
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <!-- ── GALERIE (photos supplémentaires) ─────────────── -->
                    <div class="photo-section">
                        <div class="photo-section__label">
                            <span class="photo-section__badge photo-section__badge--gallery">
                                <i class="fas fa-th me-1"></i>Galerie
                            </span>
                            <small class="text-muted ms-2">Photos supplémentaires — ajoutez-les une par une avant d'enregistrer</small>
                        </div>

                        <!-- Photos existantes de la galerie -->
                        <?php if (!empty($otherImgs)): ?>
                        <div class="gallery-existing-grid mt-2" id="galleryExistingGrid">
                            <?php foreach ($otherImgs as $img): ?>
                            <div class="gallery-existing-item" id="gimg_<?= $img['id'] ?>">
                                <img src="<?= UPLOADS_URL ?>/vehicles/<?= htmlspecialchars($img['filename']) ?>"
                                     alt="Galerie" loading="lazy">
                                <div class="gallery-existing-overlay">
                                    <button type="button"
                                            class="btn-set-main-gallery"
                                            onclick="promoteToMain(<?= $vehicle['id'] ?>, <?= $img['id'] ?>)"
                                            title="Définir comme principale">
                                        <i class="fas fa-star"></i>
                                    </button>
                                    <a href="<?= BASE_URL ?>/admin/vehicules/deleteImage/<?= $img['id'] ?>?vehicle_id=<?= $vehicle['id'] ?>"
                                       class="btn-delete-gallery"
                                       onclick="return confirm('Supprimer cette photo ?')"
                                       title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <p class="text-muted small mt-2" id="galleryEmptyMsg">
                            <i class="fas fa-info-circle me-1"></i>Aucune photo de galerie pour l'instant.
                        </p>
                        <?php endif; ?>

                        <!-- Zone d'ajout + prévisualisation avant save -->
                        <div class="gallery-add-zone mt-3" id="galleryAddZone">

                            <!-- Photos en attente d'upload (prévisualisées) -->
                            <div class="gallery-pending-grid" id="galleryPendingGrid">
                                <!-- Rempli dynamiquement par JS -->
                            </div>

                            <!-- Bouton Ajouter une photo -->
                            <div class="gallery-add-btn-wrapper">
                                <label for="gallery_add_input" class="gallery-add-btn" id="galleryAddBtn">
                                    <i class="fas fa-plus-circle fa-lg"></i>
                                    <span>Ajouter une photo</span>
                                </label>
                                <input type="file" id="gallery_add_input"
                                       accept="image/jpeg,image/png,image/webp"
                                       class="d-none">
                            </div>

                            <!-- Input hidden multiple final — alimenté par JS -->
                            <div id="galleryHiddenInputs"></div>
                        </div>

                        <p class="text-muted mt-2" style="font-size:.75rem;">
                            <i class="fas fa-lightbulb me-1 text-warning"></i>
                            Cliquez sur <i class="fas fa-plus-circle"></i> pour ajouter une photo.
                            Chaque photo apparaît en aperçu avant d'être enregistrée avec le formulaire.
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Boutons de sauvegarde -->
    <div class="form-actions sticky-bottom-actions">
        <a href="<?= BASE_URL ?>/admin/vehicules" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Annuler
        </a>
        <?php if ($isEdit): ?>
        <a href="<?= BASE_URL ?>/vehicule/<?= htmlspecialchars($vehicle['slug'] ?? '') ?>" target="_blank" class="btn btn-outline-primary">
            <i class="fas fa-eye me-2"></i>Voir la fiche
        </a>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary-custom btn-lg">
            <i class="fas fa-save me-2"></i><?= $isEdit ? 'Enregistrer les modifications' : 'Publier le véhicule' ?>
        </button>
    </div>

</form>

<script>
// ================================================================
// ÉQUIPEMENTS
// ================================================================
document.getElementById('addEquipmentBtn')?.addEventListener('click', () => {
    const list = document.getElementById('equipmentList');
    const row  = document.createElement('div');
    row.className = 'equipment-row d-flex gap-2 mb-2';
    row.innerHTML = '<input type="text" name="equipment[]" class="form-control" placeholder="Option ou équipement...">' +
                    '<button type="button" class="btn btn-outline-danger btn-sm remove-equipment"><i class="fas fa-times"></i></button>';
    list.appendChild(row);
    row.querySelector('input').focus();
});
document.addEventListener('click', e => {
    if (e.target.closest('.remove-equipment')) e.target.closest('.equipment-row').remove();
});

// ================================================================
// PHOTO PRINCIPALE — upload + prévisualisation
// ================================================================
(function () {
    const input       = document.getElementById('main_image_input');
    const zone        = document.getElementById('mainUploadZone');
    const newPreview  = document.getElementById('mainNewPreview');
    const newImg      = document.getElementById('mainNewPreviewImg');
    const newName     = document.getElementById('mainNewPreviewName');
    const removeBtn   = document.getElementById('mainNewPreviewRemove');

    if (!input) return;

    // Drag & Drop sur la zone
    zone?.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag-over'); });
    zone?.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
    zone?.addEventListener('drop', e => {
        e.preventDefault();
        zone.classList.remove('drag-over');
        if (e.dataTransfer.files.length) {
            const dt = new DataTransfer();
            dt.items.add(e.dataTransfer.files[0]);
            input.files = dt.files;
            previewMain(input.files[0]);
        }
    });

    input.addEventListener('change', () => {
        if (input.files.length) previewMain(input.files[0]);
    });

    removeBtn?.addEventListener('click', () => {
        input.value = '';
        newPreview.classList.add('d-none');
    });

    function previewMain(file) {
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => {
            newImg.src  = e.target.result;
            newName.textContent = file.name + ' (' + (file.size / 1024).toFixed(0) + ' Ko)';
            newPreview.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    }
})();

// ================================================================
// GALERIE — ajout photo par photo avec prévisualisation
// ================================================================
(function () {
    const addInput     = document.getElementById('gallery_add_input');
    const pendingGrid  = document.getElementById('galleryPendingGrid');
    const hiddenInputs = document.getElementById('galleryHiddenInputs');
    const emptyMsg     = document.getElementById('galleryEmptyMsg');

    if (!addInput) return;

    // Tableau des fichiers en attente (DataTransfer pour gérer le multi-input)
    let pendingFiles = [];

    addInput.addEventListener('change', () => {
        if (!addInput.files.length) return;
        const file = addInput.files[0];

        // Vérification taille
        if (file.size > 5 * 1024 * 1024) {
            alert('La photo "' + file.name + '" dépasse 5 Mo.');
            addInput.value = '';
            return;
        }

        // Vérification type
        if (!['image/jpeg','image/png','image/webp'].includes(file.type)) {
            alert('Format non supporté. Utilisez JPG, PNG ou WebP.');
            addInput.value = '';
            return;
        }

        const idx = pendingFiles.length;
        pendingFiles.push(file);
        addInput.value = ''; // reset pour permettre d'ajouter la même image

        // Prévisualisation
        const reader = new FileReader();
        reader.onload = e => {
            addPendingCard(e.target.result, file.name, idx);
            syncHiddenInputs();
            if (emptyMsg) emptyMsg.style.display = 'none';
        };
        reader.readAsDataURL(file);
    });

    function addPendingCard(dataUrl, filename, idx) {
        const card = document.createElement('div');
        card.className   = 'gallery-pending-item';
        card.dataset.idx = idx;
        card.innerHTML   = `
            <img src="${dataUrl}" alt="${filename}">
            <div class="gallery-pending-overlay">
                <span class="gallery-pending-name">${truncate(filename, 18)}</span>
                <button type="button" class="gallery-pending-remove" data-idx="${idx}" title="Retirer">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <span class="gallery-pending-badge"><i class="fas fa-clock me-1"></i>En attente</span>
        `;
        pendingGrid.appendChild(card);

        card.querySelector('.gallery-pending-remove').addEventListener('click', () => {
            pendingFiles[idx] = null; // nullifier (ne pas splicecar ça décalerait les indices)
            card.remove();
            syncHiddenInputs();
            if (pendingGrid.children.length === 0 && emptyMsg) emptyMsg.style.display = '';
        });
    }

    function syncHiddenInputs() {
        // Reconstruire un seul input[file] multiple avec tous les fichiers non-nuls
        hiddenInputs.innerHTML = '';

        const active = pendingFiles.filter(f => f !== null);
        if (!active.length) return;

        // On ne peut pas pré-remplir un input[file] par sécurité,
        // donc on stocke les fichiers dans un DataTransfer et on met à jour
        // un seul input caché qui sera soumis avec le formulaire.
        const container = document.createElement('div');
        container.style.display = 'none';

        const fakeInput = document.createElement('input');
        fakeInput.type     = 'file';
        fakeInput.name     = 'gallery_images[]';
        fakeInput.multiple = true;
        fakeInput.id       = 'gallery_images_final';

        container.appendChild(fakeInput);
        hiddenInputs.appendChild(container);

        const dt = new DataTransfer();
        active.forEach(f => dt.items.add(f));
        fakeInput.files = dt.files;
    }

    function truncate(str, max) {
        return str.length > max ? str.substring(0, max) + '…' : str;
    }
})();

// ================================================================
// PROMOUVOIR une galerie → photo principale
// ================================================================
function promoteToMain(vehicleId, imageId) {
    if (!confirm('Définir cette photo comme image principale ?')) return;
    const form   = document.getElementById('vehicleForm');
    const hidden = document.createElement('input');
    hidden.type  = 'hidden';
    hidden.name  = 'set_main_image';
    hidden.value = imageId;
    form.appendChild(hidden);
    form.submit();
}

// ================================================================
// KW AUTO-CALC depuis CH
// ================================================================
document.querySelector('[name="power_hp"]')?.addEventListener('input', function () {
    const hp = parseFloat(this.value);
    const kwField = document.querySelector('[name="power_kw"]');
    if (!isNaN(hp) && kwField && !kwField.value) {
        kwField.value = Math.round(hp * 0.7355);
    }
});

// ================================================================
// GARANTIE : afficher/masquer le champ durée
// ================================================================
(function () {
    const cb    = document.getElementById('warCheck');
    const field = document.getElementById('warrantyMonthsField');
    if (!cb || !field) return;
    const toggle = () => { field.style.display = cb.checked ? 'block' : 'none'; };
    toggle();
    cb.addEventListener('change', toggle);
})();

// ================================================================
// VALIDATION TAILLE fichiers avant submit
// ================================================================
document.getElementById('vehicleForm')?.addEventListener('submit', function (e) {
    const max = 5 * 1024 * 1024;
    // Photo principale
    const mainInput = document.getElementById('main_image_input');
    if (mainInput?.files.length && mainInput.files[0].size > max) {
        e.preventDefault();
        alert('La photo principale dépasse 5 Mo.');
        return;
    }
    // Photos galerie
    const galleryInput = document.getElementById('gallery_images_final');
    if (galleryInput) {
        for (const f of galleryInput.files) {
            if (f.size > max) {
                e.preventDefault();
                alert('La photo "' + f.name + '" dépasse 5 Mo.');
                return;
            }
        }
    }
});
</script>

