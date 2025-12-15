document.addEventListener('DOMContentLoaded', function () {
    const reportBtn = document.getElementById('btn-report-artwork');
    const reportForm = document.getElementById('report-artwork-form');

    if (!reportBtn || !reportForm) return;

    reportBtn.addEventListener('click', function (e) {
        e.preventDefault();

        Swal.fire({
            title: 'Tandai artwork ini?',
            text: 'Laporan akan dikirim ke Halaman Moderasi.',
            input: 'textarea',
            inputLabel: 'Alasan Moderasi (opsional)',
            inputPlaceholder: 'Tuliskan alasan Moderasi di sini...',
            inputAttributes: {
                'aria-label': 'Alasan Moderasi'
            },
            showCancelButton: true,
            confirmButtonText: 'Kirim Moderasi',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'swal2-dark-rounded'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Isi hidden input "alasan" dengan nilai dari textarea
                const alasanField = reportForm.querySelector('input[name="alasan"]');
                if (alasanField) {
                    alasanField.value = result.value || '';
                }
                reportForm.submit();
            }
        });
    });
});
