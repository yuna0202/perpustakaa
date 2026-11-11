# Kontrak implementasi

Laravel 11, Blade/Tailwind 3, MySQL. UI Indonesia, hijau tua/ivory/aksen emas. Tidak ada peminjaman, denda, reservasi, laporan, atau reset password.

## Model (parent)
User: t_user, id_user, nama/email/password/role; anggota() hasOne. Tidak ada remember_token; login tanpa remember.
Anggota: t_anggota, id_anggota; user() belongsTo. QRcode nullable unique string 64 (token acak), status pending/disetujui/ditolak. Foto portrait disk public `anggota/foto`, KTM disk local (private) `ktm`. Kategori: t_kategori, id_kategori; buku() hasMany, tanpa timestamps. Buku: t_buku, id_buku; kategori() belongsTo, tanpa timestamps, tanggal_masuk cast date.

## Route dan view contract
Parent owns routes/web.php, bootstrap/app.php, config/*, providers, models, migrations, seeders, composer/npm manifests, lang, docs, tests initially.
Auth backend agent owns AuthController, RegistrationController, MemberController, Admin/ApplicantController, middleware EnsureRole, requests LoginRequest/RegisterRequest/ReviewApplicantRequest, Mail/*, Services/*, resources/views/emails/*.
UI agent owns resources/views except emails, resources/css/app.css, resources/js/app.js and tailwind/postcss configs.

- home GET /: view home, $totalBuku, $totalKategori, $bukuTerbaru (6 Buku with kategori)
- katalog.index GET /katalog: view katalog.index, $buku paginator, $kategori collection, q and kategori request inputs
- katalog.show GET /katalog/{buku}: view katalog.show, $buku with kategori
- login GET /login: AuthController.create view auth.login; login.store POST /login AuthController.store; logout POST /logout AuthController.destroy
- daftar GET /daftar: RegistrationController.create view auth.daftar; daftar.store POST /daftar RegistrationController.store
- anggota.kartu GET /anggota/kartu: MemberController.card view anggota.kartu, $anggota with user (can be null), $qrCode (safe SVG string or null)
- anggota.verifikasi GET /anggota/verifikasi/{token}: MemberController.verify view anggota.verifikasi, $anggota (approved only); public card identity: nama, nim, prodi, fakultas, tanggal terbit, status, foto, and institution. No angkatan/email/phone/KTM.
- admin.dashboard GET /admin: view admin.dashboard, $pending, $totalAnggota (approved), $totalBuku (titles), $stokHabis, $pendaftarTerbaru (5 with user)
- admin.pendaftar.index GET /admin/pendaftar: ApplicantController.index view admin.pendaftar.index, $pendaftar paginator with user. request q and status (default pending)
- admin.pendaftar.show GET /admin/pendaftar/{anggota}: ApplicantController.show view admin.pendaftar.show, $anggota with user
- admin.pendaftar.review PATCH /admin/pendaftar/{anggota}: ApplicantController.review; fields status_verifikasi=disetujui/ditolak, alasan_penolakan required if ditolak. Pending only transition, repeated decisions rejected with validation.
- admin.pendaftar.ktm GET /admin/pendaftar/{anggota}/ktm: ApplicantController.ktm returns private file with no-store
- admin.kategori resource except show: index $kategori paginator with buku_count; create/edit view admin.kategori.form with $kategori model (new model on create). fields nama_kategori
- admin.buku resource: index $buku paginator + $kategori list with q/kategori filter, create/edit view admin.buku.form with $buku model and $kategori list; show redirect public detail. Required fields exactly spec.

All forms CSRF; PUT for resource updates, DELETE deletes. Admin routes auth+role:admin, card auth+role:mahasiswa. Guest only login/register. Session flash `success`, `warning`, `error`. Templates use x-layout with title prop and optional admin prop; UI agent may choose layout implementation. Forms show old values and per-field Indonesian errors. Client form prevent duplicate submit preserves clicked submitter.

Registration backend logs in newly registered mahasiswa after transaction, redirects card. Mail after commit with failure caught: preserve registration/decision and show warning; never log secrets. Admin dashboard pending count is durable notification. Approval QR is random 64 chars, generated once, only after approved. QR generated as SVG using simplesoftwareio/simple-qrcode (no imagick).

Seeder creates admin@gajayana.ac.id only when ADMIN_INITIAL_PASSWORD is supplied privately, plus 5 categories and 20 illustrative books; it is idempotent. Mail log, file sessions/cache, sync queue. MySQL default; isolated SQLite in-memory tests.
