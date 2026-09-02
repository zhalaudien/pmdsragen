# AGENTS.md — Sistem Pendataan Pemuda

## 1. Ringkasan Project

Project ini adalah aplikasi web **Sistem Pendataan Pemuda** yang berfungsi untuk:

- Mengumpulkan data pemuda melalui form online.
- Mengelola data pemuda dari dashboard admin.
- Membagi pemuda berdasarkan **Wilayah** dan **Cabang**.
- Menyediakan hak akses bertingkat:
  - `superadmin`
  - `admin_wilayah`
  - `admin_cabang`
- Menyediakan pencarian, filter, verifikasi, statistik, dan laporan.
- Menyediakan export data, terutama Excel/CSV.
- Mendukung form dinamis seperti Google Forms pada tahap pengembangan berikutnya.

Project menggunakan:

- **Backend:** CodeIgniter 4
- **Language:** PHP 8.2+
- **Database:** MySQL
- **Frontend:** HTML5, CSS, JavaScript
- **UI:** Bootstrapm 5 AdminLte3
- **Chart:** Chart.js
- **Export:** PhpSpreadsheet
- **Database access:** CodeIgniter Model / Query Builder
- **Authentication:** CodeIgniter Session
- **Time Zone:** Asia/Jakarta

---

## 2. Prinsip Utama Pengembangan

### 2.1 Gunakan CodeIgniter 4 secara native

Utamakan fitur bawaan CodeIgniter 4:

- Controllers
- Models
- Views
- Filters
- Validation
- Migrations
- Seeders
- Query Builder
- Sessions
- Routes
- Services

Jangan membuat framework atau abstraction layer sendiri jika CodeIgniter 4 sudah menyediakan solusinya.

### 2.2 Gunakan MVC dengan jelas

Pisahkan tanggung jawab:

- **Controller:** menerima request, validasi/alur proses, memanggil model/service, mengembalikan response.
- **Model:** akses dan operasi database.
- **View:** tampilan HTML.
- **Filter:** authentication dan authorization.
- **Migration:** struktur database.
- **Seeder:** data awal/reference data.

Jangan menaruh query database kompleks langsung di View.

### 2.3 Database-first dan migration-first

Semua perubahan struktur database wajib dilakukan melalui migration.

Jangan mengandalkan perubahan manual pada database development/production.

Setiap tabel baru atau perubahan struktur harus memiliki migration CodeIgniter 4 yang jelas.

---

# 3. Struktur Organisasi Data

Struktur hierarki utama:

```text
Superadmin
    |
    +-- Wilayah 1
    |      +-- Cabang A
    |      +-- Cabang B
    |
    +-- Wilayah 2
    |      +-- Cabang C
    |      +-- Cabang D
    |
    +-- Wilayah 3
    |
    +-- Wilayah 4
```

Setiap pemuda berada pada satu cabang:

```text
Pemuda
  -> Cabang
      -> Wilayah
```

**Jangan menyimpan `wilayah_id` pada tabel `pemuda`** jika wilayah dapat ditentukan dari `cabang_id`. Hal ini mencegah inkonsistensi data.

---

# 4. Role dan Authorization

Role utama:

| Role            | Scope          |
| --------------- | -------------- |
| `superadmin`    | Seluruh sistem |
| `admin_wilayah` | Satu wilayah   |
| `admin_cabang`  | Satu cabang    |

## 4.1 Superadmin

Superadmin dapat:

- Mengelola seluruh wilayah.
- Mengelola seluruh cabang.
- Mengelola seluruh pemuda.
- Mengelola user/admin.
- Mengelola form.
- Melihat seluruh statistik.
- Import Data Pemuda dari exel
- Export seluruh data.
- Mengubah konfigurasi sistem.

Pada tabel `users`:

```text
role_id    -> role superadmin
wilayah_id -> NULL
cabang_id  -> NULL
```

## 4.2 Admin Wilayah

Admin wilayah hanya boleh mengakses data pada wilayahnya.

Pada tabel `users`:

```text
role_id    -> admin_wilayah
wilayah_id -> wilayah yang dikelola
cabang_id  -> NULL
```

Scope data:

```text
users.wilayah_id
    |
    +-- cabang
          |
          +-- pemuda
```

## 4.3 Admin Cabang

Admin cabang hanya boleh mengakses data pada cabangnya.

Pada tabel `users`:

```text
role_id    -> admin_cabang
wilayah_id -> wilayah cabang tersebut
cabang_id  -> cabang yang dikelola
```

Scope data:

```text
users.cabang_id
    |
    +-- pemuda
```

## 4.4 Authorization wajib dilakukan di server

Jangan hanya menyembunyikan menu berdasarkan role.

Contoh yang tidak cukup:

```php
if ($userRole === 'admin_cabang') {
    // hide menu
}
```

Data juga harus dibatasi pada query/database layer.

Admin cabang tidak boleh dapat mengakses:

```text
/pemuda/123
```

jika ID 123 bukan milik cabangnya, walaupun URL tersebut diketahui.

---

# 5. Struktur Database

Tabel utama yang direncanakan:

```text
user_roles
users

wilayah
cabang

provinces
regencies
districts
villages

pemuda
alamat
pendidikan
pekerjaan
organisasi

skills
interests
pemuda_skills
pemuda_interests

education_levels
job_statuses

forms
questions
responses
answers
```

## 5.1 Relasi wilayah dan cabang

```text
wilayah
    1
    |
    N
cabang
    1
    |
    N
pemuda
```

### 5.1.1 Detail Data Cabang

Tabel `cabang` menyimpan data struktural dan operasional setiap cabang pemuda:

- `id`: INT UNSIGNED AUTO_INCREMENT
- `wilayah_id`: INT UNSIGNED (FK ke `wilayah.id`)
- `code`: VARCHAR(50) (Kode cabang, misal: CBG-001)
- `name`: VARCHAR(100) (Nama cabang)
- `description`: TEXT (Deskripsi/catatan cabang)
- `alamat`: TEXT (Alamat lengkap/sekretariat cabang)
- `pimpinan_nama`: VARCHAR(100) (Nama pimpinan cabang)
- `no_wa`: VARCHAR(20) (Nomor WhatsApp/kontak pimpinan)
- `has_gelombang`: ENUM('sudah', 'belum') (Status ketersediaan gelombang pemuda)
- `gelombang_hari`: VARCHAR(100) (Hari pelaksanaan pengajian/gelombang pemuda)
- `gelombang_jam`: VARCHAR(50) (Waktu/jam masuk pelaksanaan kegiatan)
- `gelombang_ustadz`: VARCHAR(150) (Nama ustadz yang mengampu)

## 5.2 Relasi user

```text
user_roles
    1
    |
    N
users
```

User memiliki scope:

```text
users.wilayah_id
users.cabang_id
```

## 5.3 Data pemuda

`pemuda` adalah tabel utama data individu.

Informasi tambahan dipisahkan:

```text
pemuda
    |
    +-- alamat
    +-- pendidikan
    +-- pekerjaan
    +-- organisasi
    +-- pemuda_skills
    +-- pemuda_interests
```

---

# 6. Aturan Database

## 6.1 Primary Key

Gunakan:

```sql
INT UNSIGNED AUTO_INCREMENT
```

untuk primary key tabel utama, kecuali ada alasan kuat untuk menggunakan tipe lain.

## 6.2 Foreign Key

Semua relasi penting wajib menggunakan foreign key.

Contoh:

```text
cabang.wilayah_id -> wilayah.id
pemuda.cabang_id  -> cabang.id
users.role_id     -> user_roles.id
```

## 6.3 Index

Kolom yang sering digunakan untuk:

- filter
- search
- join
- authorization scope

harus memiliki index yang sesuai.

Contoh:

```text
pemuda.cabang_id
pemuda.status_verifikasi
pemuda.status_data
cabang.wilayah_id
users.role_id
users.wilayah_id
users.cabang_id
```

## 6.4 Unique

Gunakan unique constraint untuk identifier yang memang harus unik:

```text
users.username
users.email
wilayah.code
cabang.code
pemuda.registration_number
```

---

# 7. Data Sensitif

Data seperti:

- nomor HP
- email
- alamat

harus diperlakukan sebagai data sensitif.

Aturan:

1. Jangan mencatat password dalam log.
2. Gunakan HTTPS pada production.
3. Password wajib menggunakan hashing.
4. Jangan pernah menyimpan password plaintext.
5. Batasi akses data berdasarkan role dan scope.
6. Jangan mengirim seluruh data pribadi ke frontend jika tidak diperlukan.

---

# 8. Authentication

Authentication menggunakan session CodeIgniter 4.

Password:

```php
password_hash($password, PASSWORD_DEFAULT)
```

dan verifikasi:

```php
password_verify($password, $hash)
```

Jangan membuat algoritma hashing password sendiri.

Setelah login, session minimal menyimpan informasi yang diperlukan:

```text
user_id
role
wilayah_id
cabang_id
is_logged_in
```

Jangan menyimpan password dalam session.

---

# 9. Route dan Filter

Gunakan route group untuk area admin.

Contoh konsep:

```php
$routes->group('admin', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('pemuda', 'Pemuda::index');
});
```

Authorization dapat menggunakan filter khusus:

```text
auth
role
scope
```

Contoh:

```text
/admin/dashboard
/admin/pemuda
/admin/cabang
/admin/wilayah
/admin/users
/admin/laporan
```

Area public:

```text
/
/pendataan
/pendataan/simpan
/pendataan/sukses
```

---

# 10. Form Pendataan Public

Pemuda tidak perlu login untuk mengisi form public, kecuali kebutuhan bisnis berubah.

Alur:

```text
Landing Page
    |
    v
Form Pendataan
    |
    v
Validasi
    |
    v
Simpan Transaction
    |
    +-- pemuda
    +-- alamat
    +-- pendidikan
    +-- pekerjaan
    +-- organisasi
    +-- skills
    +-- interests
    |
    v
Nomor Registrasi
    |
    v
Halaman Sukses
```

Penyimpanan beberapa tabel harus menggunakan database transaction.

Contoh:

```php
$db->transStart();

// insert pemuda
// insert alamat
// insert pendidikan
// insert pekerjaan
// insert organisasi
// insert skills
// insert interests

$db->transComplete();
```

Jika salah satu proses gagal, seluruh transaksi harus rollback.

---

# 11. Validasi

Semua input public dan admin harus divalidasi server-side.

Jangan hanya mengandalkan:

```html
required
```

di HTML.

Validasi harus dilakukan di backend.

Contoh:

```php
$rules = [
    'name' => 'required|min_length[3]|max_length[150]',
    'gender' => 'required|in_list[L,P]',
    'phone' => 'permit_empty|max_length[20]',
];
```

Untuk data cabang:

```text
cabang_id harus valid
cabang_id harus aktif
cabang_id harus berada pada wilayah yang sesuai
```

Admin cabang tidak boleh memanipulasi request untuk memasukkan pemuda ke cabang lain.

---

# 12. Query Scope

Ini adalah aturan penting.

## Superadmin

Tidak ada filter scope.

```php
$query = $this->pemudaModel;
```

## Admin Wilayah

Filter berdasarkan:

```php
->where('cabang.wilayah_id', session('wilayah_id'))
```

## Admin Cabang

Filter berdasarkan:

```php
->where('pemuda.cabang_id', session('cabang_id'))
```

Jangan hanya mengambil `pemuda` berdasarkan ID lalu memeriksa scope setelah data diambil jika hal tersebut berpotensi menyebabkan kebocoran data.

Lebih baik scope menjadi bagian query.

---

# 13. Model

Gunakan model terpisah.

Contoh:

```text
UserRoleModel
UserModel

WilayahModel
CabangModel

PemudaModel
AlamatModel
PendidikanModel
PekerjaanModel
OrganisasiModel

SkillModel
InterestModel
PemudaSkillModel
PemudaInterestModel

EducationLevelModel
JobStatusModel

FormModel
QuestionModel
ResponseModel
AnswerModel
```

Model harus mendefinisikan:

```php
protected $table;
protected $primaryKey;
protected $allowedFields;
protected $useTimestamps;
```

Jangan menggunakan:

```php
$builder->set($request->getPost());
```

secara mentah tanpa whitelist field.

---

# 14. Controller

Controller harus tetap tipis.

Contoh alur:

```text
Controller
    |
    +-- validasi request
    |
    +-- authorization/scope
    |
    +-- panggil model/service
    |
    +-- response/redirect
```

Hindari controller dengan ratusan baris query database.

Jika proses mulai kompleks, pindahkan business logic ke Service.

Contoh:

```text
app/Services/
    PemudaService.php
    AuthService.php
    FormService.php
    ReportService.php
```

---

# 15. UI/UX

Gunakan Bootstrap 5.

Prioritas:

1. Mobile responsive.
2. Form mudah diisi.
3. Dashboard mudah dibaca.
4. Tabel memiliki search dan filter.
5. Gunakan modal untuk operasi ringan.
6. Berikan confirmation sebelum delete/archive.
7. Gunakan alert/toast untuk feedback.
8. Jangan membuat form terlalu padat.

Form pendataan sebaiknya menggunakan section:

```text
Data Pribadi
Alamat
Pendidikan
Pekerjaan
Organisasi
Keahlian
Minat
Konfirmasi
```

---

# 16. Status Data Pemuda

Gunakan:

```text
status_verifikasi:
    verified (Terverifikasi — jika data sinkron/tercatat di database MTA Pusat)
    pending  (Belum Terverifikasi — jika data belum sinkron/tidak tercatat di database MTA Pusat)
```

Aturan Ketat Status Verifikasi:
1. Status verifikasi **HANYA ADA 2**: `verified` (Terverifikasi) dan `pending` (Belum Terverifikasi).
2. Status verifikasi ditentukan secara otomatis oleh sistem berdasarkan hasil sinkronisasi dengan API MTA Pusat.
3. Status verifikasi **TIDAK DAPAT diubah secara manual**, baik oleh superadmin, admin wilayah, maupun admin cabang.

dan:

```text
status_data:
    active
    archived
```

`archived` lebih disukai daripada hard delete untuk data operasional yang masih perlu dipertahankan.

Delete permanen hanya boleh dilakukan oleh role yang berwenang dan harus dipertimbangkan dengan kebijakan retensi data.

---

# 17. Form Dinamis

Fitur form dinamis direncanakan menggunakan:

```text
forms
    |
    +-- questions
             |
             +-- responses
                    |
                    +-- answers
```

Jenis pertanyaan:

```text
text
textarea
number
date
radio
checkbox
select
file
```

`questions.options` dapat menggunakan JSON untuk pilihan.

Contoh:

```json
["Olahraga", "Seni", "Teknologi", "Wirausaha"]
```

---

# 18. Naming Convention

## PHP class

Gunakan PascalCase:

```text
PemudaModel
WilayahModel
CabangController
```

## Method

Gunakan camelCase:

```php
getByCabang()
getByWilayah()
savePemuda()
```

## Database

Gunakan snake_case:

```text
registration_number
wilayah_id
cabang_id
status_verifikasi
created_at
```

## Table

Gunakan lowercase snake_case:

```text
pemuda
wilayah
cabang
user_roles
pemuda_skills
```

---

# 19. Migration

Migration harus:

- dapat dijalankan dari database kosong.
- memiliki `up()`.
- memiliki `down()`.
- membuat foreign key.
- membuat index.
- tidak bergantung pada data manual di database.

Urutan migration harus memperhatikan dependency.

Contoh:

```text
001 user_roles
002 wilayah
003 cabang
004 users
005 provinces
006 regencies
007 districts
008 villages
009 education_levels
010 job_statuses
011 skills
012 interests
013 pemuda
014 alamat
015 pendidikan
016 pekerjaan
017 organisasi
018 pemuda_skills
019 pemuda_interests
020 forms
021 questions
022 responses
023 answers
```

Jika migration digabung menjadi satu file, tetap pastikan urutan pembuatan tabel benar.

---

# 20. Seeder

Seeder digunakan untuk data awal/reference.

Minimal:

```text
UserRoleSeeder
WilayahSeeder
EducationLevelSeeder
JobStatusSeeder
SkillSeeder
InterestSeeder
```

Wilayah awal berjumlah 4:

```text
W01
W02
W03
W04
```

Nama wilayah dapat diubah sesuai struktur organisasi sebenarnya.

Jangan membuat password admin default yang permanen pada production.

---

# 21. Error Handling

Jangan menampilkan:

```text
SQL error
stack trace
database credentials
file path
```

kepada user production.

Gunakan halaman/error response yang sesuai.

Untuk development, error detail boleh diaktifkan.

---

# 22. Security Checklist

Sebelum production:

- [ ] `.env` tidak masuk repository.
- [ ] Password menggunakan hashing.
- [ ] CSRF protection aktif untuk form yang sesuai.
- [ ] Session dikonfigurasi dengan aman.
- [ ] Input divalidasi server-side.
- [ ] Output HTML di-escape.
- [ ] Query menggunakan Query Builder/parameter binding.
- [ ] Authorization diterapkan di server.
- [ ] Scope wilayah/cabang diterapkan pada query.
- [ ] Upload file divalidasi tipe dan ukurannya.
- [ ] HTTPS digunakan.
- [ ] Database user production memiliki privilege minimum.
- [ ] Backup database tersedia.

---

# 23. Testing

Minimal test untuk:

### Authentication

- Login valid.
- Login password salah.
- User nonaktif tidak dapat login.
- Logout.

### Authorization

- Superadmin dapat melihat semua data.
- Admin wilayah hanya dapat melihat wilayahnya.
- Admin cabang hanya dapat melihat cabangnya.
- Admin cabang tidak dapat mengakses cabang lain melalui URL/API.

### Pendataan

- Form valid dapat disimpan.
- Form invalid ditolak.
- Transaction rollback jika insert gagal.
- Registration number unik.

### CRUD

- Create.
- Read.
- Update.
- Archive.
- Restore jika fitur tersedia.

---

# 24. Laporan

Laporan harus mengikuti scope user.

Contoh:

```text
Superadmin
    -> semua wilayah

Admin Wilayah
    -> wilayah sendiri

Admin Cabang
    -> cabang sendiri
```

Jangan membuat endpoint export yang mengabaikan authorization.

Contoh buruk:

```text
/admin/export-all
```

yang dapat diakses semua role.

Export harus menggunakan scope yang sama dengan halaman data.

---

# 25. Dashboard

Dashboard menampilkan statistik sesuai scope.

Superadmin:

```text
Total Pemuda
Total Wilayah
Total Cabang
Pemuda per Wilayah
Pemuda per Cabang
Pendidikan
Pekerjaan
Gender
```

Admin Wilayah:

```text
Total Pemuda Wilayah
Total Cabang
Pemuda per Cabang
Pendidikan
Pekerjaan
Gender
```

Admin Cabang:

```text
Total Pemuda Cabang
Pendidikan
Pekerjaan
Gender
Keahlian
Minat
```

---

# 26. API / AJAX

Untuk dependent dropdown alamat:

```text
Provinsi
    ↓
Kabupaten
    ↓
Kecamatan
    ↓
Desa
```

Gunakan endpoint terpisah.

Contoh:

```text
GET /api/regencies/{provinceId}
GET /api/districts/{regencyId}
GET /api/villages/{districtId}
```

Untuk cabang:

```text
GET /api/cabang/by-wilayah/{wilayahId}
```

Semua endpoint tetap harus memiliki validation dan authorization yang sesuai.

---

# 27. Workflow Pengembangan

Urutan pengembangan yang disarankan:

```text
1. Database
   ↓
2. Migration
   ↓
3. Seeder
   ↓
4. Models
   ↓
5. Authentication
   ↓
6. Authorization / Scope
   ↓
7. Admin Dashboard
   ↓
8. CRUD Wilayah
   ↓
9. CRUD Cabang
   ↓
10. CRUD Pemuda
   ↓
11. Form Public
   ↓
12. Verifikasi
   ↓
13. Laporan
   ↓
14. Export
   ↓
15. Form Builder
```

Jangan mengembangkan form builder terlebih dahulu sebelum CRUD data utama dan authorization stabil.

---

# 28. Git Workflow

Gunakan branch berdasarkan fitur:

```text
main
develop
feature/auth
feature/wilayah-cabang
feature/pemuda
feature/form-pendataan
feature/report
```

Commit harus jelas:

```text
feat: add wilayah and cabang management
feat: add youth registration form
fix: restrict admin cabang data scope
refactor: extract pemuda service
```

Jangan commit:

```text
.env
writable/logs/*
database credentials
password
API keys
```

---

# 29. Definition of Done

Sebuah fitur dianggap selesai jika:

- [ ] Database migration tersedia jika diperlukan.
- [ ] Seeder tersedia jika diperlukan.
- [ ] Model tersedia.
- [ ] Validation tersedia.
- [ ] Authorization tersedia.
- [ ] Scope wilayah/cabang diperiksa.
- [ ] UI responsive.
- [ ] Error handling tersedia.
- [ ] Tidak ada data sensitif di log.
- [ ] Test dasar tersedia.
- [ ] Tidak merusak fitur existing.

---

# 30. Aturan Khusus untuk Agent/AI Coding

Saat mengerjakan project ini:

1. **Baca `AGENTS.md` sebelum mengubah kode.**
2. Jangan mengubah arsitektur utama tanpa alasan.
3. Jangan membuat tabel baru jika relasi yang diperlukan sudah dapat ditangani tabel existing.
4. Jangan menambahkan `wilayah_id` ke `pemuda` tanpa alasan kuat; wilayah diturunkan dari `cabang_id`.
5. Jangan bypass authorization.
6. Jangan mengandalkan UI untuk security.
7. Jangan menulis password plaintext.
8. Jangan menggunakan `SELECT *` jika hanya beberapa kolom diperlukan untuk response sensitif.
9. Gunakan transaction untuk operasi multi-tabel.
10. Semua perubahan database harus melalui migration.
11. Gunakan seeders untuk reference data.
12. Jangan menghapus data production secara permanen tanpa mekanisme dan konfirmasi yang sesuai.
13. Pertahankan backward compatibility jika memungkinkan.
14. Jika requirement ambigu dan dapat memengaruhi database/security, jelaskan asumsi sebelum melakukan perubahan besar.
15. Untuk perubahan besar, kerjakan secara bertahap dan pastikan setiap tahap tetap dapat dijalankan.
16. Jangan memasukkan dependency baru jika fitur dapat dibuat menggunakan CodeIgniter 4 atau dependency yang sudah ada.
17. Prioritaskan keamanan, integritas data, dan authorization dibanding kemudahan implementasi sementara.

---

# 31. Target Arsitektur Akhir

```text
                    SISTEM PENDATAAN PEMUDA
                              |
             +----------------+----------------+
             |                                 |
          PUBLIC                            ADMIN
             |                                 |
       Form Pendataan                    Authentication
             |                                 |
             v                                 v
       +-----------+                    +-------------+
       |  PEMUDA   |                    |   RBAC      |
       +-----+-----+                    +------+------+
             |                                 |
             v                         +-------+-------+
        CABANG                        |       |       |
             |                    Superadmin Wilayah Cabang
             v
        WILAYAH
             |
             v
        DATABASE
             |
    +--------+--------+
    |        |        |
 Alamat Pendidikan Pekerjaan
    |
 Organisasi / Skill / Minat
             |
             v
        REPORTING
             |
       +-----+-----+
       |           |
     Excel       Dashboard
```

## Prioritas implementasi

**Phase 1 — Fondasi**

- Migration
- Seeder
- Models
- Authentication
- Role
- Wilayah
- Cabang

**Phase 2 — Pendataan**

- CRUD Pemuda
- Alamat
- Pendidikan
- Pekerjaan
- Organisasi
- Skill
- Minat

**Phase 3 — Public Form**

- Form pendataan
- Validasi
- Transaction
- Nomor registrasi
- Halaman sukses

**Phase 4 — Dashboard**

- Statistik
- Search
- Filter
- Detail
- Verifikasi

**Phase 5 — Reporting**

- Export Excel
- CSV
- Print
- Statistik per wilayah/cabang

**Phase 6 — Form Builder**

- Form dinamis
- Question builder
- Response
- Answer
- Public form URL

---

# 32. Catatan Perubahan & Pembaruan Fitur (Changelog)

Setiap penambahan atau pengurangan fitur wajib dicatat pada bagian ini.

### 2026-08-29 — Penambahan Detail Informasi Cabang & Gelombang Pemuda

- **Penambahan Kolom Database pada Tabel `cabang`:**
  - `alamat` (TEXT): Alamat lengkap atau sekretariat cabang.
  - `pimpinan_nama` (VARCHAR 100): Nama pimpinan cabang.
  - `no_wa` (VARCHAR 20): Nomor WhatsApp atau kontak pimpinan cabang.
  - `has_gelombang` (ENUM 'sudah', 'belum'): Status ketersediaan gelombang pemuda di cabang terkait.
  - `gelombang_hari` (VARCHAR 100): Hari pengajian / kegiatan gelombang pemuda (jika sudah ada).
  - `gelombang_jam` (VARCHAR 50): Jam masuk / waktu pelaksanaan kegiatan (jika sudah ada).
  - `gelombang_ustadz` (VARCHAR 150): Nama ustadz yang mengampu kegiatan pemuda (jika sudah ada).
- **Migration & Model:**
  - Dibuat migration `2026-08-29-185000_AddDetailsToCabang.php` beserta index `idx_cabang_has_gelombang`.
  - Diperbarui `CabangModel.php` whitelist `$allowedFields`.
- **Controller & UI Cabang:**
  - Ditambahkan filter status gelombang pemuda (`sudah` / `belum`) dan pencarian pimpinan/ustadz/alamat di `Admin\Cabang::index`.
  - Ditambahkan endpoint `Admin\Cabang::detail($id)` untuk AJAX detail modal.
  - Diperbarui UI `app/Views/admin/cabang/index.php`:
    - Ringkasan statistik (Total Cabang, Sudah Ada Gelombang, Belum Ada Gelombang).
    - Tampilan tabel dengan informasi pimpinan, link WhatsApp langsung, status gelombang beserta jadwal/ustadz, dan alamat.
    - Modal Tambah & Edit Cabang dengan toggle interaktif untuk detail jadwal dan ustadz pengampu gelombang.
    - Modal Detail Cabang interaktif untuk melihat informasi lengkap cabang dalam satu klik.

### 2026-08-30 — Audit & Penguatan\*

- Dilengkapi otomatisasi sinkronisasi token CSRF pada form public (`public/js/pendataan.js`) saat verifikasi duplikasi berlangsung.
- Ditambahkan meta tag CSRF dan konfigurasi `$.ajaxSetup` global pada template admin (`app/Views/admin/layou Keamanan Sistem (Security Hardening)
- **Pengaktifan Global Security Filters:**
  - `csrf`: Proteksi Cross-Site Request Forgery diaktifkan secara global di `app/Config/Filters.php`.
  - `secureheaders`: Header keamanan HTTP (`X-Frame-Options`, `X-Content-Type-Options: nosniff`, `Referrer-Policy`) diaktifkan di global after filter.
  - `invalidchars`: Filter pembersih karakter kontrol berbahaya diaktifkan di global before filter.
- \**CSRF Token Synchronization & AJAX:*ts/main.php`).
- **Pencegahan Brute-Force & DoS (Rate Limiting / Throttling):**
  - Ditambahkan throttler pada `Auth::login` (maksimal 5 percobaan per menit per IP).
  - Ditambahkan throttler pada `Pendataan::simpan` (maksimal 10 pendaftaran per menit per IP) dan `Pendataan::checkDuplicate` (maksimal 30 pengecekan per menit per IP).
- **Pengamanan Manajemen Pengguna (Users & Roles):**
  - Validasi wajib scope wilayah untuk `admin_wilayah` dan scope cabang untuk `admin_cabang`.
  - Proteksi anti self-lockout: Admin yang sedang login tidak dapat menurunkan role atau menonaktifkan akunnya sendiri.
  - Proteksi penghapusan Superadmin terakhir: Mencegah sistem kehilangan seluruh akun Superadmin aktif.
- **Integritas Relasional Penghapusan Data:**
  - Ditambahkan validasi cek akun admin terkait sebelum menghapus data wilayah atau cabang.
- **Pencegahan CSV / Spreadsheet Formula Injection (CWE-1236):**
  - Ditambahkan helper `sanitizeCsvField()` pada `app/Common.php` dan diimplementasikan pada `Admin\Pemuda::export()` untuk menetralkan karakter formula (`=`, `+`, `-`, `@`, `\t`, `\r`).
- **Pengamanan Upload File Spreadsheet:**
  - Ditambahkan validasi MIME type (`mime_in`) pada `Admin\Pemuda::prosesImport()`.
- **Penguatan Session & Cookie:**
  - Diaktifkan `$regenerateDestroy = true` di `app/Config/Session.php` untuk mencegah session fixation.
  - Dikonfigurasi `$appTimezone = 'Asia/Jakarta'` di `app/Config/App.php`.

### 2026-08-31 — Integrasi & Sinkronisasi Database Warga MTA API (v1) — Khusus Perwakilan Sragen

- **Konfigurasi & Scope Wilayah:**
  - Dibuat `app/Config/MtaApi.php` dan ditambahkan konfigurasi environment (`MTA_API_BASE_URL`, `MTA_API_TOKEN`, `MTA_API_TIMEOUT`, `MTA_API_ENABLED`, `MTA_PERWAKILAN_UUID`, `MTA_PERWAKILAN_NAMA`) di `.env` dan `env`.
  - **Penguncian Scope Data:** Seluruh proses pencarian warga, pengambilan daftar cabang, dan sinkronisasi data dikunci secara ketat hanya untuk **Perwakilan Sragen** (Kode: `86`, UUID: `3246792b-f0a7-48ca-95fa-379e3bee777d`).
- **Service Layer:**
  - `app/Services/MtaApiService.php`: Ditambahkan helper `getSragenUuid()`, `getPerwakilanSragenDetail()`, `getCabangSragenList()`, `getCabangWarga()`, dan default filter Perwakilan Sragen pada `searchWarga()` dan `getWargaList()`.
  - `app/Services/MtaSyncService.php`:
    - Sinkronisasi cabang otomatis diarahkan ke 65+ cabang Perwakilan Sragen.
    - Ditambahkan `verifyYouthAgainstMta(array $inputData)`: Pengecekan otomatis apakah pemuda yang diinput sudah ada di Database Warga MTA Pusat. Jika ada -> status `verified`, jika tidak ada -> status `pending`.
    - Ditambahkan `syncAndVerifyAllPemudaSragen(?int $cabangId, bool $onlyPending)`: Fitur verifikasi dan sinkronisasi massal seluruh data pemuda terdaftar di PMD Sragen terhadap Database Warga MTA Pusat.
- **Database & Migration:**
  - Dibuat migration `2026-08-31-220000_AddMtaSyncFields.php`:
    - Tabel `wilayah`: penambahan kolom `mta_uuid`, `mta_code` (indexed).
    - Tabel `cabang`: penambahan kolom `mta_uuid`, `mta_last_synced_at` (indexed).
    - Tabel `pemuda`: penambahan kolom `mta_warga_uuid`, `mta_status_warga`, `mta_ayah_uuid`, `mta_ibu_uuid`, `mta_foto_url`, `mta_synced_at` (indexed).
    - Tabel baru `mta_sync_logs` untuk audit trail riwayat sinkronisasi.
  - Diperbarui `$allowedFields` pada `WilayahModel.php`, `CabangModel.php`, dan `PemudaModel.php`.
  - Dibuat model `MtaSyncLogModel.php`.
- **Form Public & Controller:**
  - `app/Controllers/Pendataan.php`: Pada saat pemuda mendaftar mandiri via form publik (`simpan()`), sistem langsung memverifikasi otomatis ke API MTA Pusat. Jika ditemukan di server MTA, status registrasi langsung menjadi `verified`, jika tidak ditemukan berstatus `pending`.
  - `app/Views/pendataan/sukses.php`: Badge status dinamis ("Terverifikasi Otomatis (Tercatat di MTA Pusat)" vs "Menunggu Verifikasi Admin").
  - `app/Controllers/Admin/MtaSync.php`: Ditambahkan endpoint `POST admin/mta-sync/sync-verify-all` untuk pemindaian dan verifikasi massal seluruh pemuda.
- **User Interface & UX Admin Panel:**
  - `app/Views/admin/mta_sync/index.php`: Ditambahkan card fitur & modal "Sinkronisasi & Verifikasi Otomatis Pemuda Sragen".
  - `app/Views/admin/pemuda/index.php`: Ditambahkan shortcut tombol "Sinkron & Verifikasi MTA".

### 2026-08-31 — Penyederhanaan & Prioritas Data Import Excel Pemuda

- **Prioritas 5 Data Inti Wajib:**
  - Fitur Import Excel difokuskan pada 5 kolom data utama yang esensial:
    1. `name` (Nama Lengkap)
    2. `cabang` (Nama / Kode Cabang)
    3. `gender` (Jenis Kelamin: `L` / `P`)
    4. `marital_status` (Status Pernikahan: `belum_menikah`, `sudah_menikah`, `janda`, `duda`)
    5. `birth_date` (Tanggal Lahir: `YYYY-MM-DD` / `DD/MM/YYYY`)
- **Data Pelengkap Bersifat Opsional & Menyusul:**
  - `phone` (Nomor Telepon/WA), tempat lahir, email, golongan darah, alamat lengkap, jenjang pendidikan, pekerjaan, organisasi, keahlian, dan minat dijadikan opsional (nullable/fallback otomatis) sehingga tidak memblokir proses import jika belum terisi.
- **Template Excel & UI Panduan:**
  - Template `Template_Import_Pemuda_MTA_Sragen.xlsx` diperbarui dengan visualisasi header hijau untuk 5 kolom utama wajib dan warna netral untuk kolom pelengkap yang bisa menyusul.
  - Halaman `app/Views/admin/pemuda/import.php` diperbarui dengan panduan prioritas data yang jelas.

### 2026-09-01 — Fitur Pengecekan Data Pemuda & Pelengkapan Data Otomatis pada Form Pendataan

- **Pengecekan Data Pemuda (Nama, Jenis Kelamin, Tanggal Lahir, dan Cabang):**
  - Ditambahkan method `findExistingPemuda($name, $gender, $birthDate, $cabangId, $excludeId)` pada `PemudaModel.php` untuk mencocokkan data pemuda secara akurat.
  - Ditambahkan endpoint AJAX `POST /pendataan/check-data` (serta alias legacy `POST /pendataan/check-duplicate`) pada `Pendataan::checkData`.
  - Jika data **sudah terdaftar** di cabang terkait:
    - Mengembalikan `status: 'found'` beserta data lengkap pemuda (identitas pribadi, alamat, pendidikan, pekerjaan, organisasi, keahlian, dan minat).
    - Form secara otomatis dimuat dan diisikan dengan data yang ada di database.
    - Menampilkan notifikasi visual interaktif mode "Melengkapi & Memperbarui Data Terdaftar" dengan No. Registrasi.
    - User dapat langsung melanjutkan ke langkah berikutnya untuk melengkapi atau memperbarui kolom yang belum terisi.
  - Jika data **belum terdaftar**:
    - Mengembalikan `status: 'not_found'`.
    - Menampilkan feedback informatif "Data Belum Terdaftar" dan mengizinkan user melanjutkan pengisian form pendataan baru sampai langkah konfirmasi selesai.
- **Pembaruan Alur Penyimpanan (`Pendataan::simpan`):**
  - Mendukung penyelesaian/pembaruan data terdaftar (`isUpdate = true`) dengan operasi upsert pada `alamat`, `pendidikan`, `pekerjaan`, serta sinkronisasi ulang `organisasi`, `skills`, dan `interests` tanpa memicu penolakan duplikasi.
  - Pembuatan data baru tetap meng-generate nomor registrasi unik `PMD-YYYYMMDD-XXXX`.
- **UI/UX Form Pendataan Publik (`app/Views/pendataan/form.php` & `public/js/pendataan.js`):**
  - Penataan 4 parameter verifikasi utama (Cabang, Nama Lengkap, Jenis Kelamin, Tanggal Lahir) di bagian atas Step 1.
  - Tombol aksi interaktif "Cek Data Pemuda" beserta indikator spinner dan kontainer feedback dinamis.
  - Integrasi otomatis saat klik "Selanjutnya: Alamat" jika pengecekan data belum dijalankan secara manual.
  - Adaptasi dinamis tombol konfirmasi dan halaman sukses (`app/Views/pendataan/sukses.php`).

### 2026-09-01 — Standarisasi Penyimpanan Data Pemuda dalam Format Huruf Kecil (Lowercase)

- **Format Lowercase pada Database:**
  - Seluruh data teks pemuda (nama lengkap, tempat lahir, email, golongan darah, status pernikahan, dusun, RT, RW, alamat detail, nama sekolah/kampus, jurusan, status pendidikan, profesi/jabatan, nama perusahaan/usaha, bidang usaha, nama organisasi, posisi/jabatan, deskripsi) distandarisasi untuk disimpan dalam format **lowercase** (huruf kecil) menggunakan UTF-8 `mb_strtolower()`.
- **Implementasi Multi-Layer:**
  1. **Helper & Form Public/Admin:** Helper `toLowerTrim()` di `app/Common.php` digunakan pada `Pendataan::simpan` dan `Admin\Pemuda::simpan` serta `Admin\Pemuda::update`.
  2. **Model Callbacks (`beforeInsert` & `beforeUpdate`):** Diterapkan otomatis pada `PemudaModel`, `AlamatModel`, `PendidikanModel`, `PekerjaanModel`, dan `OrganisasiModel` sehingga seluruh penyimpanan data dijamin konsisten berformat lowercase.
  3. **Import Spreadsheet (`PemudaImportService`):** Seluruh data hasil parsing file Excel/CSV otomatis dinormalisasi ke format lowercase sebelum disimpan ke database.

### 2026-09-01 — Penambahan Menu Warga MTA pada Superadmin (Data Warga Sragen dari api.mta.or.id)

- **Menu Navigasi Sidebar Superadmin:**
  - Ditambahkan menu **Warga MTA** pada navigasi sidebar (`app/Views/admin/layouts/main.php`) di bawah Menu Utama khusus bagi user dengan role `superadmin`, dilengkapi ikon kartu identitas (`fas fa-id-card text-success`) dan label badge `Sragen`.
- **Routing & Controller:**
  - Didaftarkan route group `admin/warga-mta` dengan proteksi filter `auth` dan `role:superadmin` pada `app/Config/Routes.php`.
  - Dibuat controller `App\Controllers\Admin\WargaMta.php` yang berinteraksi langsung dengan API Pusat `api.mta.or.id`:
    - `index()`: Mengambil daftar warga MTA khusus Perwakilan Sragen (Kode `86`, UUID: `3246792b-f0a7-48ca-95fa-379e3bee777d`). Menyediakan pagination terintegrasi, pencarian cepat (nama/no. HP/alamat), filter 70 Cabang MTA di Sragen, filter jenis kelamin (Putra/Putri), dan filter status PMD lokal.
    - Cross-referencing otomatis dengan database PMD Sragen lokal untuk mendeteksi apakah warga MTA tersebut sudah tercatat sebagai pemuda atau belum.
    - `detail($uuid)`: Mengambil profil lengkap warga MTA (foto, identitas, kontak, orang tua, pernikahan, pekerjaan, dan domisili) dari API MTA baik melalui AJAX Modal interaktif maupun halaman detail mandiri.
    - `import()`: Mendaftarkan/mengimpor warga MTA terpilih menjadi pemuda PMD Sragen ke cabang lokal tujuan secara instan, lengkap dengan alamat, pekerjaan, dan verifikasi otomatis (`verified`).
- **User Interface & UX:**
  - Dibuat view `app/Views/admin/warga_mta/index.php` dan `app/Views/admin/warga_mta/detail.php`.
  - Dilengkapi widget statistik ringkas (Total Warga MTA Sragen, Total Cabang MTA di Sragen, Warga Tersinkron PMD, Status Sumber API).
  - Tampilan tabel responsif dengan badge status PMD, tautan WhatsApp instan, modal detail AJAX, dan modal impor ke PMD dengan auto-match cabang lokal.
- **Testing & Validasi:**
  - Dibuat unit test `tests/unit/WargaMtaTest.php` untuk memverifikasi controller, endpoint routes, ketersediaan view, keberadaan menu sidebar, dan scope API Sragen. Seluruh 39 unit test berjalan sukses 100%.

### 2026-09-01 — Autocomplete Pencarian Warga MTA pada Form Pendataan Publik Berdasarkan Cabang Terpilih

- **Alur & Interaksi Pengguna (User Flow):**
  - Pada formulir pendataan publik (`/pendataan`), setelah pengguna memilih **Cabang Pemuda MTA**, kolom **Nama Lengkap** mengaktifkan pencarian live autocomplete yang terhubung langsung ke API MTA Pusat (`api.mta.or.id/api/v1/warga/search`) dengan filter cabang lokal yang dipilih (`mta_uuid`).
  - Saat pengguna mengetikkan huruf/nama (minimal 2 karakter) dengan mekanisme debouncing (300ms), muncul dropdown interaktif yang menampilkan daftar nama warga MTA di cabang tersebut beserta nomor warga, jenis kelamin (Putra/Putri), usia, alamat, serta penanda status apakah sudah terdaftar di sistem PMD lokal atau belum.
  - Pengguna dapat mengeklik salah satu warga yang sesuai dari daftar saran.
- **Auto-Populate Data Formulir:**
  - Begitu warga dipilih, sistem memanggil endpoint detail dan otomatis mengisikan data ke formulir:
    - Nama Lengkap (`name`)
    - Jenis Kelamin (`gender`: L / P)
    - Tanggal Lahir (`birth_date`)
    - Tempat Lahir (`birth_place`)
    - Nomor HP / WhatsApp (`phone`)
    - Status Pernikahan (`marital_status`)
    - Golongan Darah (`blood_type`)
    - Alamat Lengkap (`address_detail`), Dusun (`dusun`), RT (`rt`), RW (`rw`)
    - Pencocokan otomatis Kecamatan (`district_id`) dan Desa (`village_id`) di Sragen
    - Identitas keterhubungan UUID Warga MTA (`mta_warga_uuid`)
  - Jika warga tersebut sudah pernah terdaftar di PMD Sragen, sistem otomatis memuat data profil lengkapnya (pendidikan, pekerjaan, organisasi, keahlian, minat) dalam mode "Melengkapi & Memperbarui Data Terdaftar".
  - Jika belum terdaftar di PMD Sragen, ditampilkan banner notifikasi hijau bahwa data warga MTA berhasil dimuat dan pengguna tinggal melengkapi langkah data berikutnya (Pendidikan, Pekerjaan, Organisasi, Keahlian, Minat) sampai selesai.
  - Pengguna tetap memiliki fleksibilitas untuk membatalkan pilihan atau melanjutkan pendaftaran baru secara mandiri jika nama yang diketik tidak terdaftar di data warga MTA cabang tersebut.
- **Backend & Endpoint:**
  - Ditambahkan endpoint publik:
    - `GET /pendataan/search-warga`: menerima parameter `cabang_id` dan `q`, memetakan UUID cabang ke API MTA, serta melakukan cross-check dengan data pemuda lokal.
    - `GET /pendataan/warga-detail/(:segment)`: mengambil detail warga MTA berdasarkan UUID dan memformatnya sesuai kebutuhan field formulir pendaftaran.
  - Ditambahkan proteksi rate limiting/throttling pada endpoint AJAX pencarian.
- **Testing & Validasi:**
  - Dibuat unit test `tests/unit/WargaMtaAutocompleteTest.php` untuk memverifikasi metode controller, definisi rute publik, komponen UI view, dan ketersediaan fungsi JavaScript.
  - Seluruh 43 unit test proyek lulus 100% tanpa error.

### 2026-09-01 — Portal Pengaturan Konten Beranda (Homepage) Khusus Superadmin

- **Database Migration & Seeder:**
  - Dibuat migration `app/Database/Migrations/2026-09-01-231500_CreateHomepageSettingsTable.php` untuk tabel `homepage_settings` dengan kolom:
    - `id` (INT UNSIGNED AUTO_INCREMENT PRIMARY KEY)
    - `group` (VARCHAR 50, indexed)
    - `key` (VARCHAR 100, UNIQUE)
    - `value` (LONGTEXT, nullable)
    - `type` (ENUM 'text', 'textarea', 'json', 'number', 'boolean', 'image')
    - `label` (VARCHAR 255)
    - `created_at` & `updated_at` (DATETIME)
  - Dibuat model `App\Models\HomepageSettingModel` yang memuat konfigurasi nilai bawaan (`getDefaults()`), getter/setter dinamis (`getAllSettings()`, `getSetting()`, `setSetting()`), dan fungsi pemulihan (`resetToDefaults()`).
  - Dibuat seeder `App\Database\Seeds\HomepageSettingSeeder` yang didaftarkan ke `DatabaseSeeder.php` untuk menginisialisasi 41 item pengaturan bawaan landing page.
- **Controller & Authorization Superadmin:**
  - Dibuat controller `App\Controllers\Admin\HomepageSetting.php` dengan pembatasan hak akses strictly khusus role `superadmin` via route filter `role:superadmin` dan method guard `ensureSuperadmin()`.
  - Metode `index()`: Membaca seluruh pengaturan dari database, mendekode format JSON (highlight chips, 4 misi strategis, 6 divisi program kerja, 4 langkah alur pendataan, dan daftar tanya jawab FAQ) untuk dimuat ke dalam tab portal.
  - Metode `update()`: Memvalidasi dan menyimpan perubahan teks, textarea, serta struktur array/JSON secara terpadu, dilengkapi token CSRF dan flash notification.
  - Metode `reset()`: Mengembalikan seluruh konten halaman muka ke setelan bawaan sistem.
- **Tampilan Portal Pengaturan AdminLTE 3:**
  - Dibuat view `app/Views/admin/homepage/index.php` yang terstruktur dalam 7 tab navigasi tematik:
    1. **Header & Hero Banner:** Badge pill, Judul Hero, Subjudul, Teks Tombol Pendaftaran, Kartu Samping (Keuntungan/Manfaat), Angka Counter Bidang Pengabdian, dan Highlight Chips interaktif (bisa tambah/hapus baris).
    2. **Tentang & Visi Misi:** Tag Section, Judul Profil, Paragraf 1 & 2, Teks Visi Organisasi, dan 4 Pilar Misi Strategis.
    3. **Struktur Wilayah:** Tag Section, Judul Wilayah, dan Deskripsi Pengantar Wilayah & Cabang.
    4. **Bidang & Program Kerja:** Tag Section, Judul Program, Deskripsi Pengantar, serta Kartu Program Kerja (ikon, warna tema, judul, deskripsi, dan jadwal/badge) dengan tombol tambah/hapus kartu.
    5. **Alur & Banner CTA:** Tag Section, Judul Alur, 4 Tahapan Langkah Pengisian Form, dan Banner Ajakan Besar (CTA strip).
    6. **Tanya Jawab (FAQ):** Tag Section, Judul FAQ, Deskripsi Pengantar, serta accordion Q&A dengan tombol tambah/hapus pertanyaan baru.
    7. **Kontak & Sekretariat:** Alamat Fisik Kantor Sekretariat, Nomor WhatsApp Helpdesk, dan Label Keterangan Layanan.
  - Ditambahkan menu navigasi baru **Kelola Homepage** (`fas fa-desktop text-warning`) pada sidebar superadmin (`app/Views/admin/layouts/main.php`).
- **Penerapan Dinamis pada Halaman Depan Publik:**
  - Diperbarui `App\Controllers\Home::index()` untuk memuat data pengaturan homepage dan mengirimkannya ke view.
  - Diperbarui `app/Views/landing.php` agar setiap bagian teks, badge, kartu, misi, program, FAQ, dan link WhatsApp mengambil data dari database secara dinamis dengan fallback nilai default yang aman.
- **Testing & Validasi:**
  - Dibuat unit test suite `tests/unit/HomepageSettingTest.php` (6 test, 40 assertions) untuk menguji model, controller, route definitions, ketersediaan menu sidebar, integritas view portal, dan binding pada landing page.
  - Seluruh 49 unit test proyek lulus 100%.
  - Diverifikasi secara langsung via HTTP curl ke server lokal, pengujian update konten secara live, dan pengujian reset ke default.

### 2026-09-01 — Penyesuaian Kode Cabang Mengikuti Data Resmi API Pusat (api.mta.or.id)

- **Standarisasi Format Kode Cabang:**
  - Menyelaraskan seluruh kode cabang pemuda di database dari format lama (`CBG-xxx`) ke format resmi API Pusat MTA (`86.0`, `86.1`, `86.2`, ..., `86.69`) di mana `86` merupakan kode Perwakilan MTA Sragen dan digit di belakang titik adalah nomor cabang resmi di sistem pusat.
- **Database Migration:**
  - Dibuat migration `app/Database/Migrations/2026-09-01-235000_UpdateCabangCodeFromMtaApi.php` untuk memperbarui seluruh 70 cabang pemuda di tabel `cabang` dengan kode resmi dan UUID dari API Pusat MTA (`api.mta.or.id`).
  - Migration berhasil dieksekusi, sehingga 100% data cabang di database lokal kini menggunakan format kode resmi pusat.
- **Sinkronisasi Database Cabang (`MtaSyncService`):**
  - Diperbarui metode `MtaSyncService::syncCabang()` pada `app/Services/MtaSyncService.php` agar setiap kali proses sinkronisasi cabang dijalankan dari admin panel (`/admin/mta-sync`), kode cabang lokal otomatis disinkronkan dengan data terbaru dari API pusat.
- **Pembaruan Seeder (`CabangSeeder`):**
  - Diperbarui `app/Database/Seeds/CabangSeeder.php` untuk mencakup seluruh 70 cabang lengkap dengan kode resmi `86.x`, pemetaan wilayah 1-4, deskripsi, dan `mta_uuid`.
- **Integrasi Import & Form Input:**
  - Diperbarui `app/Services/PemudaImportService.php` (panduan template, fallback lookups, dan pemetaan cabang) untuk mendukung dan mereferensikan kode `86.x`.
  - Diperbarui `app/Views/admin/cabang/index.php` (modal tambah & edit cabang: placeholder `Contoh: 86.1`).
  - Diperbarui `app/Views/admin/pemuda/import.php` (keterangan format kode cabang).
  - Diperbarui `app/Views/pendataan/form.php` dan `app/Views/admin/pemuda/form.php` agar dropdown pilihan cabang menampilkan kode cabang resmi pusat (contoh: `[86.1] Gemolong 1 (Wilayah 2)`).
- **Testing & Validasi:**
  - Dibuat unit test suite `tests/unit/CabangApiCodeTest.php` (4 test, 16 assertions) untuk menguji format kode seeder, verifikasi database lokal, pemetaan lookups import pemuda, dan view placeholder.
  - Seluruh 53 unit test proyek lulus 100%.

### 2026-09-02 — Pembaruan Format Nomor Registrasi Pemuda (IdPerwakilanIdCabangtanggallahirRandomNomor)

- **Standarisasi Format Nomor Registrasi Pemuda:**
  - Format nomor registrasi diubah dari format lama (`PMD-YYYYMMDD-XXXX`) menjadi format terstruktur 16 digit: `IdPerwakilanIdCabangtanggallahirRandomNomor`.
  - **Struktur Komponen (16 Digit, Tanpa Pemisah):**
    - `IdPerwakilan` (2 digit): Kode Perwakilan MTA Sragen (`86`).
    - `IdCabang` (2 digit): Nomor/kode cabang resmi MTA dengan padding 2 digit (contoh: `86.1` Gemolong 1 -> `01`, `86.6` Gesi -> `06`, `86.10` Jenar -> `10`, `86.42` Sambungmacan 2 -> `42`, `86.0` Sragen Perwakilan -> `00`).
    - `tanggallahir` (8 digit): Tanggal lahir pemuda format `YYYYMMDD` (contoh: `20000517` untuk 17 Mei 2000).
    - `RandomNomor` (4 digit): Angka acak 4 digit unik (`0001` - `9999`) yang diverifikasi keunikannya secara otomatis di database.
  - **Contoh:** Pemuda lahir 17 Mei 2000 di Cabang Gemolong 1 (Kode 86.1) mendapatkan No. Registrasi: `8601200005178234`.
- **Implementasi Model & Controller:**
  - Diperbarui `PemudaModel::generateRegistrationNumber(?int $cabangId = null, ?string $birthDate = null)` di `app/Models/PemudaModel.php`.
  - Diperbarui pemanggilan di `app/Controllers/Pendataan.php` (pendaftaran mandiri).
  - Diperbarui pemanggilan di `app/Controllers/Admin/Pemuda.php` (tambah pemuda oleh admin).
  - Diperbarui pemanggilan di `app/Services/MtaSyncService.php` (sinkronisasi dari API MTA).
  - Diperbarui pemanggilan di `app/Services/PemudaImportService.php` (import massal Excel).
  - Diperbarui fallback tampilan di `app/Views/pendataan/sukses.php` dan contoh teks di `HomepageSettingModel.php`.
- **Testing & Validasi:**
  - Diperbarui `tests/unit/PemudaManagementTest.php` untuk menguji struktur format 16 digit, kecocokan kode cabang & tanggal lahir, serta format default.
  - Seluruh 53 unit test di `tests/unit/` lulus 100%.

### 2026-09-02 — Penegakan Otomatisasi Status Verifikasi (2 Status Berdasarkan Sinkronisasi MTA Pusat)

- **Kebijakan & Ketentuan Status Verifikasi:**
  - Status verifikasi dipangkas menjadi **hanya 2 status**:
    1. **`verified` (Terverifikasi)**: jika data pemuda tersinkronisasi / cocok dengan Database Warga MTA Pusat (`api.mta.or.id`).
    2. **`pending` (Belum Terverifikasi)**: jika data pemuda belum tersinkronisasi / tidak ditemukan di MTA Pusat.
  - Status `rejected` (Ditolak) ditiadakan.
  - **Larangan Modifikasi Manual:** Status verifikasi tidak dapat diubah atau dimanipulasi secara manual oleh siapapun, baik Superadmin, Admin Wilayah, maupun Admin Cabang.
- **Implementasi Backend & Controller:**
  - `Admin\Pemuda::save()` & `Admin\Pemuda::update()`: Menghapus input manual `status_verifikasi` dari formulir. Status ditentukan secara otomatis melalui panggilan `MtaSyncService::verifyYouthAgainstMta()`.
  - `Admin\Pemuda::verifikasi($id)`: Diubah dari endpoint toggle status manual menjadi aksi pemeriksaan & sinkronisasi live terhadap API MTA Pusat.
  - `Pendataan::simpan()`: Status verifikasi pendaftar mandiri secara ketat mengikuti hasil pencocokan API MTA Pusat.
  - `PemudaImportService`: Seluruh pemuda hasil impor spreadsheet di-set default `pending` (Belum Terverifikasi) sampai disinkronkan dengan API MTA.
  - `PemudaModel::getCountsSummary()`: Ringkasan statistik hanya menghitung `verified` dan `pending`.
- **Implementasi Antarmuka (UI/UX):**
  - `app/Views/admin/pemuda/form.php`: Dropdown pilihan verifikasi dihapus dan diganti dengan informasi status read-only (badge + indikator sinkronisasi pusat).
  - `app/Views/admin/pemuda/index.php`: Dropdown toggle manual pada baris tabel diganti dengan badge status informatif. Tab filter "Ditolak" dihapus. Ditambahkan opsi "Sinkronkan MTA" pada dropdown aksi baris.
  - `app/Views/admin/pemuda/detail.php`: Dropdown ubah status verifikasi dihapus.
  - `app/Views/admin/dashboard/index.php`: Kotak statistik utama disederhanakan menjadi 3 card: Total Pemuda, Terverifikasi (Sinkron Pusat), dan Belum Terverifikasi.
  - `app/Views/admin/pemuda/cetak.php`: Format cetak menampilkan status "TERVERIFIKASI (SINKRON PUSAT)" atau "BELUM TERVERIFIKASI".
  - `app/Views/pendataan/sukses.php`: Keterangan status sukses pendaftaran diperjelas menjadi Terverifikasi Otomatis vs Belum Terverifikasi.
- **Pengujian:**
  - Dibuat unit test suite `tests/unit/VerificationPolicyTest.php` (3 test, 7 assertions).
  - Seluruh 56 unit test proyek lulus 100%.

### 2026-09-02 — Penggabungan Fitur Check Data dengan Search Warga & Autocomplete Terpadu

- **Pencarian Terpadu (Unified Search & Check Data):**
  - Menggabungkan fitur pemeriksaan data pemuda dengan pencarian warga MTA saat mengetik nama pada formulir pendataan publik.
  - Endpoint `Pendataan::searchWarga` diperbarui untuk mencari secara simultan di dua sumber data:
    1. **Database MTA Pusat** (via `MtaApiService::searchWarga` berdasarkan cabang terpilih).
    2. **Database Lokal Pemuda PMD** (tabel `pemuda` dan `alamat` untuk cabang terpilih).
  - Hasil pencarian digabungkan (merge) dan dideduplikasi secara cerdas:
    - `both`: Warga tercatat di MTA Pusat dan sudah terdaftar di PMD Lokal (badge *Terdaftar di PMD* & *Terhubung MTA*).
    - `pmd`: Pemuda terdaftar di database PMD lokal cabang terkait (badge *Terdaftar di PMD (Lokal)*).
    - `mta`: Warga tercatat di MTA Pusat namun belum terdaftar di PMD (badge *Warga MTA Pusat (Belum Terdaftar PMD)*).
- **Penanganan Pemuda Terdaftar & Endpoint Detail Lokal:**
  - Ditambahkan endpoint `GET /pendataan/pemuda-detail/(:num)` pada `Pendataan::pemudaDetail($id)` untuk memuat data lengkap pemuda lokal (identitas, alamat, pendidikan, pekerjaan, organisasi, keahlian, dan minat) secara instan.
  - Ketika memilih hasil pemuda yang sudah terdaftar di PMD, formulir otomatis terisi penuh dan mode formulir berganti ke "Mode Melengkapi & Memperbarui Data Terdaftar" dengan nomor registrasi yang bersangkutan.
- **Dukungan Pendaftaran / Input Data Baru:**
  - Jika nama belum ditemukan di database MTA Pusat maupun PMD Lokal:
    - Dropdown menampilkan kartu interaktif: *"Nama [nama] Belum Ada di MTA Pusat maupun PMD Cabang... Silakan lanjutkan untuk mendaftar sebagai pemuda baru"*.
    - Tombol aksi `[+ Input Data Baru dengan Nama Ini]` (`selectNewPemudaInput()`) mengaktifkan formulir baru, membersihkan ID terkait, menampilkan konfirmasi Mode Pendaftaran Baru, dan mengarahkan fokus user ke input berikutnya.
  - Jika hasil pencarian ada tetapi nama pendaftar berbeda, disediakan opsi di bagian bawah dropdown: `[+ Nama Tidak Tercantum? Input Data Baru]`.
- **UI/UX, Cache-Busting & Bugfix Data Pemuda:**
  - Penyederhanaan antarmuka Step 1 `app/Views/pendataan/form.php`: kotak menu "Fitur Pengecekan Data Terpadu" dihapus dari form sehingga antarmuka lebih bersih dan terfokus pada pencarian otomatis saat mengetik nama. Kontainer feedback dinamis `#check-data-result-wrapper` tetap dipertahankan untuk notifikasi mode data.
  - Ditambahkan cache-busting `?v=filemtime` pada pemanggilan `js/pendataan.js` di `app/Views/pendataan/form.php` untuk mencegah browser menggunakan cache berkas JS lama.
  - **Perbaikan Bug Pemilihan Data Pemuda Lokal:**
    - Pada `Pendataan::wargaDetail($uuid)`, ditambahkan pengecekan prioritas ke database lokal pemuda (`findByMtaWargaUuid`, pencarian ID numerik, dan `registration_number`). Jika ditemukan di database pemuda lokal, sistem langsung mengembalikan data profil lengkap pemuda tanpa perlu memanggil API MTA Pusat.
    - Pada `Pendataan::searchWarga`, pemetaan UUID untuk data lokal pemuda dijamin valid (menggunakan `mta_warga_uuid` atau ID pemuda lokal sebagai fallback) agar tidak terjadi pencarian `null` ke MTA Pusat.
    - Pada `public/js/pendataan.js`, fungsi pemilihan item dropdown diubah menjadi `selectSuggestionByIndex(idx)` yang membaca objek data langsung dari array tanpa risiko kesalahan parsing parameter atau event bubbling button, serta dilengkapi fallback multi-layer ke `selectLocalPemuda` dan `selectWargaMta`.
  - Diperbarui suite pengujian `tests/unit/WargaMtaAutocompleteTest.php` untuk memvalidasi method baru, rute baru, elemen view, fungsi JavaScript, serta penanganan validasi.
