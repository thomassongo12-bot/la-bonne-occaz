<!-- Stats cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card stat-card--primary">
            <div class="stat-card__icon"><i class="fas fa-car"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__num"><?= $stats['total'] ?? 0 ?></div>
                <div class="stat-card__label">Véhicules total</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card stat-card--success">
            <div class="stat-card__icon"><i class="fas fa-check-circle"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__num"><?= $stats['available'] ?? 0 ?></div>
                <div class="stat-card__label">Disponibles</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card stat-card--info">
            <div class="stat-card__icon"><i class="fas fa-inbox"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__num"><?= ($newContacts ?? 0) + ($newSelling ?? 0) ?></div>
                <div class="stat-card__label">Nouvelles demandes</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card stat-card--warning">
            <div class="stat-card__icon"><i class="fas fa-tags"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__num"><?= $stats['sold'] ?? 0 ?></div>
                <div class="stat-card__label">Vendus</div>
            </div>
        </div>
    </div>
</div>

<!-- Stats trafic rapides -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="admin-card p-3 d-flex align-items-center gap-3">
            <div class="stat-card__icon stat-card--primary m-0" style="width:46px;height:46px;border-radius:12px;background:rgba(230,57,70,.1);color:#e63946;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <div style="font-size:1.5rem;font-weight:800;color:#1e293b;line-height:1"><?= number_format($totalVisiteurs ?? 0) ?></div>
                <div style="font-size:.75rem;color:#64748b">Visiteurs (30 jours)</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-card p-3 d-flex align-items-center gap-3">
            <div style="width:46px;height:46px;border-radius:12px;background:rgba(59,130,246,.1);color:#3b82f6;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <div style="font-size:1.5rem;font-weight:800;color:#1e293b;line-height:1"><?= $moyVisiteurs ?? 0 ?></div>
                <div style="font-size:.75rem;color:#64748b">Moyenne / jour</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-card p-3 d-flex align-items-center gap-3">
            <div style="width:46px;height:46px;border-radius:12px;background:rgba(234,179,8,.1);color:#eab308;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0">
                <i class="fas fa-eye"></i>
            </div>
            <div>
                <div style="font-size:1.5rem;font-weight:800;color:#1e293b;line-height:1"><?= number_format(array_sum(array_slice(array_values($trafficFull ?? []), -7))) ?></div>
                <div style="font-size:.75rem;color:#64748b">Visiteurs (7 derniers jours)</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Graphique trafic -->
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="admin-card__header d-flex justify-content-between align-items-center">
                <h3><i class="fas fa-chart-area me-2"></i>Trafic visiteurs — 30 derniers jours</h3>
                <span class="badge bg-primary"><?= number_format($totalVisiteurs ?? 0) ?> visites</span>
            </div>
            <div class="admin-card__body">
                <canvas id="trafficChart" height="90"></canvas>
            </div>
        </div>
    </div>

    <!-- Dernières demandes -->
    <div class="col-lg-4">
        <div class="admin-card">
            <div class="admin-card__header d-flex justify-content-between align-items-center">
                <h3><i class="fas fa-bell me-2"></i>Nouvelles demandes</h3>
                <a href="<?= BASE_URL ?>/admin/demandes" class="btn btn-sm btn-outline-primary">Tout voir</a>
            </div>
            <div class="admin-card__body p-0">
                <?php if ($newContacts + $newSelling === 0): ?>
                <div class="p-4 text-center text-muted">
                    <i class="fas fa-check-circle fa-2x mb-2 text-success d-block"></i>
                    Aucune nouvelle demande
                </div>
                <?php else: ?>
                <div class="demand-quick-list">
                    <?php if ($newContacts > 0): ?>
                    <a href="<?= BASE_URL ?>/admin/demandes?status=new&type=contact" class="demand-quick-item">
                        <i class="fas fa-envelope text-primary"></i>
                        <span><?= $newContacts ?> demande<?= $newContacts > 1 ? 's' : '' ?> de contact</span>
                        <i class="fas fa-arrow-right ms-auto"></i>
                    </a>
                    <?php endif; ?>
                    <?php if ($newSelling > 0): ?>
                    <a href="<?= BASE_URL ?>/admin/demandes?status=new" class="demand-quick-item">
                        <i class="fas fa-car text-warning"></i>
                        <span><?= $newSelling ?> dépôt<?= $newSelling > 1 ? 's' : '' ?>-vente</span>
                        <i class="fas fa-arrow-right ms-auto"></i>
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Derniers véhicules -->
    <div class="col-12">
        <div class="admin-card">
            <div class="admin-card__header d-flex justify-content-between align-items-center">
                <h3><i class="fas fa-car me-2"></i>Derniers véhicules ajoutés</h3>
                <a href="<?= BASE_URL ?>/admin/vehicules" class="btn btn-sm btn-outline-primary">Voir tout</a>
            </div>
            <div class="admin-card__body p-0">
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Photo</th>
                                <th>Véhicule</th>
                                <th>Année</th>
                                <th>Prix</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent as $v): ?>
                            <tr>
                                <td>
                                    <img src="<?= !empty($v['main_image']) ? UPLOADS_URL.'/vehicles/'.$v['main_image'] : BASE_URL.'/public/images/no-vehicle.svg' ?>"
                                         alt="" class="admin-table-thumb"
                                         >
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars(($v['brand_name'] ?? '') . ' ' . $v['model']) ?></strong><br>
                                    <small class="text-muted"><?= number_format($v['mileage'], 0, ',', ' ') ?> km</small>
                                </td>
                                <td><?= $v['year'] ?></td>
                                <td><strong><?= number_format($v['price'], 0, ',', ' ') ?> €</strong></td>
                                <td><?php
                                    $badges = ['available'=>'success','sold'=>'danger','reserved'=>'warning','draft'=>'secondary','pending'=>'info'];
                                    $labels = ['available'=>'Disponible','sold'=>'Vendu','reserved'=>'Réservé','draft'=>'Brouillon','pending'=>'En attente'];
                                    $s = $v['status'];
                                    echo '<span class="badge bg-'  .($badges[$s]??'secondary').'">'  .($labels[$s]??$s).'</span>';
                                ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/admin/vehicules/edit/<?= $v['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?= BASE_URL ?>/vehicule/<?= htmlspecialchars($v['slug'] ?? '') ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Données trafic 30 jours
const trafficLabels = <?= json_encode(array_map(function($d) {
    return date('d/m', strtotime($d));
}, array_keys($trafficFull ?? []))) ?>;
const trafficValues = <?= json_encode(array_values($trafficFull ?? [])) ?>;

document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('trafficChart');
    if (!ctx) return;

    // Gradient
    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0,   'rgba(230, 57, 70, 0.35)');
    gradient.addColorStop(1,   'rgba(230, 57, 70, 0.02)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: trafficLabels,
            datasets: [{
                label: 'Visiteurs',
                data: trafficValues,
                borderColor: '#e63946',
                borderWidth: 2.5,
                pointRadius: 3,
                pointBackgroundColor: '#e63946',
                pointHoverRadius: 6,
                fill: true,
                backgroundColor: gradient,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#94a3b8',
                    bodyColor: '#fff',
                    padding: 10,
                    callbacks: {
                        label: ctx => ' ' + ctx.parsed.y + ' visiteur' + (ctx.parsed.y > 1 ? 's' : '')
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        color: '#94a3b8', font: { size: 10 },
                        maxTicksLimit: 10,
                        maxRotation: 0,
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,.05)' },
                    ticks: { color: '#94a3b8', stepSize: 5, font: { size: 11 } }
                }
            }
        }
    });
});
</script>

