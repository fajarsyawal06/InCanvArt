// ====== DELETE ARTWORK (SATU TOMBOL) ======
const deleteArtworkBtn = document.getElementById('btn-deleteArtwork');

if (deleteArtworkBtn) {
    deleteArtworkBtn.addEventListener('click', function () {
        Swal.fire({
            title: 'Hapus Artwork?',
            text: 'Tindakan ini tidak dapat dibatalkan. Artwork akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('delete-artwork-form');
                if (form) form.submit();
            }
        });
    });
}

// ====== DELETE CATEGORY (BANYAK TOMBOL DI TABEL) ======
document.querySelectorAll('.btn-delete').forEach(button => {
    button.addEventListener('click', function () {
        const formId = this.getAttribute('data-form');

        Swal.fire({
            title: 'Hapus Kategori?',
            text: 'Tindakan ini tidak dapat dibatalkan. Kategori akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed && formId) {
                const form = document.getElementById(formId);
                if (form) form.submit();
            }
        });
    });
});
