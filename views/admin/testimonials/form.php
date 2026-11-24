<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="admin-card">
            <div class="admin-card__header">
                <h3><?= $item ? 'Modifier le témoignage' : 'Ajouter un témoignage' ?></h3>
            </div>
            <div class="admin-card__body">
                <form action="<?= BASE_URL ?>/admin/temoignages/<?= $item ? 'update/'.$item['id'] : 'create' ?>"
                      method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf ?? '') ?>">
                    <div class="row g-3">

                        <!-- Photo de profil -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Photo de profil</label>
                            <div class="d-flex align-items-start gap-3">
                                <!-- Aperçu existant -->
                                <div id="photoPreviewBox">
                                    <?php if (!empty($item['photo_url'])): ?>
                                    <img src="<?= htmlspecialchars($item['photo_url']) ?>"
                                         id="photoCurrentImg"
                                         alt="Photo actuelle"
                                         style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid #e2e8f0;">
                                    <?php else: ?>
                                    <div id="photoPlaceholder"
                                         style="width:72px;height:72px;border-radius:50%;background:#e2e8f0;display:flex;align-items:center;justify-content:center;font-size:1.8rem;color:#94a3b8;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-fill">
                                    <div class="branding-upload-area" id="photoUploadArea"
                                         style="padding:1rem;min-height:72px;cursor:pointer;">
                                        <i class="fas fa-camera me-1"></i>
                                        <span class="small">
                                            Glissez ou
                                            <span class="text-primary" style="cursor:pointer;text-decoration:underline">parcourir</span>
                                        </span>
                                        <div class="text-muted" style="font-size:.72rem;">JPG, PNG, WebP — max 2 Mo</div>
                                        <input type="file" id="photo" name="photo"
                                               accept="image/jpeg,image/png,image/webp"
                                               class="d-none">
                                    </div>
                                    <?php if (!empty($item['photo_url'])): ?>
                                    <label class="form-check mt-2 d-flex align-items-center gap-2" style="cursor:pointer">
                                        <input type="checkbox" class="form-check-input" name="remove_photo" value="1">
                                        <span class="text-danger small">Supprimer la photo actuelle</span>
                                    </label>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Auteur <span class="text-danger">*</span></label>
                            <input type="text" name="author" class="form-control" required
                                   value="<?= htmlspecialchars($item['author'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Localisation</label>
                            <input type="text" name="location" class="form-control" placeholder="Ex : Annemasse"
                                   value="<?= htmlspecialchars($item['location'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Note</label>
                            <select name="rating" class="form-select">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                <option value="<?= $i ?>" <?= (isset($item['rating']) && $item['rating'] == $i) ? 'selected' : '' ?>>
                                    <?= $i ?> étoile<?= $i > 1 ? 's' : '' ?>
                                </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Véhicule référencé</label>
                            <input type="text" name="vehicle_ref" class="form-control" placeholder="Ex : BMW Série 3"
                                   value="<?= htmlspecialchars($item['vehicle_ref'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ordre d'affichage</label>
                            <input type="number" name="sort_order" class="form-control" min="0"
                                   value="<?= $item['sort_order'] ?? 0 ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Témoignage <span class="text-danger">*</span></label>
                            <textarea name="content" class="form-control" rows="5" required><?= htmlspecialchars($item['content'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActiveCheck"
                                       <?= (!$item || $item['is_active']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="isActiveCheck">Publier ce témoignage</label>
                            </div>
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary-custom">
                                <i class="fas fa-save me-2"></i>Enregistrer
                            </button>
                            <a href="<?= BASE_URL ?>/admin/temoignages" class="btn btn-secondary">Annuler</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('photo');
    const area  = document.getElementById('photoUploadArea');

    // ── Ouvrir le sélecteur uniquement via l'input, pas via le label
    // (évite le double déclenchement label + area)
    area.addEventListener('click', e => {
        // Si le clic vient du label interne, ne rien faire de plus
        if (e.target.tagName === 'LABEL' || e.target.closest('label')) return;
        input.click();
    });

    // Drag & drop
    area.addEventListener('dragover',  e => { e.preventDefault(); area.classList.add('drag-over'); });
    area.addEventListener('dragleave', ()  => area.classList.remove('drag-over'));
    area.addEventListener('drop', e => {
        e.preventDefault();
        area.classList.remove('drag-over');
        const files = e.dataTransfer.files;
        if (files.length) showPreview(files[0]);
    });

    // Sélection fichier — utiliser onchange pour fiabilité max
    input.onchange = () => {
        if (input.files && input.files[0]) showPreview(input.files[0]);
    };

    function showPreview(file) {
        // Validation immédiate
        if (!file.type.startsWith('image/')) return;
        if (file.size > 2 * 1024 * 1024) {
            alert('Image trop volumineuse (max 2 Mo). Choisissez une image plus petite.');
            input.value = '';
            return;
        }

        const box = document.getElementById('photoPreviewBox');

        // Afficher un spinner pendant le chargement
        box.innerHTML = `<div style="width:72px;height:72px;border-radius:50%;background:#f1f5f9;
            display:flex;align-items:center;justify-content:center;">
            <i class="fas fa-spinner fa-spin text-muted"></i></div>`;

        // Redimensionner via Canvas avant d'afficher (rapide, évite les lenteurs sur grosses photos)
        const img = new Image();
        const url = URL.createObjectURL(file);
        img.onload = () => {
            URL.revokeObjectURL(url);

            // Canvas 150×150 max
            const size  = 150;
            const ratio = Math.min(size / img.width, size / img.height);
            const w = Math.round(img.width  * ratio);
            const h = Math.round(img.height * ratio);

            const canvas = document.createElement('canvas');
            canvas.width  = w;
            canvas.height = h;
            canvas.getContext('2d').drawImage(img, 0, 0, w, h);

            box.innerHTML = `<img src="${canvas.toDataURL('image/jpeg', 0.85)}" alt="Aperçu"
                style="width:72px;height:72px;border-radius:50%;object-fit:cover;
                       border:3px solid #e63946;display:block;">`;
        };
        img.onerror = () => {
            URL.revokeObjectURL(url);
            box.innerHTML = `<div style="width:72px;height:72px;border-radius:50%;background:#fee2e2;
                display:flex;align-items:center;justify-content:center;color:#ef4444;">
                <i class="fas fa-times"></i></div>`;
        };
        img.src = url;
    }
});
</script>
