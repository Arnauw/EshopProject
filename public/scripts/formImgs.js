console.log('formImgs.js loaded');

function renderImagesBeforeUpload() {
    // Dynamically get the exact ID of the file input from Twig.
    const imageInputId = '{{ form.image.vars.id }}';
    const imageInput = document.getElementById(imageInputId);

    // If for some reason the input doesn't exist, stop right here.
    if (!imageInput) { return; }

    // Get the preview elements using their dynamic IDs.
    const previewContainer = document.getElementById('{{ form.vars.id }}_preview_container');
    const previewImage = document.getElementById('{{ form.vars.id }}_preview_image');

    // Attach the event listener.
    imageInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
}
renderImagesBeforeUpload();
