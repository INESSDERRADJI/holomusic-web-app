document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('profile-form');
    const saveBtn = document.getElementById('save-btn');

    const selectedPhoto = document.getElementById('selected-photo');
    const selectablePhotos = document.querySelectorAll('.selectable-photo');
    const uploadInput = document.getElementById('upload-photo');
    const hiddenImageInput = document.getElementById('selected-image');

    const initialValues = {};
    Array.from(form.elements).forEach(el => {
        if (el.name) initialValues[el.name] = el.value;
    });
    const initialImage = hiddenImageInput.value;

    function checkChanges() {
        let changed = false;

        Array.from(form.elements).forEach(el => {
            if (el.name && el.value !== initialValues[el.name]) changed = true;
        });

        const currentImage = hiddenImageInput.value || uploadInput.files[0]?.name || '';
        if (currentImage !== initialImage) changed = true;

        saveBtn.style.display = changed ? 'inline-block' : 'none';
    }

    form.addEventListener('input', checkChanges);

    selectablePhotos.forEach(img => {
        img.addEventListener('click', () => {
            selectedPhoto.src = img.src;
            hiddenImageInput.value = img.src.split('/').pop();
            uploadInput.value = '';
            checkChanges();
        });
    });

    uploadInput.addEventListener('change', () => {
        if (uploadInput.files.length > 0) {
            selectedPhoto.src = URL.createObjectURL(uploadInput.files[0]);
            hiddenImageInput.value = '';
            checkChanges();
        }
    });
});
