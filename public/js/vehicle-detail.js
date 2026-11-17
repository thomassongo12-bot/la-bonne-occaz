/**
 * La Bonne Occaz — Galerie véhicule v5
 */
'use strict';

document.addEventListener('DOMContentLoaded', () => {

    /* ── Données ── */
    if (!window.vehicleImages || vehicleImages.length < 1) return;
    const total = vehicleImages.length;
    let current = 0;

    /* ── DOM ── */
    const mainImg   = document.getElementById('mainGalleryImg');
    const counter   = document.getElementById('galleryCounter');
    const thumbWrap = document.getElementById('galleryThumbs');
    const modal     = document.getElementById('galleryModal');
    const modalImg  = document.getElementById('galleryModalImg');
    const modalCtr  = document.getElementById('modalCounter');

    if (!mainImg) return;

    /* ────────────────────────────────────────
       go(idx) — change l'image affichée
    ──────────────────────────────────────── */
    function go(idx) {
        idx = ((idx % total) + total) % total;
        current = idx;

        const url = vehicleImages[idx];

        /* 1. Changer le src directement — le navigateur charge l'image */
        mainImg.src = url;

        /* 2. Compteur */
        if (counter) counter.textContent = (idx + 1) + ' / ' + total;

        /* 3. Mettre à jour les miniatures actives */
        if (thumbWrap) {
            thumbWrap.querySelectorAll('.gallery-thumb').forEach((el, i) => {
                el.classList.toggle('gallery-thumb--active', i === idx);
            });
        }

        /* 4. Mettre à jour la modal si elle est ouverte */
        if (modal && modal.classList.contains('open') && modalImg) {
            modalImg.src = url;
            if (modalCtr) modalCtr.textContent = (idx + 1) + ' / ' + total;
        }
    }

    /* ── Miniatures : délégation d'événements ── */
    if (thumbWrap) {
        thumbWrap.addEventListener('click', function(e) {
            const thumb = e.target.closest('.gallery-thumb');
            if (!thumb) return;
            const all = thumbWrap.querySelectorAll('.gallery-thumb');
            for (let i = 0; i < all.length; i++) {
                if (all[i] === thumb) { go(i); return; }
            }
        });
    }

    /* ── Flèches ── */
    var btnPrev = document.getElementById('galleryPrev');
    var btnNext = document.getElementById('galleryNext');
    if (btnPrev) btnPrev.addEventListener('click', function(e) { e.stopPropagation(); go(current - 1); });
    if (btnNext) btnNext.addEventListener('click', function(e) { e.stopPropagation(); go(current + 1); });

    /* ── Clavier ── */
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft')  go(current - 1);
        if (e.key === 'ArrowRight') go(current + 1);
        if (e.key === 'Escape')     closeModal();
    });

    /* ── Modal plein écran ── */
    function openModal() {
        if (!modal || !modalImg) return;
        modalImg.src = vehicleImages[current];
        if (modalCtr) modalCtr.textContent = (current + 1) + ' / ' + total;
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeModal() {
        if (!modal) return;
        modal.classList.remove('open');
        document.body.style.overflow = '';
    }

    var fullBtn  = document.getElementById('galleryFullscreenBtn');
    var closeBtn = document.getElementById('galleryModalClose');
    var overlay  = document.getElementById('galleryModalOverlay');
    var mprev    = document.getElementById('modalPrev');
    var mnext    = document.getElementById('modalNext');

    if (fullBtn)  fullBtn.addEventListener('click',  function(e) { e.stopPropagation(); openModal(); });
    if (closeBtn) closeBtn.addEventListener('click',  closeModal);
    if (overlay)  overlay.addEventListener('click',   closeModal);
    if (mprev)    mprev.addEventListener('click',     function(e) { e.stopPropagation(); go(current - 1); });
    if (mnext)    mnext.addEventListener('click',     function(e) { e.stopPropagation(); go(current + 1); });

    /* Clic sur l'image principale = plein écran */
    mainImg.addEventListener('click', function() { openModal(); });
    mainImg.style.cursor = 'zoom-in';

    /* ── Swipe galerie ── */
    var galleryMain = document.getElementById('galleryMain');
    var tx = 0, ty = 0;
    if (galleryMain) {
        galleryMain.addEventListener('touchstart', function(e) {
            tx = e.touches[0].clientX;
            ty = e.touches[0].clientY;
        }, { passive: true });
        galleryMain.addEventListener('touchend', function(e) {
            var dx = tx - e.changedTouches[0].clientX;
            var dy = ty - e.changedTouches[0].clientY;
            if (Math.abs(dx) > 40 && Math.abs(dx) > Math.abs(dy))
                go(dx > 0 ? current + 1 : current - 1);
        }, { passive: true });
    }

    /* ── Swipe modal ── */
    var mx = 0;
    if (modal) {
        modal.addEventListener('touchstart', function(e) { mx = e.touches[0].clientX; }, { passive: true });
        modal.addEventListener('touchend', function(e) {
            var dx = mx - e.changedTouches[0].clientX;
            if (Math.abs(dx) > 40) go(dx > 0 ? current + 1 : current - 1);
        }, { passive: true });
    }

    /* ── S'assurer que la première image est visible ── */
    mainImg.style.opacity = '1';
    mainImg.style.transition = '';

});
