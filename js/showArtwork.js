/* ========= UTIL ========= */
function $(sel, base) { return (base || document).querySelector(sel); }
function $all(sel, base) { return Array.from((base || document).querySelectorAll(sel)); }

function getIndexUrl() {
  const meta = document.querySelector('meta[name="artworks-index-url"]');
  return meta ? meta.content : '/artworks'; // fallback jika meta lupa dipasang
}

function getCsrf() {
  const meta = document.querySelector('meta[name="csrf-token"]');
  if (meta && meta.getAttribute) return meta.getAttribute('content');
  const input = document.querySelector('input[name="_token"]');
  return input?.value || null;
}

/* ========= KOMENTAR: INPUT ANTI HISTORI / AUTOFILL =========
   - Input text visible dijadikan dummy (name diacak)
   - Input hidden bernama 'isi_komentar' yang dikirim ke server
*/
(function setupCommentInputNoHistory() {
  const form = $('#comment-form');
  if (!form) return;

  const vis = $('input[name="isi_komentar"]', form);
  if (!vis) return;

  // hidden field yang dikirim ke server
  const real = document.createElement('input');
  real.type = 'hidden';
  real.name = 'isi_komentar';
  real.id = 'realComment';
  form.appendChild(real);

  // ubah visible input jadi dummy
  const randName = 'cmt_' + Math.random().toString(36).slice(2);
  vis.setAttribute('name', randName);
  vis.id = randName;

  // matikan autofill/histori
  vis.setAttribute('autocomplete', 'off');
  vis.setAttribute('autocapitalize', 'off');
  vis.setAttribute('autocorrect', 'off');
  vis.setAttribute('spellcheck', 'false');
  vis.setAttribute('data-lpignore', 'true');
  vis.readOnly = true;

  const unlock = () => {
    vis.readOnly = false;
    vis.removeEventListener('focus', unlock);
    vis.removeEventListener('mousedown', unlock);
  };
  vis.addEventListener('focus', unlock);
  vis.addEventListener('mousedown', unlock);

  // salin nilai ke hidden saat submit
  form.addEventListener('submit', function () {
    real.value = (vis.value || '').trim();
  });
})();

function getVisibleCommentInput() {
  const form = $('#comment-form');
  if (!form) return null;
  const input = form.querySelector('input[type="text"]');
  return input || null;
}

/* ========= REPLY / CANCEL LOGIC ========= */
document.addEventListener('click', function (e) {
  // klik "Balas"
  if (e.target.matches('.reply-link')) {
    e.preventDefault();

    const id   = e.target.getAttribute('data-id')   || '';
    const name = e.target.getAttribute('data-name') || 'User';
    const text = e.target.getAttribute('data-text') || '';

    const form   = $('#comment-form');
    const banner = $('#reply-banner');
    const hidPid = form ? $('input[name="parent_comment_id"]', form) : null;

    if (hidPid) hidPid.value = id;
    if (banner) banner.style.display = 'block';

    const rn = $('#reply-name');
    const rt = $('#reply-text');
    if (rn) rn.textContent = name;
    if (rt) rt.textContent = '“' + (text.length > 140 ? (text.slice(0, 137) + '…') : text) + '”';

    const visInput = getVisibleCommentInput();
    if (visInput) {
      visInput.focus();
      visInput.placeholder = 'Balas ' + name + '...';
    }

    form && form.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }

  // klik "Batal" balasan
  if (e.target.matches('#reply-cancel')) {
    const form   = $('#comment-form');
    const banner = $('#reply-banner');
    const hidPid = form ? $('input[name="parent_comment_id"]', form) : null;

    if (hidPid) hidPid.value = '';
    if (banner) banner.style.display = 'none';

    const visInput = getVisibleCommentInput();
    if (visInput) visInput.placeholder = 'Tulis komentar apresiatif di sini...';
  }
});

// Batal balasan dengan ESC
document.addEventListener('keydown', function (e) {
  if (e.key !== 'Escape') return;

  const banner = $('#reply-banner');
  if (!banner || banner.style.display !== 'block') return;

  const form   = $('#comment-form');
  const hidPid = form ? $('input[name="parent_comment_id"]', form) : null;
  if (hidPid) hidPid.value = '';

  banner.style.display = 'none';

  const visInput = getVisibleCommentInput();
  if (visInput) visInput.placeholder = 'Tulis komentar apresiatif di sini...';
});

/* ========= PRETTY CONFIRM DELETE (SweetAlert2) ========= */
document.addEventListener('click', function (e) {
  const delBtn = e.target.closest('.comment-delete');
  if (!delBtn) return;

  e.preventDefault();

  const cid  = delBtn.getAttribute('data-id');
  const form = document.getElementById('del-' + cid);
  if (!form) return;

  Swal.fire({
    title: 'Hapus komentar ini?',
    text: 'Tindakan ini tidak bisa dibatalkan.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, hapus',
    cancelButtonText: 'Batal',
    reverseButtons: true,
    background: 'rgba(24,18,43,0.98)',
    color: '#E7E4FF',
    iconColor: '#ff7675',
    customClass: {
      popup: 'swal2-dark-rounded',
      confirmButton: 'swal2-btn-confirm',
      cancelButton: 'swal2-btn-cancel'
    }
  }).then((result) => {
    if (result.isConfirmed) {
      form.submit();
    }
  });
});

/* ========= LIKE BUTTON (AJAX) ========= */
document.addEventListener('DOMContentLoaded', function () {
  const btn = document.getElementById('btn-like');
  if (!btn) return;

  const filled  = document.getElementById('icon-like-filled');
  const outline = document.getElementById('icon-like-outline');
  const countEl = document.getElementById('like-count');
  const url     = btn.dataset.url;
  let liked     = btn.dataset.liked === '1';

  function toggleIcons(isLiked) {
    if (!filled || !outline) return;
    if (isLiked) {
      filled.classList.remove('hidden');
      outline.classList.add('hidden');
    } else {
      filled.classList.add('hidden');
      outline.classList.remove('hidden');
    }
  }

  btn.addEventListener('click', async () => {
    try {
      const csrf = getCsrf();
      if (!csrf) throw new Error('CSRF token tidak ditemukan.');

      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json',
        },
      });

      if (!res.ok) throw new Error('Gagal memproses like.');
      const data = await res.json();

      liked = !!data.liked;
      btn.dataset.liked = liked ? '1' : '0';
      toggleIcons(liked);

      if (typeof data.likes_count === 'number' && countEl) {
        countEl.textContent = data.likes_count;
      }
    } catch (e) {
      if (window.Swal)
        Swal.fire('Oops', e.message || 'Terjadi kesalahan.', 'error');
      else
        alert(e.message || 'Terjadi kesalahan.');
    }
  });
});

/* ========= BOOKMARK BUTTON (AJAX) ========= */
document.addEventListener('DOMContentLoaded', function () {
  const btn = document.getElementById('btn-bookmark');
  if (!btn) return;

  const filled    = document.getElementById('icon-bookmark-filled');
  const outline   = document.getElementById('icon-bookmark-outline');
  const url       = btn.dataset.url;
  let bookmarked  = btn.dataset.bookmarked === '1';

  function toggleIcons(isBookmarked) {
    if (!filled || !outline) return;
    if (isBookmarked) {
      filled.classList.remove('hidden');
      outline.classList.add('hidden');
    } else {
      filled.classList.add('hidden');
      outline.classList.remove('hidden');
    }
  }

  btn.addEventListener('click', async () => {
    try {
      const csrf = getCsrf();
      if (!csrf) throw new Error('CSRF token tidak ditemukan.');

      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json',
        },
      });

      if (!res.ok) {
        console.error('Bookmark error status:', res.status);
        throw new Error('Gagal memproses bookmark.');
      }

      const data = await res.json();

      bookmarked = !!data.bookmarked;
      btn.dataset.bookmarked = bookmarked ? '1' : '0';
      toggleIcons(bookmarked);

      if (typeof data.favorites_count === 'number') {
        const el = document.getElementById('bookmark-count');
        if (el) el.textContent = data.favorites_count;
      }
    } catch (e) {
      console.error(e);
      if (window.Swal)
        Swal.fire('Oops', e.message || 'Terjadi kesalahan.', 'error');
      else
        alert(e.message || 'Terjadi kesalahan.');
    }
  });
});

/* ========= BACK BUTTON: KEMBALI KE HALAMAN ASAL ========= */
document.addEventListener('DOMContentLoaded', function () {
  const backBtn = document.getElementById('btn-back');
  if (!backBtn) return;

  // key unik per halaman show (per path artwork)
  const storageKey = 'artwork_back_ref_' + location.pathname;

  // Ambil referrer pertama kali masuk ke halaman ini
  let storedRef = sessionStorage.getItem(storageKey);

  // Jika belum ada, dan referrer bukan halaman ini sendiri → simpan
  if ((!storedRef || storedRef === window.location.href) &&
      document.referrer &&
      document.referrer !== window.location.href) {
    storedRef = document.referrer;
    sessionStorage.setItem(storageKey, storedRef);
  }

  backBtn.addEventListener('click', function () {
    const target = sessionStorage.getItem(storageKey);

    // 1) Kalau ada referrer asli yang tersimpan → pakai itu
    if (target && target !== window.location.href) {
      window.location.href = target;
      return;
    }

    // 2) Kalau tidak ada tapi referrer browser masih valid → pakai itu
    if (document.referrer && document.referrer !== window.location.href) {
      window.location.href = document.referrer;
      return;
    }

    // 3) Fallback terakhir → ke index artworks
    window.location.href = getIndexUrl();
  });
});
