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
var originalProvinceValue = '';
var originalCityValue = '';
var originalBarangayValue = '';
var originalPermanentProvinceValue = '';
var originalPermanentCityValue = '';
var originalPermanentBarangayValue = '';

editButton.addEventListener('click', function () {
    isEdit = true;
    originalValues = [];
    originalSameAsCurrentChecked = sameAsCurrent.checked;

    // Save original address values
    originalProvinceValue = document.getElementById('province').value;
    originalCityValue = document.getElementById('city').value;
    originalBarangayValue = document.getElementById('barangay').value;
    originalPermanentProvinceValue = document.getElementById('permanentProvince').value;
    originalPermanentCityValue = document.getElementById('permanentCity').value;
    originalPermanentBarangayValue = document.getElementById('permanentBarangay').value;

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

    // Restore original address dropdowns
    const provinceSelect = document.getElementById('province');
    const citySelect = document.getElementById('city');
    const barangaySelect = document.getElementById('barangay');
    const permanentProvinceSelect = document.getElementById('permanentProvince');
    const permanentCitySelect = document.getElementById('permanentCity');
    const permanentBarangaySelect = document.getElementById('permanentBarangay');

    // Restore current address
    if (originalProvinceValue) {
        provinceSelect.value = originalProvinceValue;
        // Trigger the population functions to restore cities and barangays
        if (window.populateCitiesByProvinceKey) {
            window.populateCitiesByProvinceKey(originalProvinceValue, originalCityValue);
            if (originalCityValue) {
                citySelect.value = originalCityValue;
                if (window.populateBarangaysByKeys) {
                    window.populateBarangaysByKeys(originalProvinceValue, originalCityValue, originalBarangayValue);
                    if (originalBarangayValue) {
                        barangaySelect.value = originalBarangayValue;
                    }
                }
            }
        }
    }

    // Restore permanent address
    if (originalPermanentProvinceValue) {
        permanentProvinceSelect.value = originalPermanentProvinceValue;
        if (window.populatePermanentCitiesByProvinceKey) {
            window.populatePermanentCitiesByProvinceKey(originalPermanentProvinceValue, originalPermanentCityValue);
            if (originalPermanentCityValue) {
                permanentCitySelect.value = originalPermanentCityValue;
                if (window.populatePermanentBarangaysByKeys) {
                    window.populatePermanentBarangaysByKeys(originalPermanentProvinceValue, originalPermanentCityValue, originalPermanentBarangayValue);
                    if (originalPermanentBarangayValue) {
                        permanentBarangaySelect.value = originalPermanentBarangayValue;
                    }
                }
            }
        }
    }

    // Restore occupation fields visibility based on original values
    updateOccupationFields();

    // Remove any validation alerts
    const existingAlert = document.querySelector('.validation-alert');
    if (existingAlert) {
        existingAlert.remove();
    }
});

saveButton.addEventListener('click', function (e) {
    // Validate current address
    const province = document.getElementById('province').value;
    const city = document.getElementById('city').value;
    const barangay = document.getElementById('barangay').value;

    // Validate permanent address
    const permanentProvince = document.getElementById('permanentProvince').value;
    const permanentCity = document.getElementById('permanentCity').value;
    const permanentBarangay = document.getElementById('permanentBarangay').value;

    let hasError = false;
    let errorMessages = [];

    // // Check current address
    // if (!province || !city || !barangay) {
    //     errorMessages.push('Please fill in all address fields (Province, City, Barangay).');
    //     hasError = true;
    // }

    // // Check permanent address
    // if (!permanentProvince || !permanentCity || !permanentBarangay) {
    //     errorMessages.push('Please fill in all permanent address fields (Province, City, Barangay).');
    //     hasError = true;
    // }

    if (hasError) {
        e.preventDefault();

        // Remove any existing alerts
        const existingAlert = document.querySelector('.validation-alert');
        if (existingAlert) {
            existingAlert.remove();
        }

        // Create Bootstrap alert
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show validation-alert';
        alertDiv.style.marginBottom = '1rem';
        alertDiv.innerHTML = `
            ${errorMessages.map(msg => `<div>${msg}</div>`).join('')}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

        // Insert alert at the top of the container
        const container = document.querySelector('.container.pt-3 .row .col');
        container.insertBefore(alertDiv, container.firstChild);

        // Scroll to top to show the alert
        window.scrollTo({ top: 0, behavior: 'smooth' });

        return false;
    }

    // Remove validation alert if exists (validation passed)
    const existingAlert = document.querySelector('.validation-alert');
    if (existingAlert) {
        existingAlert.remove();
    }

    sameAsCurrent.disabled = true;
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

[
    "age", "lengthOfStay",
    "blockLotNo", "phase", "subdivision", "purok", "street",
    "barangay", "city", "province",
    "permanentBlockLotNo", "permanentPhase", "permanentSubdivisionName", "permanentPurok", "permanentStreet",
    "permanentBarangay", "permanentCity", "permanentProvince"
].forEach(id => {
    var elem = document.getElementById(id);
    if (elem) {
        elem.addEventListener("input", updateResidencyType);
        elem.addEventListener("change", updateResidencyType);
    }
});

// ========== CURRENT ADDRESS (SELECT DROPDOWNS) ==========
const provinceSelect = document.getElementById("province");
const citySelect = document.getElementById("city");
const barangaySelect = document.getElementById("barangay");

// Saved values from PHP - get them from data attributes
const savedProvince = provinceSelect.getAttribute("data-saved");
const savedCity = citySelect.getAttribute("data-saved");
const savedBarangay = barangaySelect.getAttribute("data-saved");

// ========== PERMANENT ADDRESS (SELECT DROPDOWNS) ==========
const permanentProvinceSelect = document.getElementById("permanentProvince");
const permanentCitySelect = document.getElementById("permanentCity");
const permanentBarangaySelect = document.getElementById("permanentBarangay");

// Saved values from PHP for permanent address
const savedPermanentProvince = permanentProvinceSelect.getAttribute("data-saved");
const savedPermanentCity = permanentCitySelect.getAttribute("data-saved");
const savedPermanentBarangay = permanentBarangaySelect.getAttribute("data-saved");

let jsonData = null;
let isJsonLoaded = false;

// Load JSON once
fetch("assets/json/philippine_provinces_cities_municipalities_and_barangays_2019v2.json")
    .then(response => response.json())
    .then(data => {
        jsonData = data;
        isJsonLoaded = true;

        // helper: normalize text for comparisons
        function normalize(s) {
            if (!s) return '';
            return s.toString()
                .trim()
                .toLowerCase()
                .replace(/\b(city|municipality|municipal|province|of|the)\b/g, '')
                .replace(/[^a-z0-9\s]/g, '')
                .replace(/\s+/g, ' ')
                .trim();
        }

        // find a province key in jsonData that best matches savedProvince (or given name)
        function findProvinceKeyByName(name) {
            const target = normalize(name);
            if (!target) return null;
            for (const regionCode of Object.keys(jsonData)) {
                const provinces = jsonData[regionCode].province_list;
                for (const provKey of Object.keys(provinces)) {
                    if (normalize(provKey) === target) return provKey;
                }
            }
            for (const regionCode of Object.keys(jsonData)) {
                const provinces = jsonData[regionCode].province_list;
                for (const provKey of Object.keys(provinces)) {
                    if (normalize(provKey).includes(target) || target.includes(normalize(provKey))) return provKey;
                }
            }
            return null;
        }

        // find a city key inside a province's municipality_list that best matches savedCity
        function findCityKeyByName(provinceKey, cityName) {
            if (!provinceKey) return null;
            const provinceObj = (() => {
                for (const rc of Object.keys(jsonData)) {
                    const pList = jsonData[rc].province_list;
                    if (pList[provinceKey]) return pList[provinceKey];
                }
                return null;
            })();
            if (!provinceObj) return null;
            const target = normalize(cityName);
            const cityList = provinceObj.municipality_list || {};
            for (const cityKey of Object.keys(cityList)) {
                if (normalize(cityKey) === target) return cityKey;
            }
            for (const cityKey of Object.keys(cityList)) {
                if (normalize(cityKey).includes(target) || target.includes(normalize(cityKey))) return cityKey;
            }
            return null;
        }

        // find barangay name (value) inside cityList that best matches savedBarangay
        function findBarangayByName(provinceKey, cityKey, barangayName) {
            if (!provinceKey || !cityKey) return null;
            const provinceObj = (() => {
                for (const rc of Object.keys(jsonData)) {
                    const pList = jsonData[rc].province_list;
                    if (pList[provinceKey]) return pList[provinceKey];
                }
                return null;
            })();
            if (!provinceObj) return null;
            const cityList = provinceObj.municipality_list || {};
            const cityObj = cityList[cityKey];
            if (!cityObj) return null;
            const barangays = cityObj.barangay_list || [];
            const target = normalize(barangayName);
            for (const b of barangays) {
                if (normalize(b) === target) return b;
            }
            for (const b of barangays) {
                if (normalize(b).includes(target) || target.includes(normalize(b))) return b;
            }
            return null;
        }

        // ========== POPULATE CURRENT ADDRESS (SELECT DROPDOWNS) ==========

        // Populate provinces (sorted alphabetically)
        provinceSelect.innerHTML = '<option value="">Select Province</option>';
        let savedProvinceKey = findProvinceKeyByName(savedProvince);

        // Collect all provinces from all regions
        const allProvinces = [];
        Object.keys(data).forEach(regionCode => {
            const provinces = data[regionCode].province_list;
            Object.keys(provinces).forEach(provinceKey => {
                allProvinces.push(provinceKey);
            });
        });

        // Sort provinces alphabetically
        allProvinces.sort((a, b) => a.localeCompare(b));

        // Add sorted provinces to select
        allProvinces.forEach(provinceKey => {
            const opt = document.createElement("option");
            opt.value = provinceKey;
            opt.textContent = provinceKey;
            if (savedProvinceKey && provinceKey === savedProvinceKey) opt.selected = true;
            provinceSelect.appendChild(opt);
        });

        // Helper to populate cities (sorted alphabetically)
        function populateCitiesByProvinceKey(provinceKey, tryToSelectCityName) {
            // reset UI
            citySelect.innerHTML = '<option value="">Select City</option>';
            barangaySelect.innerHTML = '<option value="">Select Barangay</option>';

            // defensive: if no province, ensure placeholders stay
            if (!provinceKey || provinceKey.trim() === "") {
                citySelect.selectedIndex = 0;
                barangaySelect.selectedIndex = 0;
                return;
            }

            // find municipality list
            let municipality_list = null;
            for (const rc of Object.keys(jsonData)) {
                const pList = jsonData[rc].province_list;
                if (pList[provinceKey]) {
                    municipality_list = pList[provinceKey].municipality_list;
                    break;
                }
            }
            if (!municipality_list) {
                citySelect.selectedIndex = 0;
                return;
            }

            // populate cities (sorted)
            const sortedCities = Object.keys(municipality_list).sort((a, b) => a.localeCompare(b));
            sortedCities.forEach(cityKey => {
                const opt = document.createElement("option");
                opt.value = cityKey;
                opt.textContent = cityKey;
                citySelect.appendChild(opt);
            });

            // If caller provided a non-empty city name, try to select it.
            if (tryToSelectCityName && tryToSelectCityName.toString().trim() !== "") {
                // try matching (your findCityKeyByName will handle normalization)
                const matched = findCityKeyByName(provinceKey, tryToSelectCityName);
                if (matched) {
                    citySelect.value = matched;
                    // populate barangays for that selected city (only if matched)
                    populateBarangaysByKeys(provinceKey, matched, '');
                } else {
                    // provided saved name didn't match anything: keep placeholder
                    citySelect.selectedIndex = 0;
                }
            } else {
                // no saved city - ensure placeholder remains visible (and do NOT auto-populate barangays)
                citySelect.selectedIndex = 0;
            }
        }
        window.populateCitiesByProvinceKey = populateCitiesByProvinceKey;


        // Make function globally accessible for cancel button
        window.populateCitiesByProvinceKey = populateCitiesByProvinceKey;

        // Helper to populate barangays (sorted alphabetically)
        function populateBarangaysByKeys(provinceKey, cityKey, tryToSelectBarangayName) {
            // debug log to see calls
            console.debug("populateBarangaysByKeys called with:", { provinceKey, cityKey, tryToSelectBarangayName });

            // set placeholder first
            barangaySelect.innerHTML = '<option value="">Select Barangay</option>';

            // guard: if city not provided, force placeholder and stop
            if (!provinceKey || !cityKey || cityKey.toString().trim() === "") {
                // use setTimeout to ensure this runs after any other microtasks that might change selects
                setTimeout(() => {
                    barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
                    barangaySelect.selectedIndex = 0;
                }, 0);
                return;
            }

            // find barangays array
            let barangays = null;
            for (const rc of Object.keys(jsonData)) {
                const pList = jsonData[rc].province_list;
                if (pList[provinceKey]) {
                    const cList = pList[provinceKey].municipality_list;
                    if (cList && cList[cityKey]) {
                        barangays = cList[cityKey].barangay_list;
                    }
                    break;
                }
            }
            if (!barangays || barangays.length === 0) {
                // ensure placeholder
                barangaySelect.selectedIndex = 0;
                return;
            }

            // populate sorted barangays
            const sortedBarangays = [...barangays].sort((a, b) => a.localeCompare(b));
            sortedBarangays.forEach(b => {
                const opt = document.createElement("option");
                opt.value = b;
                opt.textContent = b;
                barangaySelect.appendChild(opt);
            });

            // only select a barangay if caller provided a non-empty saved barangay name and it matches
            if (tryToSelectBarangayName && tryToSelectBarangayName.toString().trim() !== "") {
                const matched = findBarangayByName(provinceKey, cityKey, tryToSelectBarangayName);
                if (matched) {
                    barangaySelect.value = matched;
                } else {
                    // saved name didn't match, keep placeholder
                    barangaySelect.selectedIndex = 0;
                }
            } else {
                // no saved barangay -> keep placeholder
                barangaySelect.selectedIndex = 0;
            }
        }
        window.populateBarangaysByKeys = populateBarangaysByKeys;

        // Restore saved selections
        // Restore saved selections (current)
        if (savedProvince && savedProvince !== '') {
            if (!savedProvinceKey) savedProvinceKey = findProvinceKeyByName(savedProvince);

            if (savedProvinceKey) {
                provinceSelect.value = savedProvinceKey;

                // only call populateCities if savedCity is non-empty; otherwise leave city placeholder
                if (savedCity && savedCity.trim() !== "") {
                    const cityKey = findCityKeyByName(savedProvinceKey, savedCity);
                    populateCitiesByProvinceKey(savedProvinceKey, savedCity);
                    if (cityKey) {
                        citySelect.value = cityKey;
                        // only populate barangays if saved barangay non-empty
                        if (savedBarangay && savedBarangay.trim() !== "") {
                            populateBarangaysByKeys(savedProvinceKey, cityKey, savedBarangay);
                            const matchedBarangay = findBarangayByName(savedProvinceKey, cityKey, savedBarangay);
                            if (matchedBarangay) barangaySelect.value = matchedBarangay;
                        }
                    }
                } else {
                    // no saved city: ensure placeholders
                    populateCitiesByProvinceKey(savedProvinceKey, '');
                    citySelect.selectedIndex = 0;
                    barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
                }
            }
        }

        // EVENT LISTENERS for current address
        provinceSelect.addEventListener("change", function () {
            const selectedProvinceKey = this.value;
            populateCitiesByProvinceKey(selectedProvinceKey, '');
            barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
            updateResidencyType();
        });

        citySelect.addEventListener("change", function () {
            const selectedProvinceKey = provinceSelect.value;
            const selectedCityKey = this.value;
            populateBarangaysByKeys(selectedProvinceKey, selectedCityKey, '');
            updateResidencyType();
        });

        barangaySelect.addEventListener("change", function () {
            updateResidencyType();
        });

        // ========== POPULATE PERMANENT ADDRESS (SELECT DROPDOWNS) ==========

        // Populate permanent provinces (sorted alphabetically)
        permanentProvinceSelect.innerHTML = '<option value="">Select Province</option>';
        let savedPermanentProvinceKey = findProvinceKeyByName(savedPermanentProvince);

        // Add sorted provinces to permanent select
        allProvinces.forEach(provinceKey => {
            const opt = document.createElement("option");
            opt.value = provinceKey;
            opt.textContent = provinceKey;
            if (savedPermanentProvinceKey && provinceKey === savedPermanentProvinceKey) opt.selected = true;
            permanentProvinceSelect.appendChild(opt);
        });

        // Helper to populate permanent cities (sorted alphabetically)
        // Populate Permanent Cities safely
        function populatePermanentCitiesByProvinceKey(provinceKey, tryToSelectCityName) {
            // Reset placeholders first
            permanentCitySelect.innerHTML = '<option value="">Select City</option>';
            permanentBarangaySelect.innerHTML = '<option value="">Select Barangay</option>';

            // Defensive: if no province selected, keep placeholders
            if (!provinceKey || provinceKey.trim() === "") {
                permanentCitySelect.selectedIndex = 0;
                permanentBarangaySelect.selectedIndex = 0;
                return;
            }

            // Find municipality list
            let municipality_list = null;
            for (const rc of Object.keys(jsonData)) {
                const pList = jsonData[rc].province_list;
                if (pList[provinceKey]) {
                    municipality_list = pList[provinceKey].municipality_list;
                    break;
                }
            }
            if (!municipality_list) {
                permanentCitySelect.selectedIndex = 0;
                return;
            }

            // Sort and populate cities
            const sortedCities = Object.keys(municipality_list).sort((a, b) => a.localeCompare(b));
            sortedCities.forEach(cityKey => {
                const opt = document.createElement("option");
                opt.value = cityKey;
                opt.textContent = cityKey;
                permanentCitySelect.appendChild(opt);
            });

            // Only select a saved city if provided
            if (tryToSelectCityName && tryToSelectCityName.trim() !== "") {
                const matchedCityKey = findCityKeyByName(provinceKey, tryToSelectCityName);
                if (matchedCityKey) {
                    permanentCitySelect.value = matchedCityKey;
                    populatePermanentBarangaysByKeys(provinceKey, matchedCityKey, '');
                } else {
                    permanentCitySelect.selectedIndex = 0;
                }
            } else {
                // Keep placeholders
                permanentCitySelect.selectedIndex = 0;
                permanentBarangaySelect.innerHTML = '<option value="">Select Barangay</option>';
            }
        }
        window.populatePermanentCitiesByProvinceKey = populatePermanentCitiesByProvinceKey;

        // Populate Permanent Barangays safely
        function populatePermanentBarangaysByKeys(provinceKey, cityKey, tryToSelectBarangayName) {
            // Reset barangay dropdown
            permanentBarangaySelect.innerHTML = '<option value="">Select Barangay</option>';

            // Defensive: stop if city not chosen
            if (!provinceKey || !cityKey || cityKey.trim() === "") {
                setTimeout(() => {
                    permanentBarangaySelect.innerHTML = '<option value="">Select Barangay</option>';
                    permanentBarangaySelect.selectedIndex = 0;
                }, 0);
                return;
            }

            // Find barangays
            let barangays = null;
            for (const rc of Object.keys(jsonData)) {
                const pList = jsonData[rc].province_list;
                if (pList[provinceKey]) {
                    const cList = pList[provinceKey].municipality_list;
                    if (cList && cList[cityKey]) {
                        barangays = cList[cityKey].barangay_list;
                    }
                    break;
                }
            }
            if (!barangays || barangays.length === 0) {
                permanentBarangaySelect.selectedIndex = 0;
                return;
            }

            // Sort and populate barangays
            const sortedBarangays = [...barangays].sort((a, b) => a.localeCompare(b));
            sortedBarangays.forEach(b => {
                const opt = document.createElement("option");
                opt.value = b;
                opt.textContent = b;
                permanentBarangaySelect.appendChild(opt);
            });

            // Only select saved barangay if provided
            if (tryToSelectBarangayName && tryToSelectBarangayName.trim() !== "") {
                const matchedBarangay = findBarangayByName(provinceKey, cityKey, tryToSelectBarangayName);
                if (matchedBarangay) {
                    permanentBarangaySelect.value = matchedBarangay;
                } else {
                    permanentBarangaySelect.selectedIndex = 0;
                }
            } else {
                permanentBarangaySelect.selectedIndex = 0;
            }
        }
        window.populatePermanentBarangaysByKeys = populatePermanentBarangaysByKeys;

        // Restore saved permanent selections safely
        if (savedPermanentProvince && savedPermanentProvince.trim() !== "") {
            if (!savedPermanentProvinceKey) savedPermanentProvinceKey = findProvinceKeyByName(savedPermanentProvince);

            if (savedPermanentProvinceKey) {
                permanentProvinceSelect.value = savedPermanentProvinceKey;

                // Only populate cities if savedPermanentCity exists
                if (savedPermanentCity && savedPermanentCity.trim() !== "") {
                    populatePermanentCitiesByProvinceKey(savedPermanentProvinceKey, savedPermanentCity);

                    const cityKey = findCityKeyByName(savedPermanentProvinceKey, savedPermanentCity);
                    if (cityKey) {
                        permanentCitySelect.value = cityKey;

                        // Only populate barangays if savedPermanentBarangay exists
                        if (savedPermanentBarangay && savedPermanentBarangay.trim() !== "") {
                            populatePermanentBarangaysByKeys(savedPermanentProvinceKey, cityKey, savedPermanentBarangay);
                            const matchedBarangay = findBarangayByName(savedPermanentProvinceKey, cityKey, savedPermanentBarangay);
                            if (matchedBarangay) permanentBarangaySelect.value = matchedBarangay;
                        }
                    }
                } else {
                    // No saved city → keep placeholders
                    populatePermanentCitiesByProvinceKey(savedPermanentProvinceKey, '');
                    permanentCitySelect.selectedIndex = 0;
                    permanentBarangaySelect.innerHTML = '<option value="">Select Barangay</option>';
                }
            }
        }

        // EVENT LISTENERS for permanent address
        permanentProvinceSelect.addEventListener("change", function () {
            const selectedProvinceKey = this.value;
            populatePermanentCitiesByProvinceKey(selectedProvinceKey, '');
            permanentBarangaySelect.innerHTML = '<option value="">Select Barangay</option>';
            updateResidencyType();
        });

        permanentCitySelect.addEventListener("change", function () {
            const selectedProvinceKey = permanentProvinceSelect.value;
            const selectedCityKey = this.value;
            populatePermanentBarangaysByKeys(selectedProvinceKey, selectedCityKey, '');
            updateResidencyType();
        });

        permanentBarangaySelect.addEventListener("change", function () {
            updateResidencyType();
        });

        // ========== SAME AS CURRENT ADDRESS FEATURE ==========
        function copyCurrentToPermanent() {
            // Copy text inputs
            document.getElementById("permanentBlockLotNo").value = document.getElementById("blockLotNo").value;
            document.getElementById("permanentPhase").value = document.getElementById("phase").value;
            document.getElementById("permanentSubdivisionName").value = document.getElementById("subdivision").value;
            document.getElementById("permanentPurok").value = document.getElementById("purok").value;
            document.getElementById("permanentStreet").value = document.getElementById("street").value;

            // Copy select dropdowns
            const currentProvince = provinceSelect.value;
            const currentCity = citySelect.value;
            const currentBarangay = barangaySelect.value;

            permanentProvinceSelect.value = currentProvince;

            // Populate cities for permanent address
            if (currentProvince) {
                populatePermanentCitiesByProvinceKey(currentProvince, '');
                permanentCitySelect.value = currentCity;

                // Populate barangays for permanent address
                if (currentCity) {
                    populatePermanentBarangaysByKeys(currentProvince, currentCity, '');
                    permanentBarangaySelect.value = currentBarangay;
                }
            }

            updateResidencyType();
        }

        sameAsCurrent.addEventListener('change', function () {
            const permanent = {
                blockLotNo: document.getElementById("permanentBlockLotNo"),
                phase: document.getElementById("permanentPhase"),
                subdivision: document.getElementById("permanentSubdivisionName"),
                purok: document.getElementById("permanentPurok"),
                street: document.getElementById("permanentStreet"),
                province: permanentProvinceSelect,
                city: permanentCitySelect,
                barangay: permanentBarangaySelect
            };

            if (this.checked) {
                // Copy current to permanent
                copyCurrentToPermanent();

                // Disable permanent fields but use hidden inputs for selects
                Object.values(permanent).forEach(el => {
                    if (el.tagName === 'SELECT') {
                        el.disabled = true;
                        el.style.backgroundColor = '#e9ecef';

                        // Create hidden input to submit value
                        let hiddenInput = el.parentElement.querySelector('input[type="hidden"][data-sync="true"]');
                        if (!hiddenInput) {
                            hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = el.name;
                            hiddenInput.setAttribute('data-sync', 'true');
                            el.parentElement.appendChild(hiddenInput);
                        }
                        hiddenInput.value = el.value;
                    } else {
                        el.setAttribute('readonly', true);
                        el.style.backgroundColor = '#e9ecef';
                    }
                });

                // Keep updating permanent when current changes
                const updateHandler = () => {
                    if (sameAsCurrent.checked) {
                        copyCurrentToPermanent();
                        // Update hidden inputs
                        Object.entries(permanent).forEach(([key, permEl]) => {
                            if (permEl.tagName === 'SELECT') {
                                const hiddenInput = permEl.parentElement.querySelector('input[type="hidden"][data-sync="true"]');
                                if (hiddenInput) {
                                    hiddenInput.value = permEl.value;
                                }
                            }
                        });
                    }
                };

                provinceSelect.addEventListener("change", updateHandler);
                citySelect.addEventListener("change", updateHandler);
                barangaySelect.addEventListener("change", updateHandler);
                document.getElementById("blockLotNo").addEventListener("input", updateHandler);
                document.getElementById("phase").addEventListener("input", updateHandler);
                document.getElementById("subdivision").addEventListener("input", updateHandler);
                document.getElementById("purok").addEventListener("input", updateHandler);
                document.getElementById("street").addEventListener("input", updateHandler);

            } else {
                // Re-enable permanent fields
                Object.values(permanent).forEach(el => {
                    if (el.tagName === 'SELECT') {
                        el.disabled = false;
                        el.style.backgroundColor = '';

                        // Remove hidden input
                        const hiddenInput = el.parentElement.querySelector('input[type="hidden"][data-sync="true"]');
                        if (hiddenInput) {
                            hiddenInput.remove();
                        }
                    } else {
                        el.removeAttribute('readonly');
                        el.style.backgroundColor = '';
                    }

                    // Respect edit mode
                    if (!isEdit) {
                        el.disabled = true;
                    }
                });
            }
        });

        updateResidencyType();
    })
    .catch(error => {
        console.error('Error loading JSON:', error);
    });



document.getElementById('lengthOfStay').addEventListener('input', function() {
    const lengthInput = parseInt(this.value.replace(/\D/g, '')) || 0; 
    const age = parseInt(document.getElementById('age').value) || 0;

    if (lengthInput > age) {
        this.value = age;
    }
});


// ========== OCCUPATION FIELDS ==========
const occupation = document.getElementById('occupation');
const employedDiv = document.getElementById('employedDiv');
const studentLevelDiv = document.getElementById('studentLevelDiv');
const shsTrackDiv = document.getElementById('shsTrackDiv');
const collegeCourseDiv = document.getElementById('collegeCourseDiv');
const collegeYearDiv = document.getElementById('collegeYearDiv');
const studentLevel = document.getElementById('studentLevel');

// Function to update occupation-related fields visibility
function updateOccupationFields() {
    const occupationValue = occupation.value;
    const studentLevelValue = studentLevel.value;
    
    // Reset all fields first
    employedDiv.style.display = 'none';
    studentLevelDiv.style.display = 'none';
    shsTrackDiv.style.display = 'none';
    collegeCourseDiv.style.display = 'none';
    collegeYearDiv.style.display = 'none';
    
    // Show fields based on occupation
    if (occupationValue === 'Employed') {
        employedDiv.style.display = 'block';
    } else if (occupationValue === 'Student') {
        studentLevelDiv.style.display = 'block';
        
        // Show nested student fields
        if (studentLevelValue === 'Senior High School') {
            shsTrackDiv.style.display = 'block';
        } else if (studentLevelValue === 'College') {
            collegeCourseDiv.style.display = 'block';
            collegeYearDiv.style.display = 'block';
        }
    }
}

// Initial load - check if there are saved values
updateOccupationFields();

occupation.addEventListener('change', function() {
    // Clear nested values when changing occupation
    if (this.value !== 'Student') {
        studentLevel.value = '';
    }
    updateOccupationFields();
});

studentLevel.addEventListener('change', function() {
    // Clear nested values when changing student level
    if (this.value !== 'Senior High School') {
        document.getElementById('shsTrack').value = '';
    }
    if (this.value !== 'College') {
        document.getElementById('collegeCourse').value = '';
        document.getElementById('collegeYear').value = '';
    }
    updateOccupationFields();
});