<?php
// File: admin/user_tambah.php
include('../includes/admin_header.php');

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role']; 
    if (empty($nama) || empty($email) || empty($password) || empty($role)) {
        $message = '<div class="alert alert-danger">Mohon lengkapi semua data.</div>';
    } else {
        $cek_email = $conn->prepare("SELECT id FROM pengguna WHERE email = ?");
        $cek_email->bind_param("s", $email);
        $cek_email->execute();
        $result_cek = $cek_email->get_result();

        if ($result_cek->num_rows > 0) {
            $message = '<div class="alert alert-danger">Gagal! Email tersebut sudah digunakan oleh pengguna lain.</div>';
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO pengguna (nama, email, password_hash, role, aktif) VALUES (?, ?, ?, ?, 1)");
            $stmt->bind_param("ssss", $nama, $email, $password_hash, $role);
            
            if ($stmt->execute()) {
                $_SESSION['message'] = "Akun baru berhasil ditambahkan!";
                echo "<script>window.location='users.php';</script>";
                exit();
            } else {
                $message = '<div class="alert alert-danger">Terjadi kesalahan database: ' . $conn->error . '</div>';
            }
            $stmt->close();
        }
        $cek_email->close();
    }
}
?>

<h3 class="mb-4">Tambah Akun Baru</h3>

<?php echo $message; ?>

<div class="card shadow-sm" style="max-width: 600px;">
    <div class="card-header bg-primary text-white">
        Formulir Pembuatan Akun Staf
    </div>
    <div class="card-body">
        <form method="POST" action="user_tambah.php">
            
            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" name="nama" placeholder="Contoh: Budi Santoso" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email (Username Login)</label>
                <input type="email" class="form-control" name="email" placeholder="Contoh: panitia01@ppdb.com" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password Awal</label>
                <input type="text" class="form-control" name="password" required minlength="3">
                <div class="form-text text-muted">Berikan password ini kepada pemilik akun.</div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Peran (Role)</label>
                <select class="form-select" name="role" required>
                    <option value="">-- Pilih Peran --</option>
                    <option value="panitia">Panitia (Verifikasi Berkas)</option>
                    <option value="keuangan">Staf Keuangan (Verifikasi Bayar)</option>
                    <option value="kepsek">Kepala Sekolah (Monitoring)</option>
                    <option value="admin">Administrator (Full Akses)</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">Simpan Akun</button>
            <a href="users.php" class="btn btn-secondary w-100 mt-2">Batal</a>
        </form>
    </div>
</div>

<?php include('../includes/admin_footer.php'); ?>