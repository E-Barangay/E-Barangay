var editButton = document.getElementById('editButton');
var cancelButton = document.getElementById('cancelButton');
var saveButton = document.getElementById('saveButton');
var inputs = document.querySelectorAll('.form-control');
var addButton = document.getElementById('addButton');
var deleteButton = document.getElementById('deleteButton');
var fileInput = document.getElementById('profilePictureInput');
var preview = document.getElementById('profilePreview');

var isEdit = false;
var originalPreviewSrc = preview ? preview.src : null;

editButton.addEventListener('click', function () {
    isEdit = true;
    inputs.forEach(function (input) {
        input.removeAttribute('disabled');
    });

    editButton.classList.add('d-none');
    cancelButton.classList.remove('d-none');
    saveButton.classList.remove('d-none');

    if (addButton) addButton.classList.remove('d-none');
    if (deleteButton) deleteButton.classList.remove('d-none');
});

cancelButton.addEventListener('click', function () {
    isEdit = false;
    inputs.forEach(function (input) {
        input.setAttribute('disabled', true);
    });

    editButton.classList.remove('d-none');
    cancelButton.classList.add('d-none');
    saveButton.classList.add('d-none');
    
    if (addButton) addButton.classList.add('d-none');
    if (deleteButton) deleteButton.classList.add('d-none');

    if (preview && originalPreviewSrc) {
        preview.src = originalPreviewSrc;
    }

    if (fileInput) {
        fileInput.value = "";
    }
});

if (fileInput && preview) {
    fileInput.addEventListener('change', function (e) {
        const reader = new FileReader();
        reader.onload = function (event) {
            preview.src = event.target.result;
        };
        if (e.target.files[0]) {
            reader.readAsDataURL(e.target.files[0]);
        }
    });
}