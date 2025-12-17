// Theme default sesuai UI kamu (bisa disesuaikan)
const swalBase = {
  background: 'rgba(24, 18, 43, 0.98)',
  color: '#E7E4FF',
  confirmButtonText: 'OK',
  showConfirmButton: true,
  confirmButtonColor: '#635985',
  timerProgressBar: true,
  customClass: {
    popup: 'swal2-dark-rounded',
    confirmButton: 'swal2-btn-confirm',
    cancelButton: 'swal2-btn-cancel'
  }
};

// API universal
window.flash = function flash({ icon='info', title='', text='', html=null, toast=false, timer=null, position='center', showCancelButton=false, cancelButtonText='Batal' } = {}) {
  const opts = {
    ...swalBase,
    icon, title, position, toast,
    ...(html ? { html } : { text }),
    ...(timer ? { timer } : {}),
    ...(toast ? { showConfirmButton: false } : {}),
    ...(showCancelButton ? { showCancelButton, cancelButtonText } : {})
  };
  return Swal.fire(opts);
};

// Helper cepat
window.flashSuccess = (msg, opts={}) => flash({ icon:'success', title:'Berhasil!', text:msg, timer:2200, ...opts });
window.flashError   = (msg, opts={}) => flash({ icon:'error',   title:'Gagal',     text:msg, ...opts });
window.flashWarn    = (msg, opts={}) => flash({ icon:'warning', title:'Perhatian', text:msg, ...opts });
window.flashInfo    = (msg, opts={}) => flash({ icon:'info',    title:'Info',      text:msg, ...opts });

// ====== Loader universal untuk <x-flash /> ======
document.addEventListener('DOMContentLoaded', function () {
  const el = document.getElementById('flash-json');
  if (!el) return;

  let data = {};
  try { data = JSON.parse(el.textContent || '{}'); } catch (_) {}

  const escapeHtml = (s) => String(s)
    .replace(/&/g, '&amp;').replace(/</g, '&lt;')
    .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');

  if (data.success) window.flashSuccess(data.success);
  if (data.info)    window.flashInfo(data.info);
  if (data.warning) window.flashWarn(data.warning);
  if (data.error)   window.flashError(data.error);

  if (Array.isArray(data.errors) && data.errors.length) {
    const html = data.errors.map(escapeHtml).join('<br>');
    window.flash({ icon: 'error', title: 'Validasi gagal', html });
  }
});
