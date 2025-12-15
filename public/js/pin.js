// Opsional: efek fade-in gambar
document.querySelectorAll('.card-img').forEach(img => {
  img.style.opacity = 0;
  img.addEventListener('load', () => {
    img.style.transition = 'opacity 0.4s ease';
    img.style.opacity = 1;
  });
});