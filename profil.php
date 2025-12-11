<?php
session_start();
$base_url = "http://localhost/ppdb/"; 

// 1. KONEKSI DAN KEAMANAN
include('includes/koneksi.php');

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_mode'] !== 'siswa') {
    header("Location: " . $base_url . "login_siswa.php");
    exit();
}

$user_id = $_SESSION['user_id'] ?? 0;
$data_profil = null;
$message = '';

// 2. AMBIL DATA PROFIL
$sql = "SELECT 
            pds.nama_lengkap, 
            p.no_pendaftaran,
            p.status_pembayaran,
            p.status_administrasi,
            j.nama as nama_jurusan
        FROM pendaftar p
        JOIN pendaftar_data_siswa pds ON p.id = pds.pendaftar_id
        JOIN jurusan j ON p.jurusan_id = j.id
        WHERE p.user_id = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data_profil = $result->fetch_assoc();
} else {
    $message = '<div class="alert alert-warning">Anda belum menyelesaikan formulir pendaftaran. Silakan <a href="daftar.php">klik di sini</a> untuk mendaftar.</div>';
}
$stmt->close();

include('includes/header.php'); 
?>

<h2 class="text-primary mb-4">Profil Pendaftaran Saya</h2>

<?php echo $message; ?>

<?php if ($data_profil): ?>
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Data Calon Siswa</h5>
    </div>
    <div class="card-body">
        <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <strong>Nama Lengkap:</strong>
                <span><?php echo htmlspecialchars($data_profil['nama_lengkap']); ?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <strong>No. Pendaftaran:</strong>
                <span><?php echo htmlspecialchars($data_profil['no_pendaftaran']); ?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <strong>Pilihan Jurusan:</strong>
                <span><?php echo htmlspecialchars($data_profil['nama_jurusan']); ?></span>
            </li>
            
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <strong>Status Pembayaran:</strong>
                <?php
                $status_bayar = $data_profil['status_pembayaran'];
                $badge_bayar = 'bg-secondary';
                if ($status_bayar == 'Lunas') $badge_bayar = 'bg-success';
                if ($status_bayar == 'Pending Verifikasi') $badge_bayar = 'bg-warning text-dark';
                if ($status_bayar == 'Ditolak') $badge_bayar = 'bg-danger';
                ?>
                <span class="badge <?php echo $badge_bayar; ?> fs-6"><?php echo htmlspecialchars($status_bayar); ?></span>
            </li>

            <li class="list-group-item d-flex justify-content-between align-items-center">
                <strong>Status Berkas:</strong>
                <?php
                $status_adm = $data_profil['status_administrasi'];
                $badge_adm = 'bg-secondary';
                if ($status_adm == 'Diverifikasi') $badge_adm = 'bg-success';
                if ($status_adm == 'Pending') $badge_adm = 'bg-warning text-dark';
                if ($status_adm == 'Ditolak') $badge_adm = 'bg-danger';
                ?>
                <span class="badge <?php echo $badge_adm; ?> fs-6"><?php echo htmlspecialchars($status_adm); ?></span>
            </li>

            <li class="list-group-item d-flex justify-content-between align-items-center">
                <strong>Status Kelulusan:</strong>
                <?php
                if ($data_profil['status_pembayaran'] == 'Lunas' && $data_profil['status_administrasi'] == 'Diverifikasi') {
                    $status_final = 'SELAMAT, ANDA DITERIMA';
                    $badge_final = 'bg-success';
                } elseif ($data_profil['status_pembayaran'] == 'Ditolak' || $data_profil['status_administrasi'] == 'Ditolak') {
                    $status_final = 'TIDAK DITERIMA';
                    $badge_final = 'bg-danger';
                } else {
                    $status_final = 'PROSES SELEKSI';
                    $badge_final = 'bg-info';
                }
                ?>
                <span class="badge <?php echo $badge_final; ?> fs-6"><?php echo $status_final; ?></span>
            </li>
        </ul>

        <div class="mt-4 text-center">
            <a href="cetak_bukti_siswa.php" target="_blank" class="btn btn-danger btn-lg shadow">
                 Cetak Bukti Pendaftaran (PDF)
            </a>
            <p class="text-muted mt-2 small">Klik tombol di atas untuk mengunduh bukti pendaftaran Anda.</p>
        </div>
        </div>
    <div class="card-footer text-muted">
        Data ini adalah status pendaftaran Anda saat ini.
    </div>
</div>
<?php endif; ?>

<?php 
$conn->close();
include('includes/footer.php'); 
?>