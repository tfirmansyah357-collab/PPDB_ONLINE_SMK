<?php 
session_start();
include('../includes/koneksi.php');
if (!isset($_SESSION['keuangan_logged_in']) || $_SESSION['keuangan_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$base_url = "http://localhost/ppdb/"; 
$display_username = $_SESSION['keuangan_email'] ?? 'Staf Keuangan';
$verifikator_id = $_SESSION['keuangan_id'] ?? 0; 
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Keuangan - PPDB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?php echo $base_url; ?>assets/images/logo_bn1.jpeg">
    <style>
        .sidebar { 
            width: 250px; 
            height: 100vh; 
            background-color: #198754;
            position: fixed; 
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .sidebar .nav-link { 
            color: #ffffff; 
            padding: 15px; 
            border-bottom: 1px solid #1a935c; 
        }
        .sidebar .nav-link:hover { 
            background-color: #1a935c; 
        }
        .sidebar .nav-link.active-menu {
            background-color: #1a935c;
            font-weight: bold;
        }
        .dashboard-header { 
            background-color: #20c997;
            color: white; 
            padding: 20px; 
            margin-left: 250px; 
        }
        .main-content { 
            margin-left: 250px; 
            padding: 20px; 
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>

<div class="d-flex">
    <div class="sidebar text-white p-3">
        <h4 class="text-warning mb-4 border-bottom pb-2">PANEL KEUANGAN</h4>
        <p class="small text-white-50 mb-4">Logged in as: <?php echo $display_username; ?></p>
        <ul class="nav flex-column">
            
            <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'verifikasi.php' ? 'active-menu' : ''); ?>" 
                href="verifikasi.php">Verifikasi Pembayaran</a></li>
            
            <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'rekap.php' ? 'active-menu' : ''); ?>" 
                href="rekap.php">Rekapitulasi Keuangan</a></li>
            
            <li class="nav-item mt-4">
                <a class="nav-link btn btn-danger text-white" 
                   href="login.php?logout=true">
                    Logout (Keuangan)
                </a>
            </li>
        </ul>
    </div>
    <div class="main-content flex-grow-1">
        <div class="dashboard-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Manajemen Keuangan PPDB</h4>
            <a href="<?php echo $base_url; ?>index.php" class="btn btn-sm btn-light">Kembali ke Menu Utama</a>
        </div>
        <div class="container-fluid mt-4">