<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Témoignages (<?= count($items) ?>)</h2>
    <a href="<?= BASE_URL ?>/admin/temoignages/add" class="btn btn-primary-custom">
        <i class="fas fa-plus me-2"></i>Ajouter
    </a>
</div>

<div class="admin-card">
    <div class="admin-card__body p-0">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr><th>Auteur</th><th>Localisation</th><th>Note</th><th>Véhicule ref.</th><th>Actif</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $t): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($t['author']) ?></strong></td>
                        <td><?= htmlspecialchars($t['location']??'') ?></td>
                        <td>
                            <?php for($i=1;$i<=5;$i++): ?>
                            <i class="fas fa-star<?= $i>$t['rating']?' text-muted':' text-warning' ?>"></i>
                            <?php endfor; ?>
                        </td>
                        <td><small><?= htmlspecialchars($t['vehicle_ref']??'') ?></small></td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/temoignages/toggle/<?= $t['id'] ?>">
                                <span class="badge bg-<?= $t['is_active']?'success':'secondary' ?>">
                                    <?= $t['is_active']?'Actif':'Inactif' ?>
                                </span>
                            </a>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/temoignages/edit/<?= $t['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                            <a href="<?= BASE_URL ?>/admin/temoignages/delete/<?= $t['id'] ?>" class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Supprimer ce témoignage ?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($items)): ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">Aucun témoignage.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
