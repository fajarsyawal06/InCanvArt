// public/js/infinite-masonry.js
(function(){
  const sentinel = document.getElementById('next-page');
  if(!sentinel) return;

  const masonry = document.getElementById('masonry');
  const loading = document.getElementById('loading');

  let next = sentinel.dataset.next || null;
  let busy = false;

  const io = new IntersectionObserver(async entries => {
    if(!entries[0].isIntersecting || busy || !next) return;
    busy = true;
    loading && (loading.hidden = false);

    try{
      const res = await fetch(next, { headers: { 'X-Requested-With':'XMLHttpRequest' }});
      const html = await res.text();
      const doc = new DOMParser().parseFromString(html, 'text/html');

      // ambil item .card dari halaman berikutnya dan append
      doc.querySelectorAll('#masonry .card').forEach(el => masonry.appendChild(el));

      // perbarui URL nextPage
      const nextNode = doc.querySelector('#next-page');
      next = nextNode ? nextNode.dataset.next : null;

      if(!next) io.disconnect();
    }catch(e){
      console.error(e);
      io.disconnect();
    }finally{
      busy = false;
      loading && (loading.hidden = true);
    }
  }, { rootMargin: '800px 0px' });

  io.observe(sentinel);
})();
