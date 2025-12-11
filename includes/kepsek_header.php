<?php 
session_start();
include('../includes/koneksi.php'); 
$base_url = "http://localhost/ppdb/"; 
if (!isset($_SESSION['user_mode']) || $_SESSION['user_mode'] !== 'kepsek') {
    header("Location: " . $base_url . "kepalasekolah/login.php");
    exit();
}

$display_role = 'Kepala Sekolah';
$display_username = $_SESSION['user_email'] ?? 'Kepala Sekolah'; 
$kepsek_id = $_SESSION['user_id'] ?? 0;
$logout_url = $base_url . 'kepalasekolah/login.php?logout=true';

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $display_role; ?> Panel - PPDB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?php echo $base_url; ?>assets/images/logo_bn1.jpeg">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar { 
            width: 250px; height: 100vh; background-color: #f39c12; 
            position: fixed; box-shadow: 2px 0 5px rgba(0,0,0,0.1); z-index: 1000;
        }
        .sidebar .nav-link { 
            color: #ffffff; padding: 15px; border-bottom: 1px solid #e67e22; 
        }
        .sidebar .nav-link:hover { background-color: #e67e22; }
        .sidebar .nav-link.active-menu { background-color: #e67e22; font-weight: bold; }
        .dashboard-header { 
            background-color: #ffc107; color: black; padding: 20px; 
            margin-left: 250px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            position: sticky; top: 0; z-index: 999;
        }
        .main-content { 
            margin-left: 250px; padding: 0; min-height: 100vh; background-color: #f8f9fa;
        }
        .card-header { font-weight: bold; }
    </style>
</head>
<body>

<div class="d-flex">
    <div class="sidebar p-3">
        <h4 class="text-white mb-4 border-bottom pb-2">PANEL KEPALA SEKOLAH</h4>
        <p class="small text-white mb-4">Logged in as: <?php echo htmlspecialchars($display_username); ?></p>
        <ul class="nav flex-column">
            
            <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'kepsek_dashboard.php' ? 'active-menu' : ''); ?>" 
                href="<?php echo $base_url; ?>kepalasekolah/kepsek_dashboard.php">
                Dashboard Monitoring
            </a></li>
            
            <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'rekapperima.php' ? 'active-menu' : ''); ?>" 
                href="<?php echo $base_url; ?>kepalasekolah/rekapperima.php">
                Daftar Siswa Diterima
            </a></li>

            <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'rekapkeuangan.php' ? 'active-menu' : ''); ?>" 
                href="<?php echo $base_url; ?>kepalasekolah/rekapkeuangan.php">
                Rekap Pembayaran
            </a></li>
            
            <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'rekapwilayah.php' ? 'active-menu' : ''); ?>" 
                href="<?php echo $base_url; ?>kepalasekolah/rekapwilayah.php">
                Rekap Wilayah Asal
            </a></li>
            
            <li class="nav-item mt-4">
                <a class="nav-link btn btn-danger text-white" 
                   href="<?php echo $logout_url; ?>">
                    Logout (Kepsek)
                </a>
            </li>
        </ul>
    </div>
    <div class="main-content flex-grow-1">
        <div class="dashboard-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Akses <?php echo $display_role; ?></h5>
            <small class="text-muted">PPDB TA 2025/2026</small>
        </div>
        <div class="container-fluid mt-4">