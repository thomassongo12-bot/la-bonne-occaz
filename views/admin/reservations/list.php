<!-- Filtres statut -->
<div class="d-flex gap-2 flex-wrap mb-4 align-items-center">
    <?php foreach ([''=>'Toutes','new'=>'Nouvelles','confirmed'=>'Confirmées','cancelled'=>'Annulées','completed'=>'Terminées'] as $k=>$l): ?>
    <a href="<?= BASE_URL ?>/admin/reservations<?= $k ? '?status='.$k : '' ?>"
       class="btn btn-sm <?= $status===$k ? 'btn-primary-custom' : 'btn-outline-secondary' ?>">
        <?= $l ?><?php if($k==='new' && $total>0 && !$status): ?> <span class="badge bg-danger ms-1"><?= $total ?></span><?php endif; ?>
    </a>
    <?php endforeach; ?>
    <span class="ms-auto text-muted small align-self-center"><?= $total ?> réservation<?= $total>1?'s':'' ?></span>
</div>

<div class="admin-card">
    <div class="admin-card__body p-0">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr><th>#</th><th>Client</th><th>Véhicule</th><th>Date</th><th>Statut</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $r): ?>
                    <tr class="<?= $r['status']==='new'?'table-row-new':'' ?>">
                        <td><?= $r['id'] ?></td>
                        <td>
                            <strong><?= htmlspecialchars($r['first_name'].' '.$r['last_name']) ?></strong><br>
                            <small><a href="mailto:<?= htmlspecialchars($r['email']) ?>"><?= htmlspecialchars($r['email']) ?></a></small><br>
                            <small><?= htmlspecialchars($r['phone']) ?></small>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars(($r['brand_name']??'').' '.$r['model']) ?></strong>
                            <small class="text-muted d-block"><?= $r['year'] ?></small>
                        </td>
                        <td><small><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></small></td>
                        <td><?php
                            $badges = ['new'=>'danger','confirmed'=>'warning','cancelled'=>'secondary','completed'=>'success'];
                            $labels = ['new'=>'Nouvelle','confirmed'=>'Confirmée','cancelled'=>'Annulée','completed'=>'Terminée'];
                            $s = $r['status'];
                            echo '<span class="badge bg-'.($badges[$s]??'secondary').'">'.($labels[$s]??$s).'</span>';
                        ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/reservations/view/<?= $r['id'] ?>"
                               class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($items)): ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">Aucune réservation.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($pages > 1): ?>
<nav class="mt-3"><ul class="pagination justify-content-center">
    <?php for($i=1;$i<=$pages;$i++): ?>
    <li class="page-item <?= $i===$page?'active':'' ?>">
        <a class="page-link" href="<?= BASE_URL ?>/admin/reservations?page=<?= $i ?>&status=<?= urlencode($status) ?>"><?= $i ?></a>
    </li>
    <?php endfor; ?>
</ul></nav>
<?php endif; ?>
