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
    pending
    verified
    rejected
```

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
