<!-- Toolbar -->
<div class="admin-toolbar mb-4">
    <form class="d-flex gap-2" method="GET" action="<?= BASE_URL ?>/admin/vehicules">
        <input type="text" name="search" class="form-control" placeholder="Rechercher un véhicule..."
               value="<?= htmlspecialchars($search ?? '') ?>">
        <button type="submit" class="btn btn-outline-primary px-3"><i class="fas fa-search"></i></button>
        <?php if ($search): ?>
        <a href="<?= BASE_URL ?>/admin/vehicules" class="btn btn-outline-secondary px-3"><i class="fas fa-times"></i></a>
        <?php endif; ?>
    </form>
    <a href="<?= BASE_URL ?>/admin/vehicules/add" class="btn btn-primary-custom ms-auto">
        <i class="fas fa-plus me-2"></i>Ajouter un véhicule
    </a>
</div>

<div class="admin-card">
    <div class="admin-card__header d-flex justify-content-between align-items-center">
        <h3><i class="fas fa-car me-2"></i>Véhicules (<?= $total ?>)</h3>
        <small class="text-muted">Page <?= $page ?> / <?= max(1,$pages) ?></small>
    </div>
    <div class="admin-card__body p-0">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:70px">Photo</th>
                        <th>Véhicule</th>
                        <th>Année / Km</th>
                        <th>Prix</th>
                        <th>Statut</th>
                        <th>Vedette</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vehicles as $v): ?>
                    <tr>
                        <td>
                            <img src="<?= !empty($v['main_image']) ? UPLOADS_URL.'/vehicles/'.$v['main_image'] : BASE_URL.'/public/images/no-vehicle.svg' ?>"
                                 alt="" class="admin-table-thumb"
                                 >
                        </td>
                        <td>
                            <strong><?= htmlspecialchars(($v['brand_name'] ?? '') . ' ' . $v['model']) ?></strong><br>
                            <small class="text-muted"><?= htmlspecialchars($v['version'] ?? '') ?></small>
                        </td>
                        <td>
                            <?= $v['year'] ?><br>
                            <small class="text-muted"><?= number_format($v['mileage'], 0, ',', ' ') ?> km</small>
                        </td>
                        <td><strong class="text-primary"><?= number_format($v['price'], 0, ',', ' ') ?> €</strong></td>
                        <td>
                            <form action="<?= BASE_URL ?>/admin/vehicules/status/<?= $v['id'] ?>" method="POST" class="status-form">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                <select name="status" class="form-select form-select-sm status-select"
                                        onchange="this.form.submit()"
                                        data-current="<?= $v['status'] ?>">
                                    <option value="available" <?= $v['status']==='available' ? 'selected':'' ?>>Disponible</option>
                                    <option value="reserved"  <?= $v['status']==='reserved'  ? 'selected':'' ?>>Réservé</option>
                                    <option value="sold"      <?= $v['status']==='sold'       ? 'selected':'' ?>>Vendu</option>
                                    <option value="draft"     <?= $v['status']==='draft'      ? 'selected':'' ?>>Brouillon</option>
                                </select>
                            </form>
                        </td>
                        <td class="text-center">
                            <a href="<?= BASE_URL ?>/admin/vehicules/featured/<?= $v['id'] ?>"
                               class="btn btn-sm <?= $v['is_featured'] ? 'btn-warning' : 'btn-outline-secondary' ?>"
                               title="<?= $v['is_featured'] ? 'Retirer de la vedette' : 'Mettre en vedette' ?>">
                                <i class="fas fa-star"></i>
                            </a>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="<?= BASE_URL ?>/admin/vehicules/edit/<?= $v['id'] ?>"
                                   class="btn btn-sm btn-outline-primary" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= BASE_URL ?>/vehicule/<?= htmlspecialchars($v['slug'] ?? '') ?>"
                                   target="_blank" class="btn btn-sm btn-outline-secondary" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-danger"
                                        onclick="confirmDelete('<?= BASE_URL ?>/admin/vehicules/delete/<?= $v['id'] ?>', '<?= htmlspecialchars(($v['brand_name']??'').' '.$v['model'], ENT_QUOTES) ?>')"
                                        title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($vehicles)): ?>
                    <tr><td colspan="7" class="text-center py-4 text-muted">Aucun véhicule trouvé.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<?php if ($pages > 1): ?>
<nav class="mt-4">
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
            <a class="page-link" href="<?= BASE_URL ?>/admin/vehicules?page=<?= $i ?>&search=<?= urlencode($search??'') ?>"><?= $i ?></a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<!-- Modal confirmation suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle text-danger me-2"></i>Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Supprimer définitivement le véhicule <strong id="deleteVehicleName"></strong> ?<br>
                <small class="text-danger">Cette action est irréversible. Toutes les photos seront supprimées.</small>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <a id="deleteConfirmBtn" href="#" class="btn btn-danger"><i class="fas fa-trash me-2"></i>Supprimer</a>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(url, name) {
    document.getElementById('deleteVehicleName').textContent = name;
    document.getElementById('deleteConfirmBtn').href = url;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

