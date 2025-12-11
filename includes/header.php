<?php 
// File: includes/header.php

// Sertakan file koneksi HANYA jika $conn belum ada
if (!isset($conn)) {
    include_once('includes/koneksi.php');
}

$base_url = "http://localhost/ppdb/"; 
$user_mode = 'guest'; // Default
$user_nama = 'Pengunjung';

if (isset($_SESSION['user_mode']) && $_SESSION['user_mode'] == 'siswa') {
    $user_mode = 'siswa';
    $user_nama = $_SESSION['user_nama'] ?? 'Calon Siswa';
}
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    $user_mode = 'admin';
    $user_nama = $_SESSION['admin_username'] ?? 'Admin';
}

// Logika URL Logout
$logout_url = '';
if ($user_mode == 'siswa') {
    $logout_url = $base_url . 'login_siswa.php?logout=true';
} elseif ($user_mode == 'admin') {
    $logout_url = $base_url . 'admin/login.php?logout=true';
}
$jurusan_options_db = [];
if (isset($conn)) { 
    $sql_jurusan = "SELECT id, kode, nama FROM jurusan ORDER BY nama ASC";
    $result_jurusan = $conn->query($sql_jurusan);
    if ($result_jurusan && $result_jurusan->num_rows > 0) {
        while($row = $result_jurusan->fetch_assoc()) {
            $jurusan_options_db[] = $row;
        }
    }

    $wilayah_options_db = [];
    $sql_wilayah = "SELECT id, nama_wilayah FROM ref_wilayah_asal ORDER BY nama_wilayah ASC";
    $result_wilayah = $conn->query($sql_wilayah);
    if ($result_wilayah && $result_wilayah->num_rows > 0) {
        while($row = $result_wilayah->fetch_assoc()) {
            $wilayah_options_db[] = $row;
        }
    }
}
// ==========================================================

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPDB Online | SMK BAKTI NUSANTARA 666</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/images/logo_bn1.jpeg">
    
    <style>
        body {
            background-image: url('<?php echo $base_url; ?>assets/images/banner all.jpg'); 
            background-size: cover; 
            background-repeat: no-repeat; 
            background-attachment: fixed; 
            background-position: center center;
            min-height: 100vh;
            display: flex; 
            flex-direction: column; 
        }
        main { flex: 1; }
        .navbar-custom { background-color: #007bff; } 
        .navbar-custom .nav-link, .navbar-custom .navbar-brand { color: #ffffff; } 
        .hero-section { background-color: rgba(255, 255, 255, 0.85); padding: 80px 0; border-radius: 10px; } 
        .btn-ppdb { background-color: #28a745; color: white; border: none; } 
        .btn-ppdb:hover { background-color: #218838; }
        .footer-custom { background-color: #007bff; color: white; padding: 20px 0; }
        .card { background-color: rgba(255, 255, 255, 0.9); } 
        .table { background-color: rgba(255, 255, 255, 0.9); }

        /*animasi*/    

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in-down,
        .animate-fade-in-up {
            opacity: 0; 
            animation-fill-mode: forwards; 
        }

        .animate-fade-in-down {
            animation-name: fadeInDown;
            animation-duration: 0.8s;
            animation-timing-function: ease-out;
        }
        
        .animate-fade-in-up {
            animation-name: fadeInUp;
            animation-duration: 0.8s;
            animation-timing-function: ease-out;
        }
        .delay-1 { animation-delay: 0.2s; }
        .delay-2 { animation-delay: 0.4s; }
        .delay-3 { animation-delay: 0.6s; }
        
     
    </style>
</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg navbar-custom shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?php echo $base_url; ?>index.php">
                <img src="<?php echo $base_url; ?>assets/images/logo_bn1.png" alt="Logo Sekolah" width="40" height="40" class="d-inline-block align-text-top me-2">
                SMK BAKTI NUSANTARA 666
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
               <ul class="navbar-nav ms-auto">
                 
                 <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>index.php">Home</a></li>
                 <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>tentang.php">Tentang Sekolah</a></li>
                 <li class_ = "nav-item"><a class="nav-link" href="<?php echo $base_url; ?>info.php">Info Sekolah</a></li>
                 <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>statistik.php">Statistik PPDB</a></li>
                 <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>kontak.php">Kontak</a></li> 
                 
                 <?php if ($user_mode == 'siswa'): ?>
                    <li class="nav-item">
                        <a class="nav-link text-info fw-bold" href="<?php echo $base_url; ?>pembayaran.php">Pembayaran</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-ppdb mx-2" href="<?php echo $base_url; ?>daftar.php">FORMULIR</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-warning" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo htmlspecialchars($user_nama); // Tampilkan nama user ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>profil.php">Profil Saya</a></li> 
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo $logout_url; ?>">Logout (Siswa)</a></li>
                        </ul>
                    </li>
                 
                 <?php elseif ($user_mode == 'admin'): ?>
                    <li class="nav-item"><a class="nav-link btn btn-danger mx-2" href="<?php echo $base_url; ?>admin/admin.php">ADMIN PANEL</a></li>
                    <li class="nav-item"><a class="nav-link text-warning" href="<?php echo $logout_url; ?>">Logout (Admin)</a></li>
                 
                 <?php else: //pengunjung ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownAkun" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Akun
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownAkun">
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>ganti_akun.php">Ganti Akun / Login</a></li>
                        </ul>
                    </li>
                 <?php endif; ?>
            </ul>
          </div>
        </div>
    </nav>
</header>

<main>
    <div class="container my-4">