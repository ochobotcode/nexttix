// NexTix Main JS

// ── Mobile Menu ────────────────────────────────────────────
const hamBtn      = document.getElementById('hamBtn');
const mobileMenu  = document.getElementById('mobileMenu');
const mobileClose = document.getElementById('mobileClose');

hamBtn?.addEventListener('click', () => mobileMenu?.classList.add('open'));
mobileClose?.addEventListener('click', () => mobileMenu?.classList.remove('open'));
mobileMenu?.addEventListener('click', e => {
    if (e.target === mobileMenu) mobileMenu.classList.remove('open');
});

// ── Auto-hide alerts ───────────────────────────────────────
document.querySelectorAll('.alert').forEach(el => {
    setTimeout(() => el.style.transition = 'opacity .5s', 3500);
    setTimeout(() => el.style.opacity = '0', 3500);
    setTimeout(() => el.remove(), 4100);
});

// ── Admin Sidebar Toggle ───────────────────────────────────
const sideToggle = document.getElementById('sideToggle');
const sidebar    = document.getElementById('sidebar');
sideToggle?.addEventListener('click', () => sidebar?.classList.toggle('open'));

// ── Delete confirm modal ───────────────────────────────────
function openDeleteModal(url, name) {
    const modal = document.getElementById('deleteModal');
    const desc  = document.getElementById('deleteDesc');
    const btn   = document.getElementById('deleteBtn');
    if (!modal) return;
    if (desc) desc.textContent = `Yakin hapus "${name}"? Tindakan ini tidak dapat dibatalkan.`;
    if (btn)  btn.href = url;
    modal.classList.add('open');
}
function closeModal(id) {
    document.getElementById(id)?.classList.remove('open');
}

// ── Ticket quantity ────────────────────────────────────────
document.querySelectorAll('.qty-control').forEach(ctrl => {
    const plus  = ctrl.querySelector('.qty-plus');
    const minus = ctrl.querySelector('.qty-minus');
    const num   = ctrl.querySelector('.qty-num');
    const input = ctrl.closest('.ticket-item')?.querySelector('.qty-hidden');
    const max   = parseInt(ctrl.dataset.max || '10');

    const update = v => {
        const n = Math.max(0, Math.min(max, v));
        if (num)   num.textContent = n;
        if (input) input.value = n;
        const card = ctrl.closest('.ticket-item');
        if (card) card.classList.toggle('selected', n > 0);
    };

    plus?.addEventListener('click',  () => update(parseInt(num?.textContent||'0') + 1));
    minus?.addEventListener('click', () => update(parseInt(num?.textContent||'0') - 1));
});

// ── Poster preview ─────────────────────────────────────────
const posterInput = document.getElementById('posterInput');
const posterPreview = document.getElementById('posterPreview');
posterInput?.addEventListener('change', e => {
    const file = e.target.files?.[0];
    if (file && posterPreview) {
        const url = URL.createObjectURL(file);
        posterPreview.src = url;
        posterPreview.style.display = 'block';
    }
});

// ── Table search live filter ───────────────────────────────
const liveSearch = document.getElementById('liveSearch');
liveSearch?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('tbody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
