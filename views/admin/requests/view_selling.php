<div class="row g-4">
    <div class="col-lg-7">
        <div class="admin-card">
            <div class="admin-card__header"><h3><i class="fas fa-car me-2"></i>Dépôt-vente #<?= $item['id'] ?></h3></div>
            <div class="admin-card__body">
                <dl class="detail-dl">
                    <dt>Client</dt><dd><?= htmlspecialchars($item['first_name'].' '.$item['last_name']) ?></dd>
                    <dt>Email</dt><dd><a href="mailto:<?= htmlspecialchars($item['email']) ?>"><?= htmlspecialchars($item['email']) ?></a></dd>
                    <dt>Téléphone</dt><dd><a href="tel:<?= htmlspecialchars($item['phone']) ?>"><?= htmlspecialchars($item['phone']) ?></a></dd>
                    <dt>Véhicule</dt><dd><strong><?= htmlspecialchars($item['brand'].' '.$item['model']) ?></strong> <?= htmlspecialchars($item['version']??'') ?></dd>
                    <dt>Année</dt><dd><?= $item['year'] ?></dd>
                    <dt>Kilométrage</dt><dd><?= number_format($item['mileage'],0,',',' ') ?> km</dd>
                    <dt>Carburant</dt><dd><?= htmlspecialchars($item['fuel_type']) ?></dd>
                    <dt>Transmission</dt><dd><?= htmlspecialchars($item['transmission']) ?></dd>
                    <?php if ($item['color']): ?><dt>Couleur</dt><dd><?= htmlspecialchars($item['color']) ?></dd><?php endif; ?>
                    <?php if ($item['desired_price']): ?><dt>Prix souhaité</dt><dd><?= number_format($item['desired_price'],0,',',' ') ?> €</dd><?php endif; ?>
                    <?php if ($item['description']): ?><dt>Description</dt><dd><?= nl2br(htmlspecialchars($item['description'])) ?></dd><?php endif; ?>
                    <dt>Date</dt><dd><?= date('d/m/Y à H:i', strtotime($item['created_at'])) ?></dd>
                </dl>
                <?php if (!empty($item['images'])): ?>
                <h4 class="mt-3">Photos fournies :</h4>
                <div class="row g-2">
                    <?php foreach ($item['images'] as $img): ?>
                    <div class="col-4">
                        <img src="<?= UPLOADS_URL ?>/selling/<?= htmlspecialchars($img['filename']) ?>"
                             class="img-fluid rounded" alt="Photo dépôt-vente"
                             >
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="admin-card">
            <div class="admin-card__header"><h3><i class="fas fa-edit me-2"></i>Mettre à jour</h3></div>
            <div class="admin-card__body">
                <form action="<?= BASE_URL ?>/admin/demandes/updateSellingStatus/<?= $item['id'] ?>" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf ?? '') ?>">
                    <div class="mb-3">
                        <label class="form-label">Statut</label>
                        <select name="status" class="form-select">
                            <?php foreach (['new'=>'Nouvelle','evaluating'=>'En évaluation','accepted'=>'Acceptée','rejected'=>'Refusée','sold'=>'Vendu'] as $k=>$l): ?>
                            <option value="<?= $k ?>" <?= $item['status']===$k?'selected':'' ?>><?= $l ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note interne</label>
                        <textarea name="admin_note" class="form-control" rows="4"><?= htmlspecialchars($item['admin_note']??'') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary-custom w-100"><i class="fas fa-save me-2"></i>Enregistrer</button>
                </form>
            </div>
        </div>
        <div class="mt-3">
            <a href="<?= BASE_URL ?>/admin/demandes?tab=selling" class="btn btn-outline-secondary w-100">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>
</div>

