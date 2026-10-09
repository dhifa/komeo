# KOMEO.ID - Platform Komunitas Industri Event Indonesia

> **"Satu Komunitas, Ribuan Peluang Kolaborasi."**

KOMEO.ID adalah platform digital resmi ekosistem komunitas industri event di Indonesia, menghubungkan:
- Event Organizers (EO) & Wedding Organizers (WO)
- Vendor Event Teknis & Multimedia (Audio, Lighting, LED Screen, Rigging, Genset)
- Fotografer, Videografer, & Operator Live Streaming
- Talenta & Pengisi Acara (MC, Musisi, Band, DJ, Dancer)
- Penyedia F&B / Katering Acara
- Freelancer & Kru Lapangan Profesional

---

## 🛠️ Stack Teknologi

- **Backend:** PHP 8.2+
- **Framework:** CodeIgniter 4 (v4.7.x)
- **Autentikasi & Otorisasi:** CodeIgniter Shield (v1.4.x)
- **Database:** MySQL 8 / MariaDB 10.4+
- **Frontend / Styling:** Tailwind CSS & Lucide Icons (Responsive UI, Mobile-First)
- **Manajemen Dependensi:** Composer

---

## 🌟 Fitur Unggulan Platform

1. **Keanggotaan & Aktivasi Bertahap (Phase 3 & Phase 5.6):**
   - Registrasi mandiri (Individu / Freelancer & Vendor / Bisnis).
   - Alur verifikasi email terisolasi dari aktivasi keanggotaan.
   - Pendaftaran berstatus `pending` hingga kontak & konfirmasi admin WhatsApp/Email disetujui.
   - Penomoran KTA otomatis unik berurutan (`KMO-YYYY-XXXXXX`).
2. **Profil Anggota & Direktori Komunitas (Phase 2 & Phase 5):**
   - Pengaturan foto profil, logo perusahaan, keahlian industri, kota/provinsi, dan galeri portofolio.
   - Direktori publik responsif dengan filter tipe member, kategori, kota, dan status sorotan (*Featured*).
3. **Verifikasi Identitas & Legalitas Usaha (Phase 6):**
   - **Individu / Freelancer:** Verifikasi KTP + Foto Selfie dengan centang biru resmi (*Identitas diverifikasi oleh KOMEO*).
   - **Bisnis Perorangan (Tanpa NIB):** Verifikasi identitas PIC dengan lencana *PIC Terverifikasi*.
   - **Bisnis Terdaftar (Dengan NIB):** Verifikasi dokumen NIB + KTP + Selfie PIC dengan centang biru nama usaha.
   - Seluruh berkas fisik disimpan secara privat di luar web root (`writable/uploads/verifications/`) dengan audit log akses admin.
4. **Manajemen Lencana Kehormatan Anggota (Custom Badges):**
   - Pembuatan lencana khusus oleh admin (*Top Vendor, Rekomendasi, Tergercep, dsb.*) dengan warna dan ikon dinamis.
   - Masa berlaku dan pencabutan lencana secara mandiri.
5. **KTA Digital & Verifikasi QR (Phase 4):**
   - Kartu Tanda Anggota digital dengan QR Code berbasis dual-token cryptographic selector & validator.
   - Halaman verifikasi publik resmi untuk memeriksa keaslian KTA.
   - Cetak KTA dalam format PNG, PDF per kartu, atau print-sheet batch.
6. **Manajemen Event & Presensi QR (Phase 5.5):**
   - Manajemen acara internal komunitas maupun tamu umum (*Hybrid*).
   - Tiket digital ber-QR code unik dengan pencegahan presensi ganda (*Anti-Double Check-in*).
   - Scanner kamera dan pencatatan presensi real-time.
7. **KOMEO Connect & Dokumen Profesional (Phase 5.6):**
   - Unggah CV profesional dengan validasi ketat `%PDF-`.
   - Sistem tautan portofolio eksternal aman.
   - Pengiriman pesan penawaran / inquiry kolaborasi dengan perlindungan token dan masa kedaluwarsa dokumen 7 hari.
8. **Admin CMS & Pengaturan Global:**
   - Panel admin komprehensif, pengaturan hero direktori, kuota sorotan, nomor WhatsApp resmi, dan logo website.

---

## 📦 Panduan Instalasi & Menjalankan Aplikasi

### 1. Prasyarat Sistem
- PHP >= 8.2 (ekstensi: `intl`, `mbstring`, `mysqli`, `curl`, `openssl`, `gd`)
- MySQL 8.0+ atau MariaDB 10.4+
- Composer 2.x

### 2. Konfigurasi Environment
Salin berkas `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Sesuaikan parameter `.env` berikut:
```ini
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost:8080/'
app.defaultLocale = 'id'
app.appTimezone = 'Asia/Jakarta'

database.default.hostname = localhost
database.default.database = komeo_db
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306
```

Generate encryption key aplikasi:
```bash
php spark key:generate --force
```

### 3. Migrasi Database
Jalankan migrasi seluruh tabel:
```bash
php spark migrate --all
```

### 4. Akun Administrator Pertama
Jalankan perintah Spark untuk inisialisasi superadmin:
```bash
php spark admin:create
```

### 5. Menjalankan Server Lokal
```bash
php spark serve --port 8080
```
Akses platform melalui browser di `http://localhost:8080`.

---

## 🔐 Keamanan Data & Penyimpanan Dokumen Privat

- **Berkas KTP, Selfie, CV, dan NIB**: Disimpan secara ketat di dalam direktori privat `writable/uploads/` yang terletak **di luar folder publik web root (`public/`)**.
- **Akses Dokumen Fisik**: Hanya dapat diakses melalui endpoint controller terautentikasi (`Admin\IdentityVerificationController::viewDocument` dan `Member\VerificationController::previewMyDocument`).
- **Audit Logging**: Setiap kali admin membuka atau mengunduh dokumen verifikasi fisik, riwayat akses dicatat ke tabel `audit_logs` (User ID, IP Address, User Agent, dan Dokumen).
- **Zero NIK Storage**: Sistem mematuhi prinsip minimalisasi data privasi dan tidak mengekstrak atau menyimpan NIK pengguna ke dalam kolom basis data terpisah.
- **Git Safety**: Berkas `.gitignore` telah dikonfigurasi untuk mencegah pengunggahan dokumen pengguna, token sesi, cadangan database, maupun berkas `.env` ke repositori publik.

---

## 💾 Panduan Persistensi & Backup Server Hosting

Saat mendeploy platform ke server hosting atau VPS:

1. **Folder yang Wajib Dipersistensikan / Dibackup:**
   - Direktori `writable/uploads/` (Dokumen verifikasi fisik KTP, NIB, CV).
   - Direktori `public/uploads/` (Foto avatar profil, logo perusahaan, portofolio).
   - Database MySQL / MariaDB (`komeo_db`).
2. **Izin Akses Folder (Permissions):**
   Pastikan direktori `writable/` dan `public/uploads/` memiliki izin tulis oleh web server (misal `chown -R www-data:www-data writable public/uploads` dan `chmod -R 775 writable`).
3. **Penyimpanan Nilai Environment di Hosting:**
   Pastikan variabel berikut dikonfigurasi di file `.env` server produksi:
   - `CI_ENVIRONMENT = production`
   - `app.baseURL = https://komeo.id/`
   - `database.default.*` (Kredensial database produksi)
   - `encryption.key` (Kunci enkripsi permanen produksi)
   - `email.SMTP*` (Kredensial pengiriman email SMTP produksi)

---

## 🧪 Pengujian Terintegrasi (Automated Tests)

KOMEO.ID dilengkapi dengan rangkaian uji coba mandiri:

```bash
# Uji coba Verifikasi Identitas & Usaha (13 tes)
php spark test:business-verif

# Uji coba Lencana Anggota & Aktivasi Manual (25 tes)
php spark test:phase5-ext

# Uji coba Event, Tiket QR, & KOMEO Connect (30 tes)
php spark test:phase5

# Uji coba Profil Anggota & Portofolio (Phase 2)
php spark test:phase2
```

---

## 📄 Lisensi
Hak Cipta © 2026 KOMEO.ID. Seluruh hak cipta dilindungi.
