/**
 * La Bonne Occaz - JS Admin
 */
'use strict';

document.addEventListener('DOMContentLoaded', () => {

  // ----- Sidebar toggle -----
  const sidebar  = document.getElementById('adminSidebar');
  const overlay  = document.getElementById('sidebarOverlay');
  const toggleBtn= document.getElementById('sidebarToggle');

  toggleBtn?.addEventListener('click', () => {
    sidebar?.classList.toggle('open');
    overlay?.classList.toggle('show');
  });
  overlay?.addEventListener('click', () => {
    sidebar?.classList.remove('open');
    overlay.classList.remove('show');
  });

  // ----- Auto-dismiss alerts -----
  document.querySelectorAll('.admin-flash .alert').forEach(alert => {
    setTimeout(() => {
      const bsAlert = bootstrap?.Alert?.getOrCreateInstance(alert);
      bsAlert?.close();
    }, 5000);
  });

  // ----- Confirm delete -----
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', e => {
      if (!confirm(el.dataset.confirm || 'Confirmer cette action ?')) e.preventDefault();
    });
  });

  // ----- Warranty field toggle -----
  const warCheck = document.getElementById('warCheck');
  const warField = document.getElementById('warrantyMonthsField');
  if (warCheck && warField) {
    const toggle = () => { warField.style.display = warCheck.checked ? 'block' : 'none'; };
    toggle();
    warCheck.addEventListener('change', toggle);
  }

  // ----- Status select color -----
  document.querySelectorAll('.status-select').forEach(sel => {
    const colors = { available:'#22c55e', sold:'#ef4444', reserved:'#f59e0b', draft:'#94a3b8', pending:'#3b82f6' };
    const update = () => {
      const c = colors[sel.value] || '#94a3b8';
      sel.style.borderColor = c;
      sel.style.color       = c;
    };
    update();
    sel.addEventListener('change', update);
  });

  // ----- KW auto-calc from HP -----
  const hpInput = document.querySelector('[name="power_hp"]');
  const kwInput = document.querySelector('[name="power_kw"]');
  if (hpInput && kwInput) {
    hpInput.addEventListener('input', () => {
      const hp = parseFloat(hpInput.value);
      if (!isNaN(hp) && !kwInput.value) kwInput.value = Math.round(hp * 0.7355);
    });
  }

});
