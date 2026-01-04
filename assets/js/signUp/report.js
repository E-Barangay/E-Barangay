const fileInput = document.getElementById('fileInput');
const uploadBtn = document.getElementById('uploadBtn');
const cardRow = document.getElementById('cardRow');

const MAX_TOTAL_SIZE = 5 * 1024 * 1024; // 5MB
const images = []; // store uploaded images

uploadBtn.addEventListener('click', () => {
    fileInput.click();
});

// function removeImage(id) {
//     const index = images.findIndex(img => img.id === id);
//     if (index > -1) {
//         URL.revokeObjectURL(images[index].url);
//         images.splice(index, 1);
//         renderPreviews();
//     }
// }

function renderPreviews() {
    cardRow.innerHTML = '';
    images.forEach(img => {
        const col = document.createElement('div');
        col.className = 'col-6 col-md-4 col-lg-3';

        const card = document.createElement('div');
        card.className = 'thumb-card';

        const imageEl = document.createElement('img');
        imageEl.src = img.url;
        imageEl.alt = img.name;

        const btn = document.createElement('button');
        btn.className = 'remove-btn';
        btn.innerHTML = '&times;';
        btn.title = 'Remove';
        btn.onclick = () => removeImage(img.id);

        card.appendChild(imageEl);
        card.appendChild(btn);
        col.appendChild(card);
        cardRow.appendChild(col);
    });
}

fileInput.addEventListener('change', (e) => {
    const files = Array.from(e.target.files).filter(f => f.type.startsWith('image/'));

    let totalSize = images.reduce((acc, img) => acc + img.size, 0);
    let exceeded = false;

    for (const file of files) {
        if (totalSize + file.size > MAX_TOTAL_SIZE) {
            exceeded = true;
            break;
        }
        const id = crypto.randomUUID?.() ?? Date.now().toString(36) + Math.random().toString(36).slice(2);
        const url = URL.createObjectURL(file);
        images.push({ id, name: file.name, url, size: file.size, file }); // ✅ keep file object
        totalSize += file.size;
    }

    if (exceeded) {
        const modal = new bootstrap.Modal(document.getElementById('fileSizeModal'));
        modal.show();
    }

    const dataTransfer = new DataTransfer();
    images.forEach(imgObj => dataTransfer.items.add(imgObj.file));
    fileInput.files = dataTransfer.files;

    renderPreviews();
});

function removeImage(id) {
    const index = images.findIndex(img => img.id === id);
    if (index > -1) {
        URL.revokeObjectURL(images[index].url);
        images.splice(index, 1);
        renderPreviews();

        const dataTransfer = new DataTransfer();
        images.forEach(imgObj => {
            if (imgObj.file) dataTransfer.items.add(imgObj.file);
        });
        fileInput.files = dataTransfer.files;
    }
}



// auto-hide success alert after 3 seconds
setTimeout(() => {
    const alert = document.querySelector('.alert-success');
    if (alert) alert.style.display = 'none';
}, 3000);

// Leaflet map
var map = L.map('map').setView([14.111903674282024, 121.14573570538116], 17);

L.tileLayer('https://api.maptiler.com/maps/streets-v2/{z}/{x}/{y}.png?key=ZnRwy10K33uDAz9hPMkT', {
    attribution: '<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> ' +
        '<a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a>'
}).addTo(map);

var currentMarker = null;

map.on('click', function (e) {
    var lat = e.latlng.lat;
    var lng = e.latlng.lng;

    if (currentMarker) {
        map.removeLayer(currentMarker);
    }

    currentMarker = L.marker([lat, lng]).addTo(map);
    currentMarker.bindPopup("Loading address...").openPopup();

    fetch(`https://api.maptiler.com/geocoding/${lng},${lat}.json?key=ZnRwy10K33uDAz9hPMkT`)
        .then(res => res.json())
        .then(data => {
            const address = data.features?.[0]?.place_name || "Unknown Location";
            currentMarker.bindPopup(address, {
                maxWidth: 180,
                minWidth: 120,
                maxHeight: 120,
                autoPan: true,
                closeButton: true,
                className: 'custom-popup-small'
            }).openPopup();

            setCoordinates(lat, lng);
        })
        .catch(err => {
            console.error("Fetch Error:", err);
            currentMarker.bindPopup("Location lookup failed").openPopup();
        });
});


function setCoordinates(lat, lng) {
    document.getElementById('lat').value = lat;
    document.getElementById('lng').value = lng;
}


(() => {
    'use strict'

    const form = document.querySelector('.needs-validation')
    const submitButton = document.getElementById('submitBtn');
    const confirmModalEl = document.getElementById('exampleModal')
    const warningModalEl = document.getElementById('warningModal')

    const confirmModal = new bootstrap.Modal(confirmModalEl)
    const warningModal = new bootstrap.Modal(warningModalEl)

    submitButton.addEventListener('click', event => {
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            warningModal.show();
        } else {
            confirmModal.show();
        }
    });


    // Ensure confirmation modal is not triggered until warning modal is fully hidden
    warningModalEl.addEventListener('hidden.bs.modal', () => {
        // Focus the first empty required field (optional)
        const firstInvalid = form.querySelector(':invalid')
        if (firstInvalid) firstInvalid.focus()
    })

    // Optional: normal form submission validation
    form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
            form.classList.add('was-validated')
        }
    })
})()




