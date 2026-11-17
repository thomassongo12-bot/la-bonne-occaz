<!-- Tabs -->
<ul class="nav nav-tabs mb-4" id="requestsTabs">
    <li class="nav-item">
        <a class="nav-link <?= empty($_GET['tab']) || $_GET['tab']==='contacts' ? 'active':'' ?>"
           href="?tab=contacts">
            <i class="fas fa-envelope me-1"></i>Demandes de contact (<?= $total ?>)
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($_GET['tab']??'')==='selling' ? 'active':'' ?>"
           href="?tab=selling">
            <i class="fas fa-car me-1"></i>Dépôt-vente (<?= count($selling) ?>)
        </a>
    </li>
</ul>

<?php if (empty($_GET['tab']) || $_GET['tab'] === 'contacts'): ?>
<!-- Filtres contacts -->
<div class="d-flex gap-2 flex-wrap mb-3">
    <a href="<?= BASE_URL ?>/admin/demandes?tab=contacts"
       class="btn btn-sm <?= !$status && !$type ? 'btn-primary-custom' : 'btn-outline-secondary' ?>">Toutes</a>
    <a href="<?= BASE_URL ?>/admin/demandes?tab=contacts&status=new"
       class="btn btn-sm <?= $status==='new' ? 'btn-primary-custom' : 'btn-outline-secondary' ?>">Nouvelles</a>
    <a href="<?= BASE_URL ?>/admin/demandes?tab=contacts&status=processing"
       class="btn btn-sm <?= $status==='processing' ? 'btn-primary-custom' : 'btn-outline-secondary' ?>">En cours</a>
    <a href="<?= BASE_URL ?>/admin/demandes?tab=contacts&status=done"
       class="btn btn-sm <?= $status==='done' ? 'btn-primary-custom' : 'btn-outline-secondary' ?>">Terminées</a>
    <span class="ms-auto text-muted small align-self-center"><?= $total ?> demande<?= $total>1?'s':'' ?></span>
</div>

<div class="admin-card">
    <div class="admin-card__body p-0">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr><th>#</th><th>Client</th><th>Type</th><th>Véhicule</th><th>Date</th><th>Statut</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($contacts as $c): ?>
                    <tr class="<?= $c['status']==='new' ? 'table-row-new' : '' ?>">
                        <td><?= $c['id'] ?></td>
                        <td>
                            <strong><?= htmlspecialchars($c['first_name'].' '.$c['last_name']) ?></strong><br>
                            <small><a href="mailto:<?= htmlspecialchars($c['email']) ?>"><?= htmlspecialchars($c['email']) ?></a></small>
                        </td>
                        <td>
                            <?php $typeLabels=['contact'=>'Contact','vehicle_info'=>'Info véhicule','appointment'=>'RDV','financing'=>'Financement'];
                            $typeColors=['contact'=>'primary','vehicle_info'=>'info','appointment'=>'warning','financing'=>'success'];
                            $t=$c['type'];
                            echo '<span class="badge bg-'.($typeColors[$t]??'secondary').'">'.($typeLabels[$t]??$t).'</span>';
                            ?>
                        </td>
                        <td><?= $c['vehicle_brand'] ? htmlspecialchars($c['vehicle_brand'].' '.$c['vehicle_model']) : '—' ?></td>
                        <td><small><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></small></td>
                        <td>
                            <?php $sBadge=['new'=>'danger','processing'=>'warning','contacted'=>'info','done'=>'success','refused'=>'secondary'];
                            $sLabel=['new'=>'Nouvelle','processing'=>'En cours','contacted'=>'Contacté','done'=>'Terminée','refused'=>'Refusée'];
                            $s=$c['status'];
                            echo '<span class="badge bg-'.($sBadge[$s]??'secondary').'">'.($sLabel[$s]??$s).'</span>';
                            ?>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/demandes/view/<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye me-1"></i>Voir
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($contacts)): ?>
                    <tr><td colspan="7" class="text-center py-4 text-muted">Aucune demande.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($pages > 1): ?>
<nav class="mt-3">
    <ul class="pagination justify-content-center">
        <?php for ($i=1;$i<=$pages;$i++): ?>
        <li class="page-item <?= $i===$page?'active':'' ?>">
            <a class="page-link" href="<?= BASE_URL ?>/admin/demandes?tab=contacts&page=<?= $i ?>&status=<?= urlencode($status) ?>"><?= $i ?></a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<?php else: // TAB: selling ?>

<div class="admin-card">
    <div class="admin-card__body p-0">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr><th>#</th><th>Client</th><th>Véhicule</th><th>Année / Km</th><th>Prix souhaité</th><th>Date</th><th>Statut</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($selling as $s): ?>
                    <tr class="<?= $s['status']==='new'?'table-row-new':'' ?>">
                        <td><?= $s['id'] ?></td>
                        <td>
                            <strong><?= htmlspecialchars($s['first_name'].' '.$s['last_name']) ?></strong><br>
                            <small><?= htmlspecialchars($s['phone']) ?></small>
                        </td>
                        <td><strong><?= htmlspecialchars($s['brand'].' '.$s['model']) ?></strong><br>
                            <small class="text-muted"><?= htmlspecialchars($s['version']??'') ?></small></td>
                        <td><?= $s['year'] ?> / <?= number_format($s['mileage'],0,',',' ') ?> km</td>
                        <td><?= $s['desired_price'] ? number_format($s['desired_price'],0,',',' ').' €' : '—' ?></td>
                        <td><small><?= date('d/m/Y H:i', strtotime($s['created_at'])) ?></small></td>
                        <td>
                            <?php $sb=['new'=>'danger','evaluating'=>'warning','accepted'=>'success','rejected'=>'secondary','sold'=>'info'];
                            $sl=['new'=>'Nouvelle','evaluating'=>'Évaluation','accepted'=>'Acceptée','rejected'=>'Refusée','sold'=>'Vendu'];
                            $st=$s['status'];
                            echo '<span class="badge bg-'.($sb[$st]??'secondary').'">'.($sl[$st]??$st).'</span>';
                            ?>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/demandes/viewSelling/<?= $s['id'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye me-1"></i>Voir
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($selling)): ?>
                    <tr><td colspan="8" class="text-center py-4 text-muted">Aucune demande de dépôt-vente.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>
