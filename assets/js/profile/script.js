var editButton = document.getElementById('editButton');
var cancelButton = document.getElementById('cancelButton');
var saveButton = document.getElementById('saveButton');
var inputs = document.querySelectorAll('.form-control, .form-select, textarea, .form-check-input');
var addButton = document.getElementById('addButton');
var deleteButton = document.getElementById('deleteButton');
var fileInput = document.getElementById('profilePictureInput');
var preview = document.getElementById('profilePreview');
var sameAsCurrent = document.getElementById('sameAsCurrent');

var isEdit = false;
var originalPreviewSrc = preview ? preview.src : null;
var originalValues = [];
var originalSameAsCurrentChecked = sameAsCurrent.checked;

editButton.addEventListener('click', function () {
    isEdit = true;
    originalValues = [];
    originalSameAsCurrentChecked = sameAsCurrent.checked;
    inputs.forEach(function (input) {
        originalValues.push(input.value);
        input.removeAttribute('disabled');
    });
    sameAsCurrent.disabled = false;
    editButton.classList.add('d-none');
    cancelButton.classList.remove('d-none');
    saveButton.classList.remove('d-none');

    if (addButton) addButton.classList.remove('d-none');
    if (deleteButton) deleteButton.classList.remove('d-none');
});

cancelButton.addEventListener('click', function () {
    isEdit = false;
    inputs.forEach(function (input, index) {
        input.value = originalValues[index];
        input.setAttribute('disabled', true);
    });
    sameAsCurrent.disabled = true;
    sameAsCurrent.checked = originalSameAsCurrentChecked;
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

saveButton.addEventListener('click', function () {
    sameAsCurrent.disabled = true;
    localStorage.setItem('sameAsCurrentChecked', sameAsCurrent.checked);
    editButton.classList.remove('d-none');
    cancelButton.classList.add('d-none');
    saveButton.classList.add('d-none');
});

if (fileInput && preview) {
    fileInput.addEventListener('change', function (e) {
        const reader = new FileReader();
        reader.onload = function (event) {
            preview.src = event.target.result;
        };
        if (e.target.files && e.target.files[0]) {
            reader.readAsDataURL(e.target.files[0]);
        }
    });
}

function updateResidencyType() {
    var age = parseInt(document.getElementById("age").value) || 0;
    var lengthOfStay = parseInt(document.getElementById("lengthOfStay").value) || 0;

    var address = {
        blockLotNo: document.getElementById("blockLotNo").value,
        phase: document.getElementById("phase").value,
        subdivision: document.getElementById("subdivision").value,
        purok: document.getElementById("purok").value,
        street: document.getElementById("street").value,
        barangay: document.getElementById("barangay").value,
        city: document.getElementById("city").value,
        province: document.getElementById("province").value
    };

    var permanentAddress = {
        blockLotNo: document.getElementById("permanentBlockLotNo").value,
        phase: document.getElementById("permanentPhase").value,
        subdivision: document.getElementById("permanentSubdivisionName").value,
        purok: document.getElementById("permanentPurok").value,
        street: document.getElementById("permanentStreet").value,
        barangay: document.getElementById("permanentBarangay").value,
        city: document.getElementById("permanentCity").value,
        province: document.getElementById("permanentProvince").value
    };

    let residencyType = "";
    
    var isSameAddress = Object.keys(address).every(key => address[key] === permanentAddress[key]);

    if (age === 0 || lengthOfStay === 0) {
        residencyType = "";
    } else if (isSameAddress && lengthOfStay >= age) {
        residencyType = "Bonafide";
    } else if (isSameAddress && lengthOfStay !== age) {
        residencyType = "Migrant";
    } else if (!isSameAddress && lengthOfStay !== age) {
        residencyType = "Transient";
    } else {
        residencyType = "Foreign";
    }

    document.getElementById("residencyType").value = residencyType;
    document.getElementById("residencyTypeHidden").value = residencyType;
}

updateResidencyType();

[
    "age", "lengthOfStay",
    "blockLotNo", "phase", "subdivision", "purok", "street",
    "barangay", "city", "province",
    "permanentBlockLotNo", "permanentPhase", "permanentSubdivisionName", "permanentPurok", "permanentStreet",
    "permanentBarangay", "permanentCity", "permanentProvince"
].forEach(id => {
    document.getElementById(id).addEventListener("input", updateResidencyType);
});

sameAsCurrent.addEventListener('change', function () {
    var currentFields = {
        blockLotNo: document.getElementById("blockLotNo").value,
        phase: document.getElementById("phase").value,
        subdivision: document.getElementById("subdivision").value,
        purok: document.getElementById("purok").value,
        street: document.getElementById("street").value,
        barangay: document.getElementById("barangay").value,
        city: document.getElementById("city").value,
        province: document.getElementById("province").value
    };

    var permanentFields = {
        blockLotNo: document.getElementById("permanentBlockLotNo"),
        phase: document.getElementById("permanentPhase"),
        subdivision: document.getElementById("permanentSubdivisionName"),
        purok: document.getElementById("permanentPurok"),
        street: document.getElementById("permanentStreet"),
        barangay: document.getElementById("permanentBarangay"),
        city: document.getElementById("permanentCity"),
        province: document.getElementById("permanentProvince")
    };

    Object.values(permanentFields).forEach(f => f.removeAttribute('disabled'));

    if (this.checked) {
        for (var key in permanentFields) {
            permanentFields[key].value = currentFields[key];
        }
    } else {
        for (var key in permanentFields) {
            permanentFields[key].value = '';
        }
    }

    if (!isEdit) {
        Object.values(permanentFields).forEach(f => f.setAttribute('disabled', true));
    }
});

let data = {};
let selectedProvince = '';
let selectedMunicipality = '';
let selectedPermanentProvince = '';
let selectedPermanentMunicipality = '';

fetch("assets/json/philippine_provinces_cities_municipalities_and_barangays_2019v2.json")
    .then(response => response.json())
    .then(jsonData => {
        data = jsonData;

        const provinceDatalist = document.getElementById('provincesList');
        const permanentProvinceDatalist = document.getElementById('permanentProvincesList');

        const provinceNames = [];
        for (const regionCode in data) {
            const provinces = data[regionCode].province_list;
            for (const provinceName in provinces) {
                provinceNames.push(provinceName);
            }
        }

        provinceNames.sort().forEach(provinceName => {
            let opt1 = document.createElement('option');
            opt1.value = provinceName;
            provinceDatalist.appendChild(opt1);

            let opt2 = document.createElement('option');
            opt2.value = provinceName;
            permanentProvinceDatalist.appendChild(opt2);
        });
    });

document.getElementById('province').addEventListener('input', function () {
    selectedProvince = this.value;
    const cityDatalist = document.getElementById('citiesList');
    cityDatalist.innerHTML = '';

    for (const regionCode in data) {
        const provinces = data[regionCode].province_list;
        if (provinces[selectedProvince]) {
            const municipalities = provinces[selectedProvince].municipality_list;
            Object.keys(municipalities).sort().forEach(city => {
                let opt = document.createElement('option');
                opt.value = city;
                cityDatalist.appendChild(opt);
            });
            break;
        }
    }
});

document.getElementById('city').addEventListener('input', function () {
    selectedMunicipality = this.value;
    const barangayDatalist = document.getElementById('barangaysList');
    barangayDatalist.innerHTML = '';

    for (const regionCode in data) {
        const provinces = data[regionCode].province_list;
        if (provinces[selectedProvince]) {
            const municipalities = provinces[selectedProvince].municipality_list;
            if (municipalities[selectedMunicipality]) {
                municipalities[selectedMunicipality].barangay_list.sort().forEach(barangay => {
                    let opt = document.createElement('option');
                    opt.value = barangay;
                    barangayDatalist.appendChild(opt);
                });
            }
            break;
        }
    }
});

document.getElementById('permanentProvince').addEventListener('input', function () {
    selectedPermanentProvince = this.value;
    const cityDatalist = document.getElementById('permanentCitiesList');
    cityDatalist.innerHTML = '';

    for (const regionCode in data) {
        const provinces = data[regionCode].province_list;
        if (provinces[selectedPermanentProvince]) {
            const municipalities = provinces[selectedPermanentProvince].municipality_list;
            Object.keys(municipalities).sort().forEach(city => {
                let opt = document.createElement('option');
                opt.value = city;
                cityDatalist.appendChild(opt);
            });
            break;
        }
    }
});

document.getElementById('permanentCity').addEventListener('input', function () {
    selectedPermanentMunicipality = this.value;
    const barangayDatalist = document.getElementById('permanentBarangaysList');
    barangayDatalist.innerHTML = '';

    for (const regionCode in data) {
        const provinces = data[regionCode].province_list;
        if (provinces[selectedPermanentProvince]) {
            const municipalities = provinces[selectedPermanentProvince].municipality_list;
            if (municipalities[selectedPermanentMunicipality]) {
                municipalities[selectedPermanentMunicipality].barangay_list.sort().forEach(barangay => {
                    let opt = document.createElement('option');
                    opt.value = barangay;
                    barangayDatalist.appendChild(opt);
                });
            }
            break;
        }
    }
});