document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('search-category');
    if (!input) return;

    const tbody = document.querySelector('.cat-table tbody');
    if (!tbody) return;

    // Ambil hanya baris data yang punya .cat-name
    const dataRows = Array.from(tbody.querySelectorAll('tr')).filter(tr =>
        tr.querySelector('.cat-name')
    );

    if (!dataRows.length) {
        // Tidak ada kategori sama sekali, tidak perlu aktifkan search
        return;
    }

    // Buat row "tidak ada hasil" secara dinamis
    let emptyRow = document.createElement('tr');
    emptyRow.dataset.empty = 'true';

    const emptyTd = document.createElement('td');
    emptyTd.colSpan = 3;
    emptyTd.textContent = 'Tidak ada kategori yang cocok.';
    emptyTd.style.textAlign = 'center';
    emptyTd.style.padding = '20px';
    emptyTd.style.color = 'var(--text-sub)';

    emptyRow.appendChild(emptyTd);
    emptyRow.style.display = 'none';
    tbody.appendChild(emptyRow);

    input.addEventListener('input', function () {
        const q = input.value.trim().toLowerCase();
        let visibleCount = 0;

        dataRows.forEach(tr => {
            const nameCell = tr.querySelector('.cat-name');
            const nameText = nameCell ? nameCell.textContent.toLowerCase() : '';

            if (!q || nameText.includes(q)) {
                tr.style.display = '';
                visibleCount++;
            } else {
                tr.style.display = 'none';
            }
        });

        // Tampilkan row "tidak ada hasil" kalau tidak ada baris yang match
        emptyRow.style.display = visibleCount === 0 ? '' : 'none';
    });
});
