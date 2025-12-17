const profileInput = document.getElementById('profileInput');
const coverInput = document.getElementById('coverInput');
const profilePreview = document.getElementById('profilePreview');
const coverPreview = document.getElementById('coverPreview');
const hiddenProfile = document.getElementById('croppedFotoProfil');
const hiddenCover = document.getElementById('croppedFotoCover');
const cropperContainer = document.getElementById('cropperContainer');
const cropperImage = document.getElementById('cropperImage');
const cropperTitle = document.getElementById('cropperTitle');
const cropperCancelBtn = document.getElementById('cropperCancelBtn');
const cropperSaveBtn = document.getElementById('cropperSaveBtn');
const circleMask = document.getElementById('circleMask');

let cropper = null;
let currentType = null;

function openCropper(file, type) {
    currentType = type;
    const reader = new FileReader();

    reader.onload = (e) => {
        cropperImage.src = e.target.result;

        cropperImage.onload = () => {
            if (cropper) cropper.destroy();

            cropperContainer.classList.remove('hidden');
            cropperTitle.textContent = type === 'profile'
                ? 'Sesuaikan Foto Profil'
                : 'Sesuaikan Foto Cover (Rasio 4:1)';

            cropper = new Cropper(cropperImage, {
                aspectRatio: type === 'profile' ? 1 : 4,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 1,
                background: false,
                movable: true,
                zoomable: true,
                responsive: true,
                ready() {
                    if (type === 'profile') {
                        circleMask.style.display = 'block';
                    } else {
                        circleMask.style.display = 'none';
                    }
                },
                crop() {
                    if (type === 'profile') updateCircleMask();
                }
            });
        };
    };

    reader.readAsDataURL(file);
}

function updateCircleMask() {
    if (!cropper) return;
    const box = cropper.getCropBoxData();
    circleMask.style.width = box.width + 'px';
    circleMask.style.height = box.height + 'px';
    circleMask.style.left = box.left + 'px';
    circleMask.style.top = box.top + 'px';
}

function closeCropper() {
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
    cropperContainer.classList.add('hidden');
    cropperImage.src = '';
    circleMask.style.display = 'none';
    currentType = null;
}

cropperCancelBtn.onclick = closeCropper;

cropperSaveBtn.onclick = () => {
    if (!cropper || !currentType) return;

    const options = currentType === 'profile'
        ? { width: 400, height: 400 }
        : { width: 1600, height: 400 };

    const canvas = cropper.getCroppedCanvas(options);
    if (!canvas) return;

    const dataUrl = canvas.toDataURL('image/jpeg', 0.9);

    if (currentType === 'profile') {
        hiddenProfile.value = dataUrl;
        profilePreview.style.backgroundImage = `url('${dataUrl}')`;
        profilePreview.textContent = "";
    } else {
        hiddenCover.value = dataUrl;
        coverPreview.style.backgroundImage = `url('${dataUrl}')`;
    }

    closeCropper();
};

profileInput.onchange = (e) => {
    const file = e.target.files[0];
    if (file) openCropper(file, 'profile');
};

coverInput.onchange = (e) => {
    const file = e.target.files[0];
    if (file) openCropper(file, 'cover');
};

cropperContainer.addEventListener('click', (e) => {
    if (e.target === cropperContainer) closeCropper();
});