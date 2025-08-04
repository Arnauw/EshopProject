console.log('formImgs.js loaded');

function renderImagesBeforeUpload() {
    const imageInput = document.getElementById('product_image');

    if (!imageInput) { return; }

    const inputWrapper = imageInput.closest('.mb-3');
    if (!inputWrapper) { return; }

    imageInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();

            reader.onload = function(e) {

                let previewImage = document.getElementById('product_preview_image');

                if (!previewImage) {
                    console.log('No preview found. Creating a new one.');

                    const newPreviewContainer = document.createElement('div');
                    newPreviewContainer.id = 'product_preview_container';
                    newPreviewContainer.className = 'mt-2 d-flex justify-content-center';

                    const newPreviewImage = document.createElement('img');
                    newPreviewImage.id = 'product_preview_image';
                    newPreviewImage.className = 'img-fluid rounded border';
                    newPreviewImage.alt = 'Image preview';

                    newPreviewContainer.appendChild(newPreviewImage);
                    inputWrapper.appendChild(newPreviewContainer);

                    previewImage = newPreviewImage;
                }

                previewImage.src = e.target.result;
                previewImage.parentElement.style.display = 'flex';
            };

            reader.readAsDataURL(file);
        }
    });
}

renderImagesBeforeUpload();
