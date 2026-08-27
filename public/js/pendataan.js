/**
 * Sistem Pendataan Pemuda Kabupaten Sragen
 * Pendataan Form Interactive Logic with Tom Select Searchable Dropdowns
 */

// Master data desa/kelurahan per kecamatan di Sragen dengan ID Database yang akurat
const sragenVillages = {
    "1": [
        { id: 1, name: "Sragen Wetan" }, { id: 2, name: "Sragen Kulon" }, { id: 3, name: "Sragen Tengah" },
        { id: 4, name: "Nglorog" }, { id: 5, name: "Sine" }, { id: 6, name: "Karangtengah" },
        { id: 7, name: "Kroyo" }, { id: 8, name: "Tangkil" }
    ],
    "2": [
        { id: 9, name: "Kujon" }, { id: 10, name: "Plumbungan" }, { id: 11, name: "Puro" },
        { id: 12, name: "Saradan" }, { id: 13, name: "Guworejo" }, { id: 14, name: "Mojorejo" },
        { id: 15, name: "Jurangjero" }, { id: 16, name: "Pelemgadung" }, { id: 17, name: "Kedungwaduk" },
        { id: 18, name: "Ngringkwit" }
    ],
    "3": [
        { id: 19, name: "Sidoharjo" }, { id: 20, name: "Jetak" }, { id: 21, name: "Purwosuman" },
        { id: 22, name: "Patihan" }, { id: 23, name: "Bentak" }, { id: 24, name: "Duyungan" },
        { id: 25, name: "Sribit" }, { id: 26, name: "Taraman" }, { id: 27, name: "Tenggak" },
        { id: 28, name: "Jambanan" }, { id: 29, name: "Pandak" }, { id: 30, name: "Singopadu" }
    ],
    "4": [
        { id: 31, name: "Gemolong" }, { id: 32, name: "Kwangen" }, { id: 33, name: "Ngembatpadas" },
        { id: 34, name: "Kragilan" }, { id: 35, name: "Jenalas" }, { id: 36, name: "Kaloran" },
        { id: 37, name: "Purworejo" }, { id: 38, name: "Peleman" }, { id: 39, name: "Brangkal" },
        { id: 40, name: "Tlogotirto" }, { id: 41, name: "Jatibatur" }, { id: 42, name: "Nganti" },
        { id: 43, name: "Kalenan" }
    ],
    "5": [
        { id: 44, name: "Kalijambe" }, { id: 45, name: "Banaran" }, { id: 46, name: "Donoyudan" },
        { id: 47, name: "Krikilan" }, { id: 48, name: "Ngetal" }, { id: 49, name: "Saren" },
        { id: 50, name: "Tegaldowo" }, { id: 51, name: "Trobayan" }, { id: 52, name: "Wonorejo" },
        { id: 53, name: "Bukuran" }, { id: 54, name: "Karangjati" }
    ],
    "6": [
        { id: 55, name: "Plupuh" }, { id: 56, name: "Dari" }, { id: 57, name: "Gedongan" },
        { id: 58, name: "Gentanbanaran" }, { id: 59, name: "Jabung" }, { id: 60, name: "Karanganyar" },
        { id: 61, name: "Karangwaru" }, { id: 62, name: "Krikil" }, { id: 63, name: "Manyarejo" },
        { id: 64, name: "Ngrombo" }, { id: 65, name: "Padas" }, { id: 66, name: "Sambirejo" },
        { id: 67, name: "Somomorodukuh" }
    ],
    "7": [
        { id: 68, name: "Masaran" }, { id: 69, name: "Dawungan" }, { id: 70, name: "Gebang" },
        { id: 71, name: "Jati" }, { id: 72, name: "Karangmalang" }, { id: 73, name: "Kliwonan" },
        { id: 74, name: "Krebet" }, { id: 75, name: "Pilangsari" }, { id: 76, name: "Pringanom" },
        { id: 77, name: "Sepat" }, { id: 78, name: "Sidodadi" }
    ],
    "8": [
        { id: 79, name: "Kedawung" }, { id: 80, name: "Bendungan" }, { id: 81, name: "Celep" },
        { id: 82, name: "Jatimulyo" }, { id: 83, name: "Karangpelem" }, { id: 84, name: "Mojokerto" },
        { id: 85, name: "Pengkok" }, { id: 86, name: "Wonokerso" }, { id: 87, name: "Wonorejo" }
    ],
    "9": [
        { id: 88, name: "Sambirejo" }, { id: 89, name: "Blimbing" }, { id: 90, name: "Dawung" },
        { id: 91, name: "Jambeyan" }, { id: 92, name: "Jetis" }, { id: 93, name: "Musuk" },
        { id: 94, name: "Sukorejo" }
    ],
    "10": [
        { id: 95, name: "Gondang" }, { id: 96, name: "Banyurip" }, { id: 97, name: "Glonggong" },
        { id: 98, name: "Kaliwedi" }, { id: 99, name: "Plosorejo" }, { id: 100, name: "Tegalrejo" },
        { id: 101, name: "Tunggul" }, { id: 102, name: "Wonotolo" }
    ],
    "11": [
        { id: 103, name: "Sambungmacan" }, { id: 104, name: "Banaran" }, { id: 105, name: "Bedoro" },
        { id: 106, name: "Cemeng" }, { id: 107, name: "Gringging" }, { id: 108, name: "Karanganyar" },
        { id: 109, name: "Plumbon" }, { id: 110, name: "Toyogo" }
    ],
    "12": [
        { id: 111, name: "Ngrampal" }, { id: 112, name: "Bener" }, { id: 113, name: "Gabus" },
        { id: 114, name: "Karangudi" }, { id: 115, name: "Kebonromo" }, { id: 116, name: "Klandungan" },
        { id: 117, name: "Pilangsari" }, { id: 118, name: "Ngarum" }
    ],
    "13": [
        { id: 119, name: "Tanon" }, { id: 120, name: "Bonagung" }, { id: 121, name: "Gading" },
        { id: 122, name: "Gentan" }, { id: 123, name: "Kalikobok" }, { id: 124, name: "Karangtalun" },
        { id: 125, name: "Karangasem" }, { id: 126, name: "Ketro" }, { id: 127, name: "Padas" },
        { id: 128, name: "Pengkol" }, { id: 129, name: "Sambiduwur" }, { id: 130, name: "Slogo" },
        { id: 131, name: "Suwatu" }
    ],
    "14": [
        { id: 132, name: "Sumberlawang" }, { id: 133, name: "Cepoko" }, { id: 134, name: "Hadiluwih" },
        { id: 135, name: "Jati" }, { id: 136, name: "Kacangan" }, { id: 137, name: "Mojopuro" },
        { id: 138, name: "Ngandul" }, { id: 139, name: "Ngargosari" }, { id: 140, name: "Ngargotirto" },
        { id: 141, name: "Pagak" }, { id: 142, name: "Pendem" }, { id: 143, name: "Tlogorejo" }
    ],
    "15": [
        { id: 144, name: "Mondokan" }, { id: 145, name: "Gemantar" }, { id: 146, name: "Jekawal" },
        { id: 147, name: "Kedawung" }, { id: 148, name: "Pare" }, { id: 149, name: "Sono" },
        { id: 150, name: "Sumberejo" }, { id: 151, name: "Tempelrejo" }, { id: 152, name: "Trombol" }
    ],
    "16": [
        { id: 153, name: "Sukodono" }, { id: 154, name: "Baleharjo" }, { id: 155, name: "Bendo" },
        { id: 156, name: "Gebang" }, { id: 157, name: "Jatitengah" }, { id: 158, name: "Juwok" },
        { id: 159, name: "Karang Anom" }, { id: 160, name: "Majenang" }, { id: 161, name: "Newung" },
        { id: 162, name: "Pantirejo" }
    ],
    "17": [
        { id: 163, name: "Gesi" }, { id: 164, name: "Blangu" }, { id: 165, name: "Poleng" },
        { id: 166, name: "Slendro" }, { id: 167, name: "Srawung" }, { id: 168, name: "Tanggan" }
    ],
    "18": [
        { id: 169, name: "Tangen" }, { id: 170, name: "Denanyar" }, { id: 171, name: "Dukuh" },
        { id: 172, name: "Galeh" }, { id: 173, name: "Katelan" }, { id: 174, name: "Ngrombo" },
        { id: 175, name: "Sigit" }
    ],
    "19": [
        { id: 176, name: "Jenar" }, { id: 177, name: "Banyurip" }, { id: 178, name: "Dawung" },
        { id: 179, name: "Japoh" }, { id: 180, name: "Kandangsapi" }, { id: 181, name: "Mlale" },
        { id: 182, name: "Ngepringan" }
    ],
    "20": [
        { id: 183, name: "Miri" }, { id: 184, name: "Bagor" }, { id: 185, name: "Doyong" },
        { id: 186, name: "Geneng" }, { id: 187, name: "Girimargo" }, { id: 188, name: "Jeruk" },
        { id: 189, name: "Soko" }, { id: 190, name: "Sunggingan" }, { id: 191, name: "Brojol" }
    ]
};

let currentStep = 1;
const totalSteps = 8;
let orgCount = 1;

// Tom Select Instances
let cabangTomSelect = null;
let districtTomSelect = null;
let villageTomSelect = null;

/**
 * Initialize Tom Select on Cabang, Kecamatan, and Desa
 */
function initSearchableSelects() {
    if (typeof TomSelect === 'undefined') return;

    // 1. Cabang Searchable Select (with optgroups)
    const cabangEl = document.getElementById('cabang_id');
    if (cabangEl && !cabangTomSelect) {
        cabangTomSelect = new TomSelect(cabangEl, {
            create: false,
            placeholder: '-- Ketik nama cabang untuk mencari... --',
            allowEmptyOption: true,
            maxOptions: 100,
            searchField: ['text', 'optgroup'],
            render: {
                no_results: function(data, escape) {
                    return '<div class="no-results text-muted small p-2"><i class="bi bi-search me-1"></i> Cabang tidak ditemukan</div>';
                }
            },
            onChange: function() {
                cabangEl.classList.remove('is-invalid');
                const tsWrap = cabangEl.nextElementSibling;
                if (tsWrap && tsWrap.classList.contains('ts-wrapper')) {
                    tsWrap.classList.remove('is-invalid');
                }
            }
        });
    }

    // 2. Kecamatan Searchable Select
    const districtEl = document.getElementById('district_id');
    if (districtEl && !districtTomSelect) {
        districtTomSelect = new TomSelect(districtEl, {
            create: false,
            placeholder: '-- Ketik nama kecamatan untuk mencari... --',
            allowEmptyOption: true,
            render: {
                no_results: function(data, escape) {
                    return '<div class="no-results text-muted small p-2"><i class="bi bi-search me-1"></i> Kecamatan tidak ditemukan</div>';
                }
            },
            onChange: function(value) {
                handleDistrictChange(value);
                districtEl.classList.remove('is-invalid');
                const tsWrap = districtEl.nextElementSibling;
                if (tsWrap && tsWrap.classList.contains('ts-wrapper')) {
                    tsWrap.classList.remove('is-invalid');
                }
            }
        });
    }

    // 3. Desa / Kelurahan Searchable Select
    const villageEl = document.getElementById('village_id');
    if (villageEl && !villageTomSelect) {
        villageTomSelect = new TomSelect(villageEl, {
            create: false,
            placeholder: '-- Pilih kecamatan terlebih dahulu --',
            allowEmptyOption: true,
            render: {
                no_results: function(data, escape) {
                    return '<div class="no-results text-muted small p-2"><i class="bi bi-search me-1"></i> Desa tidak ditemukan</div>';
                }
            },
            onChange: function() {
                villageEl.classList.remove('is-invalid');
                const tsWrap = villageEl.nextElementSibling;
                if (tsWrap && tsWrap.classList.contains('ts-wrapper')) {
                    tsWrap.classList.remove('is-invalid');
                }
            }
        });
    }
}

function updateProgress(step) {
    const percentage = ((step - 1) / (totalSteps - 1)) * 100;
    const fillEl = document.getElementById('stepperProgressFill');
    if (fillEl) {
        fillEl.style.width = percentage + '%';
    }

    document.querySelectorAll('.stepper-item').forEach(item => {
        const itemStep = parseInt(item.getAttribute('data-step'));
        item.classList.remove('active', 'completed');
        if (itemStep === step) {
            item.classList.add('active');
        } else if (itemStep < step) {
            item.classList.add('completed');
        }
    });
}

/**
 * Asynchronously checks if Pemuda with given name, birth_date, and cabang_id already exists.
 * Returns true if available (no duplicate), false if duplicate exists.
 */
async function checkDuplicatePemuda() {
    const nameEl = document.getElementById('name');
    const birthDateEl = document.getElementById('birth_date');
    const cabangEl = document.getElementById('cabang_id');
    const warningBox = document.getElementById('duplicate-warning-box');
    const warningMsg = document.getElementById('duplicate-warning-message');
    const btnNext1 = document.getElementById('btnNextStep1') || document.querySelector('#step-1 button.btn-primary-pmd');

    const name = nameEl ? nameEl.value.trim() : '';
    const birthDate = birthDateEl ? birthDateEl.value.trim() : '';
    let cabangId = cabangEl ? cabangEl.value.trim() : '';
    if (typeof cabangTomSelect !== 'undefined' && cabangTomSelect) {
        cabangId = cabangTomSelect.getValue();
    }

    if (!name || !birthDate || !cabangId) {
        return true;
    }

    let originalBtnHtml = '';
    if (btnNext1) {
        originalBtnHtml = btnNext1.innerHTML;
        btnNext1.disabled = true;
        btnNext1.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Memeriksa data...';
    }

    try {
        const formData = new FormData();
        formData.append('name', name);
        formData.append('birth_date', birthDate);
        formData.append('cabang_id', cabangId);

        if (typeof PENDATAAN_CONFIG !== 'undefined' && PENDATAAN_CONFIG.csrfToken && PENDATAAN_CONFIG.csrfHash) {
            formData.append(PENDATAAN_CONFIG.csrfToken, PENDATAAN_CONFIG.csrfHash);
        }

        const url = (typeof PENDATAAN_CONFIG !== 'undefined' && PENDATAAN_CONFIG.checkDuplicateUrl)
            ? PENDATAAN_CONFIG.checkDuplicateUrl
            : '/pendataan/check-duplicate';

        const response = await fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error('HTTP error ' + response.status);
        }

        const res = await response.json();

        if (res.csrfHash && typeof PENDATAAN_CONFIG !== 'undefined') {
            PENDATAAN_CONFIG.csrfHash = res.csrfHash;
        }

        if (res.duplicate) {
            if (warningBox && warningMsg) {
                warningMsg.innerHTML = res.message;
                warningBox.style.display = 'block';
                warningBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }

            if (nameEl) nameEl.classList.add('is-invalid');
            if (birthDateEl) birthDateEl.classList.add('is-invalid');
            if (cabangEl) {
                cabangEl.classList.add('is-invalid');
                const tsWrap = cabangEl.nextElementSibling;
                if (tsWrap && tsWrap.classList.contains('ts-wrapper')) {
                    tsWrap.classList.add('is-invalid');
                }
            }

            return false;
        } else {
            if (warningBox) {
                warningBox.style.display = 'none';
            }
            if (nameEl) nameEl.classList.remove('is-invalid');
            if (birthDateEl) birthDateEl.classList.remove('is-invalid');
            return true;
        }
    } catch (err) {
        console.warn('Gagal memverifikasi duplikat data:', err);
        return true;
    } finally {
        if (btnNext1) {
            btnNext1.disabled = false;
            btnNext1.innerHTML = originalBtnHtml;
        }
    }
}

async function goToStep(step) {
    if (step < 1 || step > totalSteps) return;

    // If trying to move forward, check validation of current step
    if (step > currentStep) {
        if (!validateStep(currentStep)) {
            return;
        }

        // If moving forward from Step 1, perform duplicate check
        if (currentStep === 1) {
            const isAvailable = await checkDuplicatePemuda();
            if (!isAvailable) {
                return;
            }
        }
    }

    // Hide all step sections
    document.querySelectorAll('.form-step-section').forEach(sec => {
        sec.classList.remove('active');
    });

    // Show target step section
    const targetSection = document.getElementById('step-' + step);
    if (targetSection) {
        targetSection.classList.add('active');
        currentStep = step;
        updateProgress(step);
        if (step === 8) {
            prepareReview();
        }
        window.scrollTo({ top: targetSection.offsetTop - 80, behavior: 'smooth' });
    }
}

function validateStep(step) {
    const currentSection = document.getElementById('step-' + step);
    if (!currentSection) return true;

    const inputs = currentSection.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;

    inputs.forEach(input => {
        const tsWrapper = input.nextElementSibling && input.nextElementSibling.classList.contains('ts-wrapper') 
                          ? input.nextElementSibling 
                          : null;

        if (input.type === 'radio') {
            const radioGroup = currentSection.querySelectorAll(`input[name="${input.name}"]`);
            const isChecked = Array.from(radioGroup).some(r => r.checked);
            if (!isChecked) {
                isValid = false;
                const errEl = document.getElementById(input.name + '-error');
                if (errEl) errEl.style.display = 'block';
            }
        } else if (!input.checkValidity() || !input.value || input.value.trim() === '') {
            isValid = false;
            input.classList.add('is-invalid');
            if (tsWrapper) {
                tsWrapper.classList.add('is-invalid');
            }
        } else {
            input.classList.remove('is-invalid');
            if (tsWrapper) {
                tsWrapper.classList.remove('is-invalid');
            }
        }
    });

    return isValid;
}

async function validateAndNext(step) {
    if (!validateStep(step)) {
        return;
    }

    if (step === 1) {
        const isAvailable = await checkDuplicatePemuda();
        if (!isAvailable) {
            return;
        }
    }

    goToStep(step + 1);
}

// Dependent Dropdown for Kecamatan -> Desa (handles both TomSelect and standard HTML select)
function handleDistrictChange(districtId) {
    const villageSelect = document.getElementById('village_id');
    const villages = sragenVillages[districtId] || [];

    if (villageTomSelect) {
        villageTomSelect.clear();
        villageTomSelect.clearOptions();

        if (villages.length > 0) {
            const options = villages.map(v => ({
                value: v.id.toString(),
                text: v.name
            }));
            villageTomSelect.addOptions(options);
            villageTomSelect.settings.placeholder = '-- Ketik nama desa/kelurahan untuk mencari... --';
            villageTomSelect.inputState();
            villageTomSelect.enable();
        } else {
            villageTomSelect.settings.placeholder = '-- Desa tidak ditemukan --';
            villageTomSelect.inputState();
            villageTomSelect.disable();
        }
    } else if (villageSelect) {
        if (villages.length > 0) {
            villageSelect.innerHTML = '<option value="" selected disabled>-- Pilih Desa / Kelurahan --</option>';
            villages.forEach(v => {
                const opt = document.createElement('option');
                opt.value = v.id;
                opt.textContent = v.name;
                villageSelect.appendChild(opt);
            });
        } else {
            villageSelect.innerHTML = '<option value="" selected disabled>-- Desa tidak ditemukan --</option>';
        }
    }
}

// Toggle skill level dropdown
function toggleSkillLevel(skillId) {
    const checkbox = document.getElementById('skill_' + skillId);
    const levelBox = document.getElementById('skill_level_box_' + skillId);
    if (checkbox && levelBox) {
        levelBox.style.display = checkbox.checked ? 'block' : 'none';
        levelBox.querySelectorAll('select, input').forEach(el => {
            el.disabled = !checkbox.checked;
        });
    }
}

// Handle job status toggles
function handleJobStatusChange(statusId) {
    const isNotWorking = (statusId === '1'); // Belum Bekerja
    
    const titleWrapper = document.getElementById('wrapper-job-title');
    const compWrapper = document.getElementById('wrapper-company-name');
    const fieldWrapper = document.getElementById('wrapper-business-field');

    if (titleWrapper && compWrapper && fieldWrapper) {
        if (isNotWorking) {
            titleWrapper.style.opacity = '0.5';
            compWrapper.style.opacity = '0.5';
            fieldWrapper.style.opacity = '0.5';
        } else {
            titleWrapper.style.opacity = '1';
            compWrapper.style.opacity = '1';
            fieldWrapper.style.opacity = '1';
        }
    }
}

// Toggle Organization detail panel
function toggleOrgDetail(orgKey) {
    const checkbox = document.getElementById('org_' + orgKey);
    const detailBox = document.getElementById('org_detail_' + orgKey);
    const cardBox = document.getElementById('org_card_box_' + orgKey);
    if (checkbox) {
        if (detailBox) {
            detailBox.style.display = checkbox.checked ? 'block' : 'none';
            detailBox.querySelectorAll('input, select, textarea').forEach(el => {
                el.disabled = !checkbox.checked;
            });
        }
        if (cardBox) {
            if (checkbox.checked) {
                cardBox.classList.add('selected');
            } else {
                cardBox.classList.remove('selected');
            }
        }
    }
}

// Prepare Review Step 8
function prepareReview() {
    // Pribadi
    const nameEl = document.getElementById('name');
    if (nameEl) document.getElementById('rev_name').innerText = nameEl.value || '-';
    
    const genderEl = document.querySelector('input[name="gender"]:checked');
    const genderText = genderEl ? (genderEl.value === 'L' ? 'Laki-laki' : 'Perempuan') : '-';
    const birthPlace = document.getElementById('birth_place')?.value || '-';
    const birthDate = document.getElementById('birth_date')?.value || '-';
    const revTtl = document.getElementById('rev_ttl');
    if (revTtl) revTtl.innerText = `${genderText} | ${birthPlace}, ${birthDate}`;
    
    // Status Pernikahan & Golongan Darah
    const maritalEl = document.getElementById('marital_status');
    const maritalText = maritalEl && maritalEl.value ? (maritalEl.options[maritalEl.selectedIndex]?.text || '-') : '-';
    const bloodEl = document.getElementById('blood_type');
    const bloodText = bloodEl && bloodEl.value ? (bloodEl.options[bloodEl.selectedIndex]?.text || '-') : '-';
    const revMaritalBlood = document.getElementById('rev_marital_blood');
    if (revMaritalBlood) {
        revMaritalBlood.innerText = `${maritalText} | Gol. Darah: ${bloodText}`;
    }

    const phone = document.getElementById('phone')?.value || '-';
    const email = document.getElementById('email')?.value || '(Tanpa email)';
    const revContact = document.getElementById('rev_contact');
    if (revContact) revContact.innerText = `${phone} / ${email}`;

    // Cabang text from TomSelect or native option
    const cabangEl = document.getElementById('cabang_id');
    const revCabang = document.getElementById('rev_cabang');
    if (cabangEl && revCabang) {
        if (cabangTomSelect && cabangTomSelect.getValue()) {
            const item = cabangTomSelect.getItem(cabangTomSelect.getValue());
            revCabang.innerText = item ? item.innerText.trim() : (cabangEl.options[cabangEl.selectedIndex]?.text || '-');
        } else {
            revCabang.innerText = cabangEl.options[cabangEl.selectedIndex]?.text || '-';
        }
    }

    // Alamat text from TomSelect or native options
    const kecEl = document.getElementById('district_id');
    const desEl = document.getElementById('village_id');
    let kecText = '';
    let desText = '';

    if (districtTomSelect && districtTomSelect.getValue()) {
        const item = districtTomSelect.getItem(districtTomSelect.getValue());
        kecText = item ? item.innerText.trim() : (kecEl ? kecEl.options[kecEl.selectedIndex]?.text || '' : '');
    } else {
        kecText = kecEl ? (kecEl.options[kecEl.selectedIndex]?.text || '') : '';
    }

    if (villageTomSelect && villageTomSelect.getValue()) {
        const item = villageTomSelect.getItem(villageTomSelect.getValue());
        desText = item ? item.innerText.trim() : (desEl ? desEl.options[desEl.selectedIndex]?.text || '' : '');
    } else {
        desText = desEl ? (desEl.options[desEl.selectedIndex]?.text || '') : '';
    }

    const dusun = document.getElementById('dusun')?.value || '';
    const rt = document.getElementById('rt')?.value || '';
    const rw = document.getElementById('rw')?.value || '';
    const detail = document.getElementById('address_detail')?.value || '';
    
    const revAddress = document.getElementById('rev_address');
    if (revAddress) {
        revAddress.innerText = `${detail}, ${dusun ? dusun + ',' : ''} RT ${rt || '00'}/RW ${rw || '00'}, Desa ${desText}, Kec. ${kecText}, Kab. Sragen`;
    }

    // Pendidikan
    const eduEl = document.getElementById('education_level_id');
    const eduLevel = eduEl ? (eduEl.options[eduEl.selectedIndex]?.text || '-') : '-';
    const school = document.getElementById('school_name')?.value || '-';
    const major = document.getElementById('major')?.value || '';
    const gradYear = document.getElementById('graduation_year')?.value || '';
    const revEducation = document.getElementById('rev_education');
    if (revEducation) {
        revEducation.innerText = `${eduLevel} - ${school} ${major ? '(' + major + ')' : ''} ${gradYear ? 'Th. ' + gradYear : ''}`;
    }

    // Pekerjaan
    const jobStatusEl = document.getElementById('job_status_id');
    const jobStatus = jobStatusEl ? (jobStatusEl.options[jobStatusEl.selectedIndex]?.text || '-') : '-';
    const jobTitle = document.getElementById('job_title')?.value || '';
    const compName = document.getElementById('company_name')?.value || '';
    const revJob = document.getElementById('rev_job');
    if (revJob) {
        revJob.innerText = `${jobStatus} ${jobTitle ? ' | ' + jobTitle : ''} ${compName ? ' di ' + compName : ''}`;
    }

    // Organisasi / Divisi
    const checkedOrgs = [];
    document.querySelectorAll('.org-toggle-check:checked').forEach(chk => {
        const title = chk.getAttribute('data-title') || chk.value;
        const orgKey = chk.getAttribute('data-key');
        const posInput = document.querySelector(`input[name="organizations[${orgKey}][position]"]`);
        const pos = posInput && posInput.value.trim() ? posInput.value.trim() : '';
        checkedOrgs.push(pos && pos !== 'Anggota' ? `${title} (${pos})` : title);
    });
    const otherOrgInput = document.querySelector('input[name="other_organization"]');
    if (otherOrgInput && otherOrgInput.value.trim()) {
        checkedOrgs.push(otherOrgInput.value.trim());
    }
    const revOrgs = document.getElementById('rev_organizations');
    if (revOrgs) {
        revOrgs.innerText = checkedOrgs.length > 0 ? checkedOrgs.join(', ') : 'Tidak ada / Belum mengikuti';
    }

    // Keahlian & Minat
    const checkedSkills = [];
    document.querySelectorAll('.skill-toggle-check:checked').forEach(chk => {
        const label = chk.closest('.form-check')?.querySelector('label')?.innerText.trim();
        if (label) checkedSkills.push(label);
    });
    
    const checkedInterests = [];
    document.querySelectorAll('.interest-tag-checkbox:checked').forEach(chk => {
        const name = chk.getAttribute('data-name');
        if (name) checkedInterests.push(name);
    });

    const skillText = checkedSkills.length > 0 ? checkedSkills.join(', ') : 'Belum memilih keahlian spesifik';
    const interestText = checkedInterests.length > 0 ? checkedInterests.join(', ') : 'Belum memilih minat';

    const revSkillsInterests = document.getElementById('rev_skills_interests');
    if (revSkillsInterests) {
        revSkillsInterests.innerHTML = `
            <div><strong>Keahlian:</strong> ${skillText}</div>
            <div><strong>Minat:</strong> ${interestText}</div>
        `;
    }
}

// DOM Ready initialization
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Searchable Dropdowns (Tom Select)
    initSearchableSelects();

    // Input constraints (Digits only for Phone)
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9+]/g, '');
        });
    }

    // Reset duplicate warning on user input
    ['name', 'birth_date', 'cabang_id'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', function() {
                const warningBox = document.getElementById('duplicate-warning-box');
                if (warningBox && warningBox.style.display !== 'none') {
                    warningBox.style.display = 'none';
                }
                el.classList.remove('is-invalid');
                const tsWrap = el.nextElementSibling;
                if (tsWrap && tsWrap.classList.contains('ts-wrapper')) {
                    tsWrap.classList.remove('is-invalid');
                }
            });
            el.addEventListener('change', function() {
                const warningBox = document.getElementById('duplicate-warning-box');
                if (warningBox && warningBox.style.display !== 'none') {
                    warningBox.style.display = 'none';
                }
                el.classList.remove('is-invalid');
                const tsWrap = el.nextElementSibling;
                if (tsWrap && tsWrap.classList.contains('ts-wrapper')) {
                    tsWrap.classList.remove('is-invalid');
                }
            });
        }
    });

    // Initialize Organization and Skill checkboxes state (in case of browser restore or validation bounce back)
    document.querySelectorAll('.org-toggle-check').forEach(chk => {
        const orgKey = chk.getAttribute('data-key');
        if (orgKey) {
            toggleOrgDetail(orgKey);
        }
    });

    document.querySelectorAll('.skill-toggle-check').forEach(chk => {
        const skillId = chk.value;
        if (skillId) {
            toggleSkillLevel(skillId);
        }
    });

    // Form Submit Handler
    const form = document.getElementById('formPendataanPemuda');
    if (form) {
        form.addEventListener('submit', function(e) {
            const agreement = document.getElementById('agreement_check');
            if (agreement && !agreement.checked) {
                e.preventDefault();
                agreement.classList.add('is-invalid');
                agreement.focus();
                return false;
            }

            // Show loading state on submit button
            const btnSubmit = document.getElementById('btnSubmitForm');
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan Data...';
            }
        });
    }
});
