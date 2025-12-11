<?php 
session_start();
include('includes/header.php'); 
?>

<div class="gateway-container" style="max-width: 600px; margin: 80px auto; text-align: center; background-color: rgba(255, 255, 255, 0.95); padding: 50px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
    
    <h2 class="text-primary fw-bold mb-4">Selamat Datang di PPDB Online</h2>
    <p class="text-muted mb-5">Silakan masuk untuk melanjutkan pendaftaran.</p>

    <a href="login_siswa.php" class="btn btn-primary btn-lg w-100 py-3 mb-4 shadow-sm d-flex align-items-center justify-content-center">
        <span class="fw-bold fs-5">Masuk sebagai Calon Siswa</span>
    </a>

    <hr class="my-4 text-muted">

    <p class="mb-3 text-muted small">Apakah Anda Panitia atau Staf Sekolah?</p>
    
    <div class="dropdown">
        <button class="btn btn-outline-secondary dropdown-toggle w-100" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
            Login Staf Sekolah
        </button>
        <ul class="dropdown-menu w-100 text-center shadow border-0 mt-1" aria-labelledby="dropdownMenuButton1">
            <li><a class="dropdown-item py-2" href="panitia/login.php">Panitia PPDB</a></li>
            <li><a class="dropdown-item py-2" href="keuangan/login.php">Staf Keuangan</a></li>
            <li><a class="dropdown-item py-2" href="kepalasekolah/login.php">Kepala Sekolah</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item py-2 text-danger" href="admin/login.php">Administrator Sistem</a></li>
        </ul>
    </div>

</div>

<?php 
if (isset($conn)) {
    $conn->close(); 
}
include('includes/footer.php'); 
?>