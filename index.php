<?php
session_start();
include('includes/koneksi.php');
if (isset($_SESSION['user_logged_in'])) {
    
    if ($_SESSION['user_mode'] == 'siswa') {
        $mode = 'siswa';
    } 
    elseif ($_SESSION['user_mode'] == 'kepsek') {
        header("Location: kepalasekolah/kepsek_dashboard.php");
        exit();
    }
    elseif ($_SESSION['user_mode'] == 'panitia') {
        header("Location: panitia/panitia_dashboard.php");
        exit();
    }
    elseif ($_SESSION['user_mode'] == 'keuangan') {
        header("Location: keuangan/verifikasi.php");
        exit();
    }

} 
// Cek login admin
elseif (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin/admin.php");
    exit();
}
else {
    $_SESSION['user_mode'] = 'guest';
    $mode = 'guest';
}
include('includes/header.php'); 

$greeting_text = ($mode === 'siswa')
    ? "Selamat datang kembali, " . htmlspecialchars($_SESSION['user_nama']) . "! Siap untuk mendaftar?"
    : "Selamat datang, Pengunjung! Silakan jelajahi informasi PPDB kami.";
?>

<main>
    <div class="hero-section text-center mt-4">
        <div class="container">
            <h1 class="display-4 fw-bold text-primary animate-fade-in-down">SELAMAT DATANG DI SPMB ONLINE</h1>
            <p class="lead text-secondary animate-fade-in-down delay-1">SMK BAKTI NUSANTARA 666 Tahun Ajaran 2025/2026</p>
            <hr class="my-4">
            <h3 class="mb-4 text-dark animate-fade-in-down delay-2"><?php echo $greeting_text; ?></h3>

            <?php if ($mode === 'siswa') { ?>
                <a href="daftar.php" class="btn btn-ppdb btn-lg shadow-lg animate-fade-in-down delay-3">ISI FORMULIR PENDAFTARAN</a>
            <?php } else { ?>
                <a href="info.php" class="btn btn-ppdb btn-lg shadow-lg animate-fade-in-down delay-3">LIHAT INFO LENGKAP</a>
            <?php } ?>
        </div>
    </div>

    <div class="container mt-5">
        <div class="row g-4 text-center">
            
            <div class="col-md-4 animate-fade-in-up">
                <div class="p-4 border rounded-3 shadow-sm bg-light">
                    <h4 class="text-dark">Tentang Sekolah</h4>
                    <p>Ketahui lebih lanjut tentang Visi, Misi, dan Fasilitas terbaik kami.</p>
                    <a href="tentang.php" class="btn btn-outline-primary">Tentang Kami</a>
                </div>
            </div>
            
            <div class="col-md-4 animate-fade-in-up delay-1">
                <div class="p-4 border rounded-3 shadow-sm bg-light">
                    <h4 class_ = "text-dark">Info Pendaftaran</h4>
                    <p>Jadwal, Syarat, dan alur pendaftaran terbaru.</p>
                    <a href="info.php" class="btn btn-outline-primary">Lihat Info</a>
                </div>
            </div>
            
            <div class="col-md-4 animate-fade-in-up delay-2">
                <div class="p-4 border rounded-3 shadow-sm bg-light">
                    <h4 class="text-dark">Kontak Kami</h4>
                    <p>Hubungi tim PPDB kami jika ada pertanyaan lebih lanjut.</p>
                    <a href="kontak.php" class="btn btn-outline-primary">Hubungi</a>
                </div>
            </div>
            
        </div>
    </div>
</main>

<?php 
if (isset($conn)) {
    $conn->close();
}
include('includes/footer.php'); 
?>