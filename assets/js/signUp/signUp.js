
let data = {};
let selectedProvince = '';
let selectedMunicipality = '';

fetch("assets/json/philippine_provinces_cities_municipalities_and_barangays_2019v2.json")
    .then(response => response.json())
    .then(jsonData => {
        data = jsonData;

        // Populate Province List (sorted)
        const provinceDatalist = document.getElementById('dataListProvince');
        const provinceNames = [];

        for (const regionCode in data) {
            const provinces = data[regionCode].province_list;
            for (const provinceName in provinces) {
                provinceNames.push(provinceName);
            }
        }

        provinceNames.sort().forEach(provinceName => {
            const option = document.createElement('option');
            option.value = provinceName;
            provinceDatalist.appendChild(option);
        });
    });

document.getElementById('provinceInput').addEventListener('input', function () {
    selectedProvince = this.value;
    const cityDatalist = document.getElementById('dataListCity');
    cityDatalist.innerHTML = '';

    for (const regionCode in data) {
        const provinces = data[regionCode].province_list;
        if (provinces[selectedProvince]) {
            const municipalities = provinces[selectedProvince].municipality_list;
            const cityNames = Object.keys(municipalities).sort();

            cityNames.forEach(city => {
                const option = document.createElement('option');
                option.value = city;
                cityDatalist.appendChild(option);
            });
            break;
        }
    }
});

document.getElementById('cityInput').addEventListener('input', function () {
    selectedMunicipality = this.value;
    const barangayDatalist = document.getElementById('dataListBrgy');
    barangayDatalist.innerHTML = '';

    for (const regionCode in data) {
        const provinces = data[regionCode].province_list;
        if (provinces[selectedProvince]) {
            const municipalities = provinces[selectedProvince].municipality_list;
            if (municipalities[selectedMunicipality]) {
                const barangays = municipalities[selectedMunicipality].barangay_list.sort();
                barangays.forEach(barangay => {
                    const option = document.createElement('option');
                    option.value = barangay;
                    barangayDatalist.appendChild(option);
                });
            }
            break;
        }
    }
});
