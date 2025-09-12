
const fileInput = document.getElementById('fileInput');
const uploadBtn = document.getElementById('uploadBtn');
const cardRow = document.getElementById('cardRow');

const images = []; // store uploaded images

uploadBtn.addEventListener('click', () => {
    fileInput.click();
});

function removeImage(id) {
    const index = images.findIndex(img => img.id === id);
    if (index > -1) {
        // revoke object URL
        URL.revokeObjectURL(images[index].url);
        images.splice(index, 1);
        renderPreviews();
    }
}

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
    files.forEach(file => {
        const id = crypto.randomUUID?.() ?? Date.now().toString(36) + Math.random().toString(36).slice(2);
        const url = URL.createObjectURL(file);
        images.push({ id, name: file.name, url });
    });
    renderPreviews();
});


setTimeout(() => {
    let alert = document.querySelector('.alert-success');
    if (alert) {
        alert.style.display = 'none';
    }
}, 3000); // 3 seconds