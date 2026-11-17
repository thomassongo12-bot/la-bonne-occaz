<div class="row g-4">
    <div class="col-lg-7">
        <div class="admin-card">
            <div class="admin-card__header">
                <h3><i class="fas fa-envelope me-2"></i>Demande #<?= $item['id'] ?></h3>
            </div>
            <div class="admin-card__body">
                <dl class="detail-dl">
                    <dt>Client</dt><dd><?= htmlspecialchars($item['first_name'].' '.$item['last_name']) ?></dd>
                    <dt>Email</dt><dd><a href="mailto:<?= htmlspecialchars($item['email']) ?>"><?= htmlspecialchars($item['email']) ?></a></dd>
                    <dt>Téléphone</dt><dd><?= $item['phone'] ? htmlspecialchars($item['phone']) : '—' ?></dd>
                    <?php if ($item['vehicle_brand']): ?>
                    <dt>Véhicule concerné</dt>
                    <dd><a href="<?= BASE_URL ?>/vehicule/..." target="_blank"><?= htmlspecialchars($item['vehicle_brand'].' '.$item['vehicle_model']) ?></a></dd>
                    <?php endif; ?>
                    <dt>Date</dt><dd><?= date('d/m/Y à H:i', strtotime($item['created_at'])) ?></dd>
                    <dt>Message</dt>
                    <dd class="message-box"><?= nl2br(htmlspecialchars($item['message'])) ?></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="admin-card">
            <div class="admin-card__header"><h3><i class="fas fa-edit me-2"></i>Mettre à jour</h3></div>
            <div class="admin-card__body">
                <form action="<?= BASE_URL ?>/admin/demandes/updateStatus/<?= $item['id'] ?>" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf ?? '') ?>">
                    <div class="mb-3">
                        <label class="form-label">Statut</label>
                        <select name="status" class="form-select">
                            <?php foreach (['new'=>'Nouvelle','processing'=>'En cours','contacted'=>'Contacté','done'=>'Terminée','refused'=>'Refusée'] as $k=>$l): ?>
                            <option value="<?= $k ?>" <?= $item['status']===$k?'selected':'' ?>><?= $l ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note interne</label>
                        <textarea name="admin_note" class="form-control" rows="4"
                                  placeholder="Notes visibles uniquement par l'administration..."><?= htmlspecialchars($item['admin_note'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary-custom w-100">
                        <i class="fas fa-save me-2"></i>Enregistrer
                    </button>
                </form>
            </div>
        </div>
        <div class="mt-3">
            <a href="<?= BASE_URL ?>/admin/demandes" class="btn btn-outline-secondary w-100">
                <i class="fas fa-arrow-left me-2"></i>Retour aux demandes
            </a>
        </div>
    </div>
</div>
