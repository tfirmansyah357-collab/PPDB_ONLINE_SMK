<?php 
session_start();
include('../includes/koneksi.php'); 
$base_url = "http://localhost/ppdb/"; 
if (!isset($_SESSION['panitia_logged_in']) || $_SESSION['panitia_logged_in'] !== true) {
    header("Location: " . $base_url . "panitia/login.php");
    exit();
}

$display_role = 'Panitia PPDB';
$display_username = $_SESSION['panitia_email'] ?? 'Panitia'; 
$panitia_id = $_SESSION['panitia_id'] ?? 0; 
$logout_url = $base_url . 'panitia/login.php?logout=true';

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $display_role; ?> Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?php echo $base_url; ?>assets/images/logo_bn1.jpeg">
    <style>
        .sidebar { 
            width: 250px; height: 100vh; background-color: #3498db;
            position: fixed; box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .sidebar .nav-link { 
            color: #ffffff; padding: 15px; border-bottom: 1px solid #2980b9; 
        }
        .sidebar .nav-link:hover { background-color: #2980b9; }
        .sidebar .nav-link.active-menu { background-color: #2980b9; font-weight: bold; }
        .dashboard-header { 
            background-color: #5dade2; color: white; padding: 20px; margin-left: 250px; 
        }
        .main-content { margin-left: 250px; padding: 20px; background-color: #f8f9fa; }
    </style>
</head>
<body>
<div class="d-flex">
    <div class="sidebar text-white p-3">
        <h4 class="text-white mb-4 border-bottom pb-2">PANEL PANITIA</h4>
        <p class="small text-white-50 mb-4">Logged in as: <?php echo htmlspecialchars($display_username); ?></p>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class.nav-link <?php echo ($current_page == 'panitia_dashboard.php' ? 'active-menu' : ''); ?>" 
                   href="panitia_dashboard.php">Verifikasi Pendaftar
                </a>
            </li>
            <li class="nav-item mt-4">
                <a class="nav-link btn btn-danger text-white" 
                   href="<?php echo $logout_url; ?>">Logout
                </a>
            </li>
        </ul>
    </div>
    <div class="main-content flex-grow-1">
        <div class="dashboard-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Verifikasi Administrasi PPDB</h4>
            <a href="<?php echo $base_url; ?>index.php" class="btn btn-sm btn-light">Kembali ke Menu Utama</a>
        </div>
        <div class="container-fluid mt-4">