<section class="page-hero">
    <div class="container">
        <nav aria-label="breadcrumb" class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Accueil</a></li>
                <li class="breadcrumb-item active">Mes favoris</li>
            </ol>
        </nav>
        <h1 class="page-hero__title"><i class="fas fa-heart me-2"></i>Mes favoris</h1>
        <p class="page-hero__subtitle">Vos véhicules sauvegardés</p>
    </div>
</section>

<section class="favorites-section section-padding">
    <div class="container">
        <div id="favoritesLoading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 text-muted">Chargement de vos favoris...</p>
        </div>

        <div id="favoritesEmpty" class="text-center py-5" style="display:none">
            <i class="fas fa-heart fa-4x text-muted mb-3 d-block"></i>
            <h3>Aucun favori sauvegardé</h3>
            <p class="text-muted">Cliquez sur le ❤ d'un véhicule pour l'ajouter à vos favoris.</p>
            <a href="<?= BASE_URL ?>/vehicules" class="btn btn-primary-custom mt-2">
                <i class="fas fa-search me-2"></i>Parcourir les véhicules
            </a>
        </div>

        <div class="row g-4" id="favoritesGrid" style="display:none"></div>

        <div id="favoritesClearBtn" class="text-center mt-4" style="display:none">
            <button class="btn btn-outline-danger" id="clearFavoritesBtn">
                <i class="fas fa-trash me-2"></i>Vider mes favoris
            </button>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const ids = JSON.parse(localStorage.getItem('lbo_favorites') || '[]');
    const loading = document.getElementById('favoritesLoading');
    const empty   = document.getElementById('favoritesEmpty');
    const grid    = document.getElementById('favoritesGrid');
    const clearBtn= document.getElementById('favoritesClearBtn');

    if (!ids.length) {
        loading.style.display = 'none';
        empty.style.display   = 'block';
        return;
    }

    fetch(`<?= BASE_URL ?>/favoris/api?ids=${ids.join(',')}`)
        .then(r => r.json())
        .then(vehicles => {
            loading.style.display = 'none';
            if (!vehicles.length) { empty.style.display = 'block'; return; }
            grid.style.display = '';
            clearBtn.style.display = '';
            vehicles.forEach(v => {
                grid.insertAdjacentHTML('beforeend', buildCard(v));
            });
            // Rebind favorite buttons
            if (window.LBO) window.LBO.initFavorites();
        })
        .catch(() => {
            loading.style.display = 'none';
            empty.style.display   = 'block';
        });

    document.getElementById('clearFavoritesBtn')?.addEventListener('click', () => {
        if (confirm('Vider tous vos favoris ?')) {
            localStorage.removeItem('lbo_favorites');
            location.reload();
        }
    });

    function buildCard(v) {
        const price = Number(v.price).toLocaleString('fr-FR') + ' €';
        return `
        <div class="col-xl-4 col-md-6">
            <article class="vehicle-card" data-vehicle-id="${v.id}">
                <div class="vehicle-card__image-wrapper">
                    <a href="${v.url}">
                        <img src="${v.image_url}" alt="${v.brand_name} ${v.model}" class="vehicle-card__image" loading="lazy">
                    </a>
                    <button class="vehicle-card__fav-btn btn-favorite active" data-id="${v.id}">
                        <i class="fas fa-heart"></i>
                    </button>
                </div>
                <div class="vehicle-card__body">
                    <div class="vehicle-card__brand">${v.brand_name}</div>
                    <h3 class="vehicle-card__model"><a href="${v.url}">${v.model} ${v.version || ''}</a></h3>
                    <div class="vehicle-card__specs">
                        <span class="spec-item"><i class="fas fa-calendar-alt"></i>${v.year}</span>
                        <span class="spec-item"><i class="fas fa-tachometer-alt"></i>${Number(v.mileage).toLocaleString('fr-FR')} km</span>
                        <span class="spec-item"><i class="fas fa-gas-pump"></i>${v.fuel_name}</span>
                        <span class="spec-item"><i class="fas fa-cogs"></i>${v.transmission_name}</span>
                    </div>
                    <div class="vehicle-card__price-row">
                        <div class="vehicle-card__price"><span class="price-current">${price}</span></div>
                        <a href="${v.url}" class="btn btn-card-cta">Voir <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </article>
        </div>`;
    }
});
</script>
