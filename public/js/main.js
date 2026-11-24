/**
 * La Bonne Occaz - JS Principal
 */
'use strict';

const LBO = (() => {

  // ================================================================
  // FAVORIS (localStorage)
  // ================================================================
  const FAVORITES_KEY = 'lbo_favorites';

  function getFavorites() {
    try { return JSON.parse(localStorage.getItem(FAVORITES_KEY) || '[]'); }
    catch { return []; }
  }

  function saveFavorites(ids) {
    localStorage.setItem(FAVORITES_KEY, JSON.stringify(ids));
  }

  function toggleFavorite(id) {
    id = parseInt(id);
    let ids = getFavorites();
    const idx = ids.indexOf(id);
    if (idx === -1) {
      ids.push(id);
    } else {
      ids.splice(idx, 1);
    }
    saveFavorites(ids);
    return idx === -1; // true = ajouté
  }

  function updateFavoriteCount() {
    const count = getFavorites().length;
    const el    = document.getElementById('favoritesCount');
    if (!el) return;
    el.textContent = count;
    el.style.display = count > 0 ? 'flex' : 'none';
  }

  function initFavorites() {
    const ids = getFavorites();
    // Marquer les boutons actifs
    document.querySelectorAll('.btn-favorite').forEach(btn => {
      const id = parseInt(btn.dataset.id);
      if (ids.includes(id)) {
        btn.classList.add('active');
        btn.setAttribute('aria-label', 'Retirer des favoris');
        btn.title = 'Retirer des favoris';
      } else {
        btn.classList.remove('active');
        btn.setAttribute('aria-label', 'Ajouter aux favoris');
        btn.title = 'Ajouter aux favoris';
      }

      btn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const added = toggleFavorite(id);
        btn.classList.toggle('active', added);
        btn.setAttribute('aria-label', added ? 'Retirer des favoris' : 'Ajouter aux favoris');
        btn.title = added ? 'Retirer des favoris' : 'Ajouter aux favoris';

        // Feedback animé
        btn.style.transform = 'scale(1.4)';
        setTimeout(() => btn.style.transform = '', 250);

        updateFavoriteCount();

        // Toast notification
        showToast(added ? '❤️ Ajouté aux favoris' : '💔 Retiré des favoris', added ? 'success' : 'info');
      });
    });
    updateFavoriteCount();
  }

  // ================================================================
  // TOAST NOTIFICATION
  // ================================================================
  function showToast(message, type = 'info') {
    const container = document.getElementById('toastContainer') || (() => {
      const c = document.createElement('div');
      c.id = 'toastContainer';
      c.style.cssText = 'position:fixed;bottom:20px;left:50%;transform:translateX(-50%);z-index:9999;display:flex;flex-direction:column;gap:8px;';
      document.body.appendChild(c);
      return c;
    })();

    const toast = document.createElement('div');
    toast.style.cssText = `
      background:${type==='success'?'#22c55e':type==='danger'?'#ef4444':'#3b82f6'};
      color:#fff; padding:.7rem 1.4rem; border-radius:50px;
      font-size:.88rem; font-weight:600; box-shadow:0 4px 20px rgba(0,0,0,.2);
      animation: toastIn .3s ease; white-space: nowrap;
    `;
    toast.textContent = message;
    container.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity .3s'; setTimeout(() => toast.remove(), 300); }, 2500);
  }

  // ================================================================
  // HEADER SCROLL
  // ================================================================
  function initHeaderScroll() {
    const header = document.getElementById('site-header');
    if (!header) return;
    const onScroll = () => header.classList.toggle('scrolled', window.scrollY > 50);
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  // ================================================================
  // BACK TO TOP
  // ================================================================
  function initBackToTop() {
    const btn = document.getElementById('backToTop');
    if (!btn) return;
    window.addEventListener('scroll', () => {
      btn.classList.toggle('show', window.scrollY > 400);
    }, { passive: true });
    btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
  }

  // ================================================================
  // FORM VALIDATION
  // ================================================================
  function initFormValidation() {
    document.querySelectorAll('form[novalidate]').forEach(form => {
      form.addEventListener('submit', e => {
        if (!form.checkValidity()) {
          e.preventDefault();
          e.stopPropagation();
          form.classList.add('was-validated');
          // Scroll vers le premier champ invalide
          const first = form.querySelector(':invalid');
          if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
          form.classList.add('was-validated');
        }
      });
    });
  }

  // ================================================================
  // AUTO-DISMISS ALERTS
  // ================================================================
  function initAlerts() {
    document.querySelectorAll('.flash-container .alert').forEach(alert => {
      setTimeout(() => {
        const bsAlert = bootstrap?.Alert?.getOrCreateInstance(alert);
        bsAlert?.close();
      }, 5000);
    });
  }

  // ================================================================
  // LAZY LOAD ANIMATIONS — exclut les éléments de galerie
  // ================================================================
  function initAnimations() {
    if (!('IntersectionObserver' in window)) return;

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0)';
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });

    // NE PAS inclure .gallery-main__img ou tout élément dans .vehicle-gallery
    document.querySelectorAll('.vehicle-card, .service-card, .testimonial-card, .why-item').forEach(el => {
      // Sécurité : ne pas toucher aux éléments dans la galerie véhicule
      if (el.closest('.vehicle-gallery, .vehicle-detail-section')) return;
      el.style.opacity = '0';
      el.style.transform = 'translateY(20px)';
      el.style.transition = 'opacity .4s ease, transform .4s ease';
      observer.observe(el);
    });
  }

  // ================================================================
  // HERO SLIDER — défilement automatique
  // ================================================================
  function initHeroSlider() {
    const slides = document.querySelectorAll('.hero-slide');
    const dots   = document.querySelectorAll('.hero-dot');
    if (!slides.length) return;

    let current  = 0;
    let timer    = null;
    const DELAY  = 5000; // 5 secondes par slide

    function goTo(index) {
      slides[current].classList.remove('hero-slide--active');
      dots[current]?.classList.remove('hero-dot--active');
      current = (index + slides.length) % slides.length;
      slides[current].classList.add('hero-slide--active');
      dots[current]?.classList.add('hero-dot--active');
    }

    function next() { goTo(current + 1); }

    function startAuto() {
      clearInterval(timer);
      timer = setInterval(next, DELAY);
    }

    // Clic sur les dots
    dots.forEach((dot, i) => {
      dot.addEventListener('click', () => { goTo(i); startAuto(); });
    });

    // Pause au survol
    const heroSection = document.getElementById('hero');
    heroSection?.addEventListener('mouseenter', () => clearInterval(timer));
    heroSection?.addEventListener('mouseleave', startAuto);

    // Pause si onglet caché
    document.addEventListener('visibilitychange', () => {
      document.hidden ? clearInterval(timer) : startAuto();
    });

    startAuto();
  }

  // ================================================================
  // CONDITIONS RÉSERVATION — accordéon accueil
  // ================================================================
  function initResConditions() {
    const toggle = document.getElementById('resConditionsToggle');
    const body   = document.getElementById('resConditionsBody');
    if (!toggle || !body) return;

    toggle.addEventListener('click', () => {
      const isOpen = body.classList.toggle('open');
      toggle.setAttribute('aria-expanded', isOpen);
    });
  }

  // ================================================================
  // INIT
  // ================================================================
  document.addEventListener('DOMContentLoaded', () => {
    initHeaderScroll();
    initBackToTop();
    initFavorites();
    initFormValidation();
    initAlerts();
    initAnimations();
    initHeroSlider();
    initResConditions();
  });

  return { initFavorites, getFavorites, showToast };
})();