const fileInput = document.getElementById('file');
const previewImg = document.getElementById('preview-image');
const placeholder = document.getElementById('preview-placeholder');
const container = document.getElementById('preview-container');
const fileNameSpan = document.getElementById('file-name');

fileInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (!file) return;

    // tampilkan nama file
    fileNameSpan.textContent = file.name;

    const reader = new FileReader();
    reader.onload = (ev) => {
        previewImg.src = ev.target.result;
        previewImg.style.display = 'block';
        placeholder.style.display = 'none';

        previewImg.onload = function () {
            const ratio = this.naturalHeight / this.naturalWidth;
            const maxWidth = container.offsetWidth;
            const calcHeight = maxWidth * ratio;

            container.style.height = `${Math.min(calcHeight, 450)}px`;
            previewImg.style.maxHeight = '100%';
        };
    };
    reader.readAsDataURL(file);
});