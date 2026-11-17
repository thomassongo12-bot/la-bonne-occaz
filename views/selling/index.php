<section class="page-hero page-hero--selling">
    <div class="container">
        <nav aria-label="breadcrumb" class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Accueil</a></li>
                <li class="breadcrumb-item active">Vendre / Dépôt-vente</li>
            </ol>
        </nav>
        <h1 class="page-hero__title">Vendez ou déposez votre véhicule</h1>
        <p class="page-hero__subtitle">Évaluation gratuite · Prise en charge complète · Paiement rapide</p>
    </div>
</section>

<!-- Avantages dépôt-vente -->
<section class="section-padding-sm bg-light-custom">
    <div class="container">
        <div class="row g-4 text-center">
            <?php foreach ([
                ['fa-search-dollar','Estimation gratuite','Obtenez rapidement la valeur réelle de votre véhicule sans engagement.'],
                ['fa-handshake','Prise en charge totale','Nous gérons les visites, les négociations et les démarches administratives.'],
                ['fa-shield-alt','Sécurité garantie','Transactions 100% sécurisées, paiement vérifié avant remise des clés.'],
                ['fa-bolt','Vente rapide','Notre réseau d\'acheteurs qualifiés accélère la vente de votre véhicule.'],
            ] as $a): ?>
            <div class="col-md-3 col-6">
                <div class="advantage-box">
                    <i class="fas <?= $a[0] ?>"></i>
                    <h4><?= $a[1] ?></h4>
                    <p><?= $a[2] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Formulaire -->
<section class="selling-form-section section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="selling-form-card">
                    <div class="selling-form-header">
                        <h2><i class="fas fa-car me-2"></i>Déposez votre annonce</h2>
                        <p>Remplissez ce formulaire, notre équipe vous contacte sous 48h avec une proposition.</p>
                    </div>

                    <form action="<?= BASE_URL ?>/vendre/submit" method="POST" enctype="multipart/form-data"
                          id="sellingForm" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf ?? '') ?>">

                        <!-- Section 1 : Vos coordonnées -->
                        <div class="form-section">
                            <h3 class="form-section-title"><span>1</span> Vos coordonnées</h3>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Prénom <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nom <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Téléphone <span class="text-danger">*</span></label>
                                    <input type="tel" name="phone" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2 : Votre véhicule -->
                        <div class="form-section mt-4">
                            <h3 class="form-section-title"><span>2</span> Votre véhicule</h3>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Marque <span class="text-danger">*</span></label>
                                    <input type="text" name="brand" class="form-control" list="brandsList" required>
                                    <datalist id="brandsList">
                                        <?php foreach ($brands as $b): ?>
                                        <option value="<?= htmlspecialchars($b['name']) ?>">
                                        <?php endforeach; ?>
                                    </datalist>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Modèle <span class="text-danger">*</span></label>
                                    <input type="text" name="model" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Version / Finition</label>
                                    <input type="text" name="version" class="form-control" placeholder="Ex : 1.5 TDI 150 Comfortline">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Année <span class="text-danger">*</span></label>
                                    <input type="number" name="year" class="form-control"
                                           min="1990" max="<?= date('Y') ?>" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Kilométrage <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="mileage" class="form-control" min="0" required>
                                        <span class="input-group-text">km</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Carburant <span class="text-danger">*</span></label>
                                    <select name="fuel_type" class="form-select" required>
                                        <option value="">Choisir...</option>
                                        <?php foreach ($fuel_types as $f): ?>
                                        <option value="<?= htmlspecialchars($f['name']) ?>"><?= htmlspecialchars($f['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Transmission <span class="text-danger">*</span></label>
                                    <select name="transmission" class="form-select" required>
                                        <option value="">Choisir...</option>
                                        <?php foreach ($transmissions as $t): ?>
                                        <option value="<?= htmlspecialchars($t['name']) ?>"><?= htmlspecialchars($t['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Couleur</label>
                                    <input type="text" name="color" class="form-control" placeholder="Ex : Blanc Nacré">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">État général</label>
                                    <select name="condition_state" class="form-select">
                                        <option value="">Choisir...</option>
                                        <option value="excellent">Excellent</option>
                                        <option value="very_good">Très bon</option>
                                        <option value="good">Bon</option>
                                        <option value="fair">Correct</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Prix souhaité</label>
                                    <div class="input-group">
                                        <input type="number" name="desired_price" class="form-control" min="0" placeholder="Facultatif">
                                        <span class="input-group-text">€</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Description / État du véhicule</label>
                                    <textarea name="description" class="form-control" rows="4"
                                        placeholder="Options, historique, état général, éventuelles réparations à prévoir..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3 : Photos -->
                        <div class="form-section mt-4">
                            <h3 class="form-section-title"><span>3</span> Photos de votre véhicule</h3>
                            <div class="photo-upload-area" id="photoUploadArea">
                                <i class="fas fa-camera fa-2x mb-2"></i>
                                <p>Glissez vos photos ici ou <label for="photosInput" class="text-primary" style="cursor:pointer">parcourir</label></p>
                                <p class="small text-muted">JPG, PNG, WebP — max 5 Mo par photo — jusqu'à 10 photos</p>
                                <input type="file" id="photosInput" name="photos[]" multiple
                                       accept="image/jpeg,image/png,image/webp" class="d-none">
                            </div>
                            <div class="photo-preview-grid" id="photoPreviewGrid"></div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary-custom btn-lg w-100">
                                <i class="fas fa-paper-plane me-2"></i>Envoyer ma demande
                            </button>
                            <p class="text-muted text-center mt-2 small">
                                <i class="fas fa-lock me-1"></i>Vos données sont sécurisées et ne seront pas partagées.
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Photo upload preview
const photosInput = document.getElementById('photosInput');
const photoUploadArea = document.getElementById('photoUploadArea');
const photoPreviewGrid = document.getElementById('photoPreviewGrid');

photoUploadArea.addEventListener('click', () => photosInput.click());

photoUploadArea.addEventListener('dragover', e => { e.preventDefault(); photoUploadArea.classList.add('drag-over'); });
photoUploadArea.addEventListener('dragleave', () => photoUploadArea.classList.remove('drag-over'));
photoUploadArea.addEventListener('drop', e => {
    e.preventDefault();
    photoUploadArea.classList.remove('drag-over');
    const dt = new DataTransfer();
    [...e.dataTransfer.files, ...(photosInput.files || [])].forEach(f => dt.items.add(f));
    photosInput.files = dt.files;
    renderPreviews(photosInput.files);
});

photosInput.addEventListener('change', () => renderPreviews(photosInput.files));

function renderPreviews(files) {
    photoPreviewGrid.innerHTML = '';
    [...files].slice(0, 10).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = 'photo-preview-item';
            div.innerHTML = `<img src="${e.target.result}" alt="Aperçu"><span class="photo-preview-name">${file.name}</span>`;
            photoPreviewGrid.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}
</script>
