<div class="row g-4">
    <div class="col-lg-7">
        <div class="admin-card mb-4">
            <div class="admin-card__header"><h3><i class="fas fa-user me-2"></i>Client</h3></div>
            <div class="admin-card__body">
                <dl class="detail-dl">
                    <dt>Nom</dt><dd><?= htmlspecialchars($item['first_name'].' '.$item['last_name']) ?></dd>
                    <dt>Email</dt><dd><a href="mailto:<?= htmlspecialchars($item['email']) ?>"><?= htmlspecialchars($item['email']) ?></a></dd>
                    <dt>Téléphone</dt><dd><a href="tel:<?= htmlspecialchars($item['phone']) ?>"><?= htmlspecialchars($item['phone']) ?></a></dd>
                    <dt>Date</dt><dd><?= date('d/m/Y à H:i', strtotime($item['created_at'])) ?></dd>
                    <dt>IP</dt><dd><small class="text-muted"><?= htmlspecialchars($item['ip_address']??'') ?></small></dd>
                    <?php if ($item['message']): ?>
                    <dt>Message</dt><dd class="message-box"><?= nl2br(htmlspecialchars($item['message'])) ?></dd>
                    <?php endif; ?>
                    <dt>Conditions</dt>
                    <dd>
                        <?php if ($item['conditions_accepted']): ?>
                        <span class="badge bg-success"><i class="fas fa-check me-1"></i>Acceptées</span>
                        <?php else: ?>
                        <span class="badge bg-danger"><i class="fas fa-times me-1"></i>Non acceptées</span>
                        <?php endif; ?>
                    </dd>
                </dl>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card__header"><h3><i class="fas fa-car me-2"></i>Véhicule</h3></div>
            <div class="admin-card__body">
                <dl class="detail-dl">
                    <dt>Véhicule</dt><dd><strong><?= htmlspecialchars(($item['brand_name']??'').' '.$item['model'].' '.$item['year']) ?></strong></dd>
                    <dt>Prix</dt><dd><?= number_format((float)$item['price'],0,',',' ') ?> €</dd>
                </dl>
                <div class="mt-2">
                    <a href="<?= BASE_URL ?>/vehicule/<?= htmlspecialchars($item['vehicle_slug']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-external-link-alt me-1"></i>Voir la fiche
                    </a>
                    <a href="<?= BASE_URL ?>/admin/vehicules/edit/<?= $item['vehicle_id'] ?>" class="btn btn-sm btn-outline-primary ms-2">
                        <i class="fas fa-edit me-1"></i>Modifier le véhicule
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="admin-card">
            <div class="admin-card__header"><h3><i class="fas fa-edit me-2"></i>Gérer la réservation</h3></div>
            <div class="admin-card__body">
                <form action="<?= BASE_URL ?>/admin/reservations/updateStatus/<?= $item['id'] ?>" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf ?? '') ?>">
                    <div class="mb-3">
                        <label class="form-label">Statut</label>
                        <select name="status" class="form-select">
                            <?php foreach (['new'=>'Nouvelle','confirmed'=>'Confirmée','cancelled'=>'Annulée','completed'=>'Terminée'] as $k=>$l): ?>
                            <option value="<?= $k ?>" <?= $item['status']===$k?'selected':'' ?>><?= $l ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note interne</label>
                        <textarea name="admin_note" class="form-control" rows="4"
                                  placeholder="Acompte reçu, rendez-vous fixé..."><?= htmlspecialchars($item['admin_note']??'') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary-custom w-100">
                        <i class="fas fa-save me-2"></i>Enregistrer
                    </button>
                </form>

                <div class="mt-3 d-flex gap-2">
                    <a href="mailto:<?= htmlspecialchars($item['email']) ?>?subject=Votre réservation - La Bonne Occaz"
                       class="btn btn-outline-primary flex-fill">
                        <i class="fas fa-envelope me-1"></i>Email client
                    </a>
                    <a href="tel:<?= str_replace([' ','.'],'', $item['phone']) ?>"
                       class="btn btn-outline-success flex-fill">
                        <i class="fas fa-phone me-1"></i>Appeler
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <a href="<?= BASE_URL ?>/admin/reservations" class="btn btn-outline-secondary w-100">
                <i class="fas fa-arrow-left me-2"></i>Retour à la liste
            </a>
        </div>
    </div>
</div>
