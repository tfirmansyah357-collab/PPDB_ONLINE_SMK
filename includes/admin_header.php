<?php 
session_start();
if (!isset($conn)) {
    include('../includes/koneksi.php');
}
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$base_url = "http://localhost/ppdb/"; 

$display_role = 'Administrator';
$display_username = $_SESSION['admin_username'] ?? 'Admin';
$admin_id = $_SESSION['admin_id'] ?? 0;

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - PPDB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/png" href="<?php echo $base_url; ?>assets/images/logo_bn1.jpeg">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .sidebar { width: 250px; height: 100vh; background-color: #2c3e50; position: fixed; }
        .sidebar .nav-link { color: #ffffff; padding: 15px; border-bottom: 1px solid #34495e; }
        .sidebar .nav-link:hover { background-color: #34495e; }
        .sidebar .nav-link.active-menu { background-color: #34495e; font-weight: bold; border-left: 4px solid #007bff; }
        .dashboard-header { background-color: #007bff; color: white; padding: 20px; margin-left: 250px; }
        .main-content { margin-left: 250px; padding: 20px; }
    </style>
</head>
<body>

<div class="d-flex">
    <div class="sidebar text-white p-3">
        <h4 class="text-warning mb-4 border-bottom pb-2">ADMIN PANEL</h4>
        <p class="small text-muted mb-4">Logged in as: <?php echo htmlspecialchars($display_username); ?></p>
        <ul class="nav flex-column">
            
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'admin.php') ? 'active-menu' : ''; ?>" href="admin.php">Dashboard Utama</a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo (in_array($current_page, ['gelombang.php', 'gelombang_tambah.php', 'gelombang_edit.php'])) ? 'active-menu' : ''; ?>" href="gelombang.php">Atur Gelombang & TA</a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo (in_array($current_page, ['kelola_pendaftar.php', 'edit_pendaftar.php'])) ? 'active-menu' : ''; ?>" href="kelola_pendaftar.php">Kelola Pendaftar</a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo (in_array($current_page, ['master_data.php', 'jurusan_tambah.php', 'jurusan_edit.php'])) ? 'active-menu' : ''; ?>" href="master_data.php">Data Jurusan</a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'wilayah.php') ? 'active-menu' : ''; ?>" href="wilayah.php">Rekap Wilayah</a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo (in_array($current_page, ['users.php', 'user_tambah.php', 'reset_password_admin.php'])) ? 'active-menu' : ''; ?>" href="users.php">Manajemen Users</a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'laporan.php') ? 'active-menu' : ''; ?>" href="laporan.php">Laporan & Cetak</a>
            </li>
            
            <li class="nav-item mt-4">
                <a class="nav-link btn btn-danger text-white" href="login.php?logout=true">Logout</a>
            </li>
        </ul>
    </div>
    <div class="main-content flex-grow-1">
        <div class="dashboard-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">PPDB SMK BAKTI NUSANTARA 666 - Admin</h4>
            <a href="<?php echo $base_url; ?>index.php" class="btn btn-sm btn-light">Lihat Website</a>
        </div>
        <div class="container-fluid mt-4">