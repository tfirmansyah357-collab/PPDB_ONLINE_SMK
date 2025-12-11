<?php 
session_start();
include('includes/koneksi.php'); // WAJIB: Sertakan koneksi database

// ===============================================
// LOGIKA OTP (CAPTCHA) BARU
// ===============================================
// Kita buat kode OTP baru setiap kali halaman dimuat, KECUALI jika itu adalah hasil submit form (POST)
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $_SESSION['otp_code'] = rand(10000, 99999); // Buat 5 digit kode acak
}
// ===============================================

// Cek apakah user sudah mendaftar?
$user_id = $_SESSION['user_id'] ?? 0;
$sudah_mendaftar = false;
$no_pendaftaran_user = '';
$status_pembayaran_user = '';

if ($user_id > 0) {
    $stmt_cek = $conn->prepare("SELECT no_pendaftaran, status_pembayaran FROM pendaftar WHERE user_id = ?");
    $stmt_cek->bind_param("i", $user_id);
    $stmt_cek->execute();
    $result_cek = $stmt_cek->get_result();
    if ($result_cek->num_rows > 0) {
        $sudah_mendaftar = true;
        $data_pendaftar = $result_cek->fetch_assoc();
        $no_pendaftaran_user = $data_pendaftar['no_pendaftaran'];
        $status_pembayaran_user = $data_pendaftar['status_pembayaran'];
    }
    $stmt_cek->close();
}

// Sertakan header SETELAH logika
include('includes/header.php'); 

$message = '';
$show_registration_form = false;
$gelombang_aktif_ditemukan = false; // Flag baru untuk cek gelombang

// --- CEK GELOMBANG AKTIF (UNTUK TAMPILAN) ---
$sql_cek_gel = "SELECT id FROM gelombang WHERE aktif = 1 AND NOW() BETWEEN tgl_mulai AND tgl_selesai LIMIT 1";
$result_cek_gel = $conn->query($sql_cek_gel);
if ($result_cek_gel->num_rows > 0) {
    $gelombang_aktif_ditemukan = true;
} else {
    // Jika tidak ada gelombang aktif, beri pesan error
    $message = '<div class="alert alert-danger fw-bold text-center" role="alert">
                    <h4 class="alert-heading">Pendaftaran Ditutup!</h4>
                    <p>Mohon maaf, saat ini tidak ada Gelombang Pendaftaran yang dibuka. Silakan hubungi panitia atau cek kembali nanti.</p>
                </div>';
}

if (isset($_SESSION['user_mode']) && $_SESSION['user_mode'] == 'siswa') {
    if ($sudah_mendaftar) {
        $show_registration_form = false; 
    } else {
        // Hanya tampilkan form jika gelombang aktif ditemukan
        if ($gelombang_aktif_ditemukan) {
            $show_registration_form = true;
        } else {
            $show_registration_form = false; // Sembunyikan form jika tutup
        }
    }
}

// ===============================================
// LOGIKA 1: BUAT AKUN BARU
// ===============================================
if (isset($_POST['action']) && $_POST['action'] == 'create_account') {
    $new_email = $_POST['new_email'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $new_nama = $_POST['new_nama'] ?? '';
    
    // Ambil input OTP dari form
    $otp_input = $_POST['otp_input'] ?? '';
    $otp_session = $_SESSION['otp_code'] ?? 'xxxxxx'; // Ambil dari session

    // --- PENGECEKAN OTP DULU ---
    if ($otp_input !== (string)$otp_session) {
        $message = '<div class="alert alert-danger" role="alert">Kode Verifikasi yang Anda masukkan salah.</div>';
        $_SESSION['otp_code'] = rand(10000, 99999);
    }
    // --- JIKA OTP BENAR, LANJUTKAN ---
    elseif (!empty($new_email) && strlen($new_password) >= 3 && !empty($new_nama)) {
        
        $stmt_cek_email = $conn->prepare("SELECT id FROM pengguna WHERE email = ?");
        $stmt_cek_email->bind_param("s", $new_email);
        $stmt_cek_email->execute();
        $result_cek_email = $stmt_cek_email->get_result();

        if ($result_cek_email->num_rows > 0) {
            $message = '<div class="alert alert-danger" role="alert">Email sudah terdaftar. Silakan <a href="login_siswa.php">Login</a>.</div>';
            $_SESSION['otp_code'] = rand(10000, 99999); 
        } else {
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $role = 'pendaftar';
            
            $sql_insert = "INSERT INTO pengguna (nama, email, password_hash, role) VALUES (?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("ssss", $new_nama, $new_email, $password_hash, $role);
            
            if ($stmt_insert->execute()) {
                $new_user_id = $conn->insert_id;
                
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_mode'] = 'siswa';
                $_SESSION['user_email'] = $new_email;
                $_SESSION['user_id'] = $new_user_id;
                $_SESSION['user_nama'] = $new_nama;
                
                unset($_SESSION['otp_code']); // Hapus OTP setelah berhasil
                
                // Cek gelombang lagi untuk redirect yang tepat
                if ($gelombang_aktif_ditemukan) {
                     $message = '<div class="alert alert-success" role="alert">Akun berhasil dibuat. Silakan isi formulir.</div>';
                     $show_registration_form = true;
                } else {
                     // Akun jadi, tapi pendaftaran tutup
                     $message = '<div class="alert alert-success" role="alert">Akun berhasil dibuat. Namun, pendaftaran sedang ditutup.</div>';
                     $show_registration_form = false;
                }

            } else {
                $message = '<div class="alert alert-danger" role="alert">Pembuatan akun gagal. Terjadi kesalahan database.</div>';
                $_SESSION['otp_code'] = rand(10000, 99999); 
            }
            $stmt_insert->close();
        }
        $stmt_cek_email->close();
    } else {
        $message = '<div class="alert alert-danger" role="alert">Pembuatan akun gagal. Pastikan data valid.</div>';
        $_SESSION['otp_code'] = rand(10000, 99999); 
    }
}

// ===============================================
// LOGIKA 2: SUBMIT FORMULIR PENDAFTARAN
// ===============================================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'submit_form') {
   
    $user_id = $_SESSION['user_id'] ?? 0;
    
    if ($user_id === 0 || $sudah_mendaftar) {
        $message = '<div class="alert alert-danger" role="alert">Sesi Anda berakhir atau Anda sudah mendaftar.</div>';
    } else {
        // --- CEK GELOMBANG AKTIF (UNTUK VALIDASI SUBMIT) ---
        // Query ini memastikan hanya mengambil gelombang yang AKTIF (1) dan TANGGAL SESUAI
        $sql_gel = "SELECT id, biaya_daftar FROM gelombang 
                    WHERE aktif = 1 
                    AND NOW() BETWEEN tgl_mulai AND tgl_selesai 
                    LIMIT 1";
        $result_gel = $conn->query($sql_gel);
        
        if ($result_gel->num_rows == 0) {
            $message = '<div class="alert alert-danger" role="alert">GAGAL: Pendaftaran sedang ditutup atau tidak ada gelombang aktif.</div>';
        } else {
            // Gelombang valid, lanjut proses
            $gelombang = $result_gel->fetch_assoc();
            $gelombang_id = $gelombang['id'];

            // Ambil data POST
            $nama_lengkap = $_POST['nama_lengkap'] ?? null;
            $nis = $_POST['nis'] ?? null;
            $no_handphone = $_POST['no_handphone'] ?? null;
            $email = $_POST['email'] ?? null;
            $wilayah_asal_id = $_POST['wilayah_asal'] ?? null;
            $nama_ayah = $_POST['nama_ayah'] ?? null;
            $nama_ibu = $_POST['nama_ibu'] ?? null;
            $no_ortu = $_POST['no_ortu'] ?? null;
            $asal_sekolah = $_POST['asal_sekolah'] ?? null;
            $jurusan_id = $_POST['pilihan_jurusan'] ?? null;

            $conn->begin_transaction();
            try {
                // 1. INSERT ke 'pendaftar'
                $sql_pendaftar = "INSERT INTO pendaftar (user_id, tanggal_daftar, no_pendaftaran, gelombang_id, jurusan_id, status_administrasi, status_pembayaran) 
                                  VALUES (?, NOW(), ?, ?, ?, 'Pending', 'Belum Bayar')";
                $no_pendaftaran = "PPDB-" . date("Y") . "-" . str_pad($user_id, 4, '0', STR_PAD_LEFT);
                
                $stmt1 = $conn->prepare($sql_pendaftar);
                $stmt1->bind_param("isii", $user_id, $no_pendaftaran, $gelombang_id, $jurusan_id);
                $stmt1->execute();
                $pendaftar_id = $conn->insert_id; 
                
                // 2. INSERT ke detail (siswa, ortu, sekolah)
                $sql_siswa = "INSERT INTO pendaftar_data_siswa (pendaftar_id, nama_lengkap, nis, no_handphone, email, wilayah_asal_id) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt2 = $conn->prepare($sql_siswa);
                $stmt2->bind_param("issssi", $pendaftar_id, $nama_lengkap, $nis, $no_handphone, $email, $wilayah_asal_id);
                $stmt2->execute();
                
                $sql_ortu = "INSERT INTO pendaftar_data_ortu (pendaftar_id, nama_ayah, nama_ibu, no_handphone_ortu) VALUES (?, ?, ?, ?)";
                $stmt3 = $conn->prepare($sql_ortu);
                $stmt3->bind_param("isss", $pendaftar_id, $nama_ayah, $nama_ibu, $no_ortu);
                $stmt3->execute();

                $sql_sekolah = "INSERT INTO pendaftar_asal_sekolah (pendaftar_id, nama_sekolah) VALUES (?, ?)";
                $stmt4 = $conn->prepare($sql_sekolah);
                $stmt4->bind_param("is", $pendaftar_id, $asal_sekolah);
                $stmt4->execute();

                // 3. Handle Upload Berkas
                $upload_dir = 'uploads/berkas/';
                if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
                $berkas_list = ['file_rapor' => 'RAPOR', 'file_akta' => 'AKTA', 'file_kk' => 'KK'];
                foreach ($berkas_list as $input_name => $jenis_berkas) {
                    if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] == 0) {
                        $file_name = $_FILES[$input_name]['name'];
                        $file_size_kb = $_FILES[$input_name]['size'] / 1024;
                        $new_file_name = $pendaftar_id . "_" . $jenis_berkas . "_" . basename($file_name);
                        $target_file = $upload_dir . $new_file_name;
                        $url_file = $base_url . $target_file; 
                        if (move_uploaded_file($_FILES[$input_name]['tmp_name'], $target_file)) {
                            $sql_berkas = "INSERT INTO pendaftar_berkas (pendaftar_id, jenis, nama_file, url_file, ukuran_kb, valid) VALUES (?, ?, ?, ?, ?, 0)";
                            $stmt_berkas = $conn->prepare($sql_berkas);
                            $stmt_berkas->bind_param("isssd", $pendaftar_id, $jenis_berkas, $new_file_name, $url_file, $file_size_kb);
                            $stmt_berkas->execute();
                        }
                    }
                }

                $conn->commit();
                $message = '<div class="alert alert-success" role="alert">Pendaftaran Anda BERHASIL terkirim! 
                            Nomor Pendaftaran Anda: <strong>' . $no_pendaftaran . '</strong>.
                            Silakan lanjutkan ke halaman <a href="pembayaran.php" class="alert-link">Pembayaran</a>.</div>';
                
                $sudah_mendaftar = true;
                $no_pendaftaran_user = $no_pendaftaran;
                $status_pembayaran_user = 'Belum Bayar';
                $show_registration_form = false;

            } catch (mysqli_sql_exception $exception) {
                $conn->rollback();
                $message = '<div class="alert alert-danger" role="alert">Pendaftaran GAGAL: Terjadi kesalahan database. Silakan coba lagi. 
                            <br><small>' . $exception->getMessage() . '</small></div>';
            }
        } 
    }
}

if (!$show_registration_form && !$sudah_mendaftar && !isset($_SESSION['user_logged_in'])):
?>
<div class="card shadow mb-5 p-4 mx-auto" style="max-width: 500px;">
    <h3 class="text-center text-success mb-4">Buat Akun Pendaftaran Baru</h3>
    <?php echo $message; ?>
    
    <?php if (!$gelombang_aktif_ditemukan): ?>
         <div class="alert alert-warning text-center">
             <strong>Perhatian:</strong> Saat ini tidak ada gelombang pendaftaran yang aktif. Anda dapat membuat akun, tetapi formulir pendaftaran belum bisa diisi.
         </div>
    <?php endif; ?>

    <p class="text-muted text-center">Silakan buat akun untuk memulai.</p>
    <form method="POST" action="daftar.php">
        <input type="hidden" name="action" value="create_account">
        <div class="mb-3">
            <label for="new_nama" class="form-label">Nama Lengkap Anda</label>
            <input type="text" class="form-control" id="new_nama" name="new_nama" required>
        </div>
        <div class="mb-3">
            <label for="new_email" class="form-label">Email (Username)</label>
            <input type="email" class="form-control" id="new_email" name="new_email" required>
        </div>
        <div class="mb-4">
            <label for="new_password" class="form-label">Password</label>
            <input type="password" class="form-control" id="new_password" name="new_password" required minlength="3">
        </div>

        <div class="alert alert-info text-center">
            <label class="form-label">Kode Verifikasi:</label>
            <h3 class="fw-bold text-dark" style="letter-spacing: 3px;"><?php echo $_SESSION['otp_code']; ?></h3>
            <small>Masukkan 5 digit kode di atas.</small>
        </div>
        <div class="mb-4">
            <input type="text" class="form-control text-center" id="otp_input" name="otp_input" required maxlength="5" autocomplete="off" placeholder="Ketik kode disini">
        </div>

        <button type="submit" class="btn btn-success w-100">Buat Akun & Lanjutkan</button>
    </form>
    <hr>
    <p class="text-center">Sudah punya akun? <a href="login_siswa.php">Login di sini</a></p>
</div>

<?php 
elseif ($show_registration_form && !$sudah_mendaftar):
?>
    <h2 class="text-center text-primary mb-4">Formulir Pendaftaran Online SMK BAKTI NUSANTARA 666</h2>
    <?php echo $message; ?>
    <div class="card shadow mb-5">
        <div class="card-header bg-primary text-white">Lengkapi Data Pendaftaran</div>
        <div class="card-body">
            <form method="POST" action="daftar.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="submit_form">
                
                <h4 class="text-secondary mb-3 border-bottom pb-2">1. Biodata Diri</h4>
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="nama_lengkap" class="form-label">Nama Lengkap Siswa</label><input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($_SESSION['user_nama'] ?? ''); ?>" required></div>
                    <div class="col-md-6 mb-3"><label for="nis" class="form-label">NIS</label><input type="text" class="form-control" id="nis" name="nis"></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="no_handphone" class="form-label">No HandPhone Pribadi</label><input type="text" class="form-control" id="no_handphone" name="no_handphone" required></div>
                    <div class="col-md-6 mb-3"><label for="email" class="form-label">Email Aktif</label><input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>" readonly required></div>
                </div>
                <div class="mb-3">
                    <label for="wilayah_asal" class="form-label">Wilayah Asal</label>
                    <select class="form-select" id="wilayah_asal" name="wilayah_asal" required>
                        <option value="">-- Pilih Wilayah --</option>
                        <?php foreach ($wilayah_options_db as $wilayah): ?>
                            <option value="<?php echo $wilayah['id']; ?>"><?php echo htmlspecialchars($wilayah['nama_wilayah']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
              
                <h4 class="text-secondary mb-3 border-bottom pb-2">2. Identitas Orang Tua</h4>
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="nama_ayah" class="form-label">Nama Ayah</label><input type="text" class="form-control" id="nama_ayah" name="nama_ayah" required></div>
                    <div class="col-md-6 mb-3"><label for="nama_ibu" class="form-label">Nama Ibu</label><input type="text" class="form-control" id="nama_ibu" name="nama_ibu" required></div>
                </div>
                <div class="mb-4"><label for="no_ortu" class="form-label">No HandPhone Ortu/Wali</label><input type="text" class="form-control" id="no_ortu" name="no_ortu" required></div>
                
                <h4 class="text-secondary mb-3 border-bottom pb-2">3. Asal Sekolah</h4>
                <div class="mb-4"><label for="asal_sekolah" class="form-label">Nama Asal Sekolah (SMP/MTs)</label><input type="text" class="form-control" id="asal_sekolah" name="asal_sekolah" required></div>

                <h4 class="text-secondary mb-3 border-bottom pb-2">4. Pilihan Jurusan</h4>
                <div class="mb-4">
                    <label for="pilihan_jurusan" class="form-label">Pilih Jurusan</label>
                    <select class="form-select" id="pilihan_jurusan" name="pilihan_jurusan" required>
                        <option value="">-- Pilih Salah Satu --</option>
                        <?php foreach ($jurusan_options_db as $jurusan): ?>
                            <option value="<?php echo $jurusan['id']; ?>"><?php echo htmlspecialchars($jurusan['kode'] . ' - ' . $jurusan['nama']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <h4 class="text-secondary mb-3 border-bottom pb-2">5. Upload Berkas</h4>
                <div class="mb-3"><label for="file_rapor" class="form-label">Rapor Semester Terakhir</label><input class="form-control" type="file" id="file_rapor" name="file_rapor" required></div>
                <div class="mb-3"><label for="file_akta" class="form-label">Akta Kelahiran</label><input class="form-control" type="file" id="file_akta" name="file_akta" required></div>
                <div class="mb-4"><label for="file_kk" class="form-label">Kartu Keluarga (KK)</label><input class="form-control" type="file" id="file_kk" name="file_kk" required></div>
                
                <button type="submit" class="btn btn-ppdb btn-lg w-100 mt-3">Kirim Pendaftaran</button>
            </form>
        </div>
    </div>

<?php 
elseif ($sudah_mendaftar):
?>
    <div class="card shadow mb-5 p-4 mx-auto" style="max-width: 700px;">
        <h3 class="text-center text-success mb-4">Pendaftaran Anda Telah Diterima</h3>
        <?php echo $message; ?>
        <div class="alert alert-info">
            <h4 class="alert-heading">Data Anda</h4>
            <p>Hai, <strong><?php echo htmlspecialchars($_SESSION['user_nama']); ?></strong>!</p>
            <p>Nomor Pendaftaran Anda adalah: <strong><?php echo htmlspecialchars($no_pendaftaran_user); ?></strong></p>
            <hr>
            <p class="mb-0">Status Pembayaran Saat Ini: <strong><?php echo htmlspecialchars($status_pembayaran_user); ?></strong></p>
        </div>
        <?php if ($status_pembayaran_user != 'Lunas'): ?>
            <p class="text-center mt-3">Silakan lanjutkan ke tahap berikutnya.</p>
            <a href="pembayaran.php" class="btn btn-ppdb btn-lg w-100">Lanjutkan ke Pembayaran</a>
        <?php else: ?>
             <p class="text-center mt-3">Pembayaran Anda sudah Lunas. Terima kasih.</p>
        <?php endif; ?>
    </div>

<?php 
elseif (!$gelombang_aktif_ditemukan && isset($_SESSION['user_logged_in'])):
?>
    <div class="alert alert-danger text-center mt-5 p-5 shadow">
        <h2 class="mb-3">🚫 Pendaftaran Ditutup</h2>
        <p class="lead">Mohon maaf, saat ini tidak ada Gelombang Pendaftaran yang sedang dibuka.</p>
        <p>Silakan hubungi admin sekolah untuk informasi lebih lanjut.</p>
        <a href="index.php" class="btn btn-outline-danger mt-3">Kembali ke Halaman Utama</a>
    </div>

<?php endif; ?>

<?php 
$conn->close();
include('includes/footer.php'); 
?>