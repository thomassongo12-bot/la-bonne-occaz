<!-- ================================================================
     CATALOGUE VÉHICULES
================================================================ -->
<section class="page-hero page-hero--catalog">
    <div class="container">
        <nav aria-label="breadcrumb" class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Accueil</a></li>
                <li class="breadcrumb-item active">Nos véhicules</li>
            </ol>
        </nav>
        <h1 class="page-hero__title">Nos véhicules</h1>
        <p class="page-hero__subtitle"><?= $total ?> véhicule<?= $total > 1 ? 's' : '' ?> disponible<?= $total > 1 ? 's' : '' ?></p>
    </div>
</section>

<section class="catalog-section section-padding-sm">
    <div class="container">
        <div class="row g-4">

            <!-- ============ SIDEBAR FILTRES ============ -->
            <div class="col-lg-3">
                <div class="filter-sidebar" id="filterSidebar">
                    <div class="filter-sidebar__header">
                        <h2 class="filter-title"><i class="fas fa-sliders-h me-2"></i>Filtres</h2>
                        <a href="<?= BASE_URL ?>/vehicules" class="filter-reset">Réinitialiser</a>
                    </div>

                    <form action="<?= BASE_URL ?>/vehicules" method="GET" id="filterForm">

                        <!-- Marque -->
                        <div class="filter-group">
                            <h3 class="filter-group__title">Marque</h3>
                            <select name="brand_id" class="form-select filter-select">
                                <option value="">Toutes les marques</option>
                                <?php foreach ($brands as $b): ?>
                                <option value="<?= $b['id'] ?>"
                                    <?= (isset($filters['brand_id']) && $filters['brand_id'] == $b['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($b['name']) ?>
                                    <?php if ($b['vehicle_count'] > 0): ?>(<?= $b['vehicle_count'] ?>)<?php endif; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Carrosserie -->
                        <div class="filter-group">
                            <h3 class="filter-group__title">Type de carrosserie</h3>
                            <select name="body_type_id" class="form-select filter-select">
                                <option value="">Toutes</option>
                                <?php foreach ($body_types as $bt): ?>
                                <option value="<?= $bt['id'] ?>"
                                    <?= (isset($filters['body_type_id']) && $filters['body_type_id'] == $bt['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($bt['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Carburant -->
                        <div class="filter-group">
                            <h3 class="filter-group__title">Carburant</h3>
                            <div class="filter-checkboxes">
                                <?php foreach ($fuel_types as $f): ?>
                                <label class="filter-checkbox">
                                    <input type="radio" name="fuel_type_id" value="<?= $f['id'] ?>"
                                        <?= (isset($filters['fuel_type_id']) && $filters['fuel_type_id'] == $f['id']) ? 'checked' : '' ?>>
                                    <?= htmlspecialchars($f['name']) ?>
                                </label>
                                <?php endforeach; ?>
                                <label class="filter-checkbox">
                                    <input type="radio" name="fuel_type_id" value=""
                                        <?= empty($filters['fuel_type_id']) ? 'checked' : '' ?>>
                                    Tous les carburants
                                </label>
                            </div>
                        </div>

                        <!-- Transmission -->
                        <div class="filter-group">
                            <h3 class="filter-group__title">Transmission</h3>
                            <div class="filter-checkboxes">
                                <?php foreach ($transmissions as $t): ?>
                                <label class="filter-checkbox">
                                    <input type="radio" name="transmission_id" value="<?= $t['id'] ?>"
                                        <?= (isset($filters['transmission_id']) && $filters['transmission_id'] == $t['id']) ? 'checked' : '' ?>>
                                    <?= htmlspecialchars($t['name']) ?>
                                </label>
                                <?php endforeach; ?>
                                <label class="filter-checkbox">
                                    <input type="radio" name="transmission_id" value=""
                                        <?= empty($filters['transmission_id']) ? 'checked' : '' ?>>
                                    Toutes
                                </label>
                            </div>
                        </div>

                        <!-- Budget -->
                        <div class="filter-group">
                            <h3 class="filter-group__title">Budget (€)</h3>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" name="price_min" class="form-control filter-input" placeholder="Min"
                                           value="<?= htmlspecialchars($filters['price_min'] ?? '') ?>">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="price_max" class="form-control filter-input" placeholder="Max"
                                           value="<?= htmlspecialchars($filters['price_max'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Année -->
                        <div class="filter-group">
                            <h3 class="filter-group__title">Année</h3>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" name="year_min" class="form-control filter-input" placeholder="De"
                                           value="<?= htmlspecialchars($filters['year_min'] ?? '') ?>" min="2000" max="<?= date('Y') ?>">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="year_max" class="form-control filter-input" placeholder="À"
                                           value="<?= htmlspecialchars($filters['year_max'] ?? '') ?>" min="2000" max="<?= date('Y') ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Kilométrage max -->
                        <div class="filter-group">
                            <h3 class="filter-group__title">Kilométrage max.</h3>
                            <select name="mileage_max" class="form-select filter-select">
                                <option value="">Sans limite</option>
                                <?php foreach ([10000,30000,50000,80000,100000,150000,200000] as $km): ?>
                                <option value="<?= $km ?>"
                                    <?= (isset($filters['mileage_max']) && $filters['mileage_max'] == $km) ? 'selected' : '' ?>>
                                    <?= number_format($km, 0, ',', ' ') ?> km
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Puissance min -->
                        <div class="filter-group">
                            <h3 class="filter-group__title">Puissance min. (ch)</h3>
                            <select name="power_min" class="form-select filter-select">
                                <option value="">Sans limite</option>
                                <?php foreach ([70,90,100,120,150,180,200,250,300] as $pw): ?>
                                <option value="<?= $pw ?>"
                                    <?= (isset($filters['power_min']) && $filters['power_min'] == $pw) ? 'selected' : '' ?>>
                                    <?= $pw ?> ch
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Couleur -->
                        <div class="filter-group">
                            <h3 class="filter-group__title">Couleur</h3>
                            <input type="text" name="color" class="form-control filter-input" placeholder="Ex : Blanc, Noir..."
                                   value="<?= htmlspecialchars($filters['color'] ?? '') ?>">
                        </div>

                        <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
                        <button type="submit" class="btn btn-primary-custom w-100 mt-2">
                            <i class="fas fa-search me-2"></i>Appliquer les filtres
                        </button>
                    </form>
                </div>
            </div>

            <!-- ============ RÉSULTATS ============ -->
            <div class="col-lg-9">

                <!-- Barre de contrôle -->
                <div class="catalog-toolbar">
                    <div class="catalog-toolbar__left">
                        <!-- Recherche textuelle -->
                        <div class="search-inline">
                            <form action="<?= BASE_URL ?>/vehicules" method="GET" class="d-flex gap-2">
                                <?php foreach ($filters as $k => $v): if ($k === 'search') continue; ?>
                                <input type="hidden" name="<?= htmlspecialchars($k) ?>" value="<?= htmlspecialchars($v) ?>">
                                <?php endforeach; ?>
                                <input type="text" name="search" class="form-control"
                                       placeholder="Rechercher une marque, modèle..."
                                       value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                                <button type="submit" class="btn btn-primary-custom px-3">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="catalog-toolbar__right d-flex align-items-center gap-3">
                        <!-- Tri -->
                        <div class="sort-wrapper">
                            <select class="form-select form-select-sm" id="sortSelect" onchange="applySort(this.value)">
                                <option value="newest"    <?= $sort==='newest'    ? 'selected':'' ?>>Plus récents</option>
                                <option value="price_asc" <?= $sort==='price_asc' ? 'selected':'' ?>>Prix croissant</option>
                                <option value="price_desc"<?= $sort==='price_desc'? 'selected':'' ?>>Prix décroissant</option>
                                <option value="year_desc" <?= $sort==='year_desc' ? 'selected':'' ?>>Année récente</option>
                                <option value="mileage"   <?= $sort==='mileage'   ? 'selected':'' ?>>Kilométrage</option>
                            </select>
                        </div>
                        <!-- Filtre mobile -->
                        <button class="btn btn-outline-secondary btn-sm d-lg-none" id="filterToggleBtn">
                            <i class="fas fa-sliders-h me-1"></i>Filtres
                        </button>
                    </div>
                </div>

                <!-- Résultats -->
                <div class="catalog-results-info">
                    <span><?= $total ?> véhicule<?= $total > 1 ? 's' : '' ?> trouvé<?= $total > 1 ? 's' : '' ?></span>
                </div>

                <!-- Grille -->
                <div class="row g-4" id="catalogGrid">
                    <?php if (!empty($vehicles)): ?>
                        <?php foreach ($vehicles as $v): ?>
                            <?php include VIEWS_PATH . '/vehicles/_vehicle_card.php'; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="no-results">
                                <i class="fas fa-car-crash fa-3x mb-3"></i>
                                <h3>Aucun véhicule trouvé</h3>
                                <p>Modifiez vos critères de recherche pour voir plus de résultats.</p>
                                <a href="<?= BASE_URL ?>/vehicules" class="btn btn-primary-custom mt-2">Voir tous les véhicules</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if ($pages > 1): ?>
                <nav aria-label="Pagination" class="mt-5">
                    <ul class="pagination pagination-custom justify-content-center">
                        <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= BASE_URL ?>/vehicules?<?= http_build_query(array_merge($filters, ['sort'=>$sort,'page'=>$page-1])) ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php for ($i = max(1, $page-2); $i <= min($pages, $page+2); $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="<?= BASE_URL ?>/vehicules?<?= http_build_query(array_merge($filters, ['sort'=>$sort,'page'=>$i])) ?>">
                                <?= $i ?>
                            </a>
                        </li>
                        <?php endfor; ?>

                        <?php if ($page < $pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= BASE_URL ?>/vehicules?<?= http_build_query(array_merge($filters, ['sort'=>$sort,'page'=>$page+1])) ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>

<script>
function applySort(val) {
    const url = new URL(window.location.href);
    url.searchParams.set('sort', val);
    url.searchParams.delete('page');
    window.location.href = url.toString();
}

// Toggle filtres mobile
document.getElementById('filterToggleBtn')?.addEventListener('click', () => {
    const sidebar = document.getElementById('filterSidebar');
    sidebar.classList.toggle('filter-sidebar--open');
});
</script>
