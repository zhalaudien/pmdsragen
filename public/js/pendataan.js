/**
 * Sistem Pendataan Pemuda Kabupaten Sragen
 * Pendataan Form Interactive Logic with Tom Select Searchable Dropdowns
 */

// Master data desa/kelurahan per kecamatan di Sragen
const sragenVillages = {
    "1": ["Sragen Wetan", "Sragen Kulon", "Sragen Tengah", "Nglorog", "Sine", "Karangtengah", "Kroyo", "Tangkil"],
    "2": ["Kujon", "Plumbungan", "Puro", "Saradan", "Guworejo", "Mojorejo", "Jurangjero", "Pelemgadung", "Kedungwaduk", "Ngringkwit"],
    "3": ["Sidoharjo", "Jetak", "Purwosuman", "Patihan", "Bentak", "Duyungan", "Sribit", "Taraman", "Tenggak", "Jambanan", "Pandak", "Singopadu"],
    "4": ["Gemolong", "Kwangen", "Ngembatpadas", "Kragilan", "Jenalas", "Kaloran", "Purworejo", "Peleman", "Brangkal", "Tlogotirto", "Jatibatur", "Nganti", "Kalenan"],
    "5": ["Kalijambe", "Banaran", "Donoyudan", "Krikilan", "Ngetal", "Saren", "Tegaldowo", "Trobayan", "Wonorejo", "Bukuran", "Karangjati"],
    "6": ["Plupuh", "Dari", "Gedongan", "Gentanbanaran", "Jabung", "Karanganyar", "Karangwaru", "Krikil", "Manyarejo", "Ngrombo", "Padas", "Sambirejo", "Somomorodukuh"],
    "7": ["Masaran", "Dawungan", "Gebang", "Jati", "Karangmalang", "Kliwonan", "Krebet", "Pilangsari", "Pringanom", "Sepat", "Sidodadi"],
    "8": ["Kedawung", "Bendungan", "Celep", "Jatimulyo", "Karangpelem", "Mojokerto", "Pengkok", "Wonokerso", "Wonorejo"],
    "9": ["Sambirejo", "Blimbing", "Dawung", "Jambeyan", "Jetis", "Musuk", "Sukorejo"],
    "10": ["Gondang", "Banyurip", "Glonggong", "Kaliwedi", "Plosorejo", "Tegalrejo", "Tunggul", "Wonotolo"],
    "11": ["Sambungmacan", "Banaran", "Bedoro", "Cemeng", "Gringging", "Karanganyar", "Plumbon", "Toyogo"],
    "12": ["Ngrampal", "Bener", "Gabus", "Karangudi", "Kebonromo", "Klandungan", "Pilangsari", "Ngarum"],
    "13": ["Tanon", "Bonagung", "Gading", "Gentan", "Kalikobok", "Karangtalun", "Karangasem", "Ketro", "Padas", "Pengkol", "Sambiduwur", "Slogo", "Suwatu"],
    "14": ["Sumberlawang", "Cepoko", "Hadiluwih", "Jati", "Kacangan", "Mojopuro", "Ngandul", "Ngargosari", "Ngargotirto", "Pagak", "Pendem", "Tlogorejo"],
    "15": ["Mondokan", "Gemantar", "Jekawal", "Kedawung", "Pare", "Sono", "Sumberejo", "Tempelrejo", "Trombol"],
    "16": ["Sukodono", "Baleharjo", "Bendo", "Gebang", "Jatitengah", "Juwok", "Karang Anom", "Majenang", "Newung", "Pantirejo"],
    "17": ["Gesi", "Blangu", "Poleng", "Slendro", "Srawung", "Tanggan"],
    "18": ["Tangen", "Denanyar", "Dukuh", "Galeh", "Katelan", "Ngrombo", "Sigit"],
    "19": ["Jenar", "Banyurip", "Dawung", "Japoh", "Kandangsapi", "Mlale", "Ngepringan"],
    "20": ["Miri", "Bagor", "Doyong", "Geneng", "Girimargo", "Jeruk", "Soko", "Sunggingan", "Brojol"]
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

function goToStep(step) {
    if (step < 1 || step > totalSteps) return;

    // If trying to move forward, check validation of current step
    if (step > currentStep) {
        if (!validateStep(currentStep)) {
            return;
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

function validateAndNext(step) {
    if (validateStep(step)) {
        goToStep(step + 1);
    }
}

// Dependent Dropdown for Kecamatan -> Desa (handles both TomSelect and standard HTML select)
function handleDistrictChange(districtId) {
    const villageSelect = document.getElementById('village_id');
    const villages = sragenVillages[districtId] || [];

    if (villageTomSelect) {
        villageTomSelect.clear();
        villageTomSelect.clearOptions();

        if (villages.length > 0) {
            const options = villages.map((v, idx) => ({
                value: (idx + 1).toString(),
                text: v
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
            villages.forEach((v, idx) => {
                const opt = document.createElement('option');
                opt.value = idx + 1;
                opt.textContent = v;
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

// Dynamic Organization Repeater
function addOrganizationRow() {
    const container = document.getElementById('organizationRepeaterContainer');
    if (!container) return;

    const newIndex = orgCount++;
    const rowId = `org-row-${newIndex}`;

    const rowHtml = `
        <div class="repeater-item" id="${rowId}">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold px-3 py-2 rounded-pill">
                    <i class="bi bi-flag-fill me-1"></i> Pengalaman Organisasi #${newIndex + 1}
                </span>
                <button type="button" class="btn btn-outline-danger btn-sm border-0" onclick="removeOrganizationRow('${rowId}')" title="Hapus Organisasi">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Organisasi / Lembaga / Komunitas</label>
                    <input type="text" class="form-control" name="organizations[${newIndex}][name]" placeholder="Contoh: BEM / Karang Taruna / Saka Bhayangkara">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jabatan / Posisi</label>
                    <input type="text" class="form-control" name="organizations[${newIndex}][position]" placeholder="Contoh: Koordinator Lapangan / Anggota">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal / Tahun Mulai</label>
                    <input type="date" class="form-control" name="organizations[${newIndex}][join_date]">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal / Tahun Selesai</label>
                    <input type="date" class="form-control" name="organizations[${newIndex}][end_date]">
                    <div class="form-text">Biarkan kosong jika masih aktif.</div>
                </div>
                <div class="col-12">
                    <label class="form-label">Deskripsi Peran & Program yang Pernah Diikuti</label>
                    <textarea class="form-control" name="organizations[${newIndex}][description]" rows="2" placeholder="Jelaskan kontribusi Anda..."></textarea>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', rowHtml);
}

function removeOrganizationRow(rowId) {
    const row = document.getElementById(rowId);
    if (row) {
        row.remove();
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
