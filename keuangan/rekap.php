<?php 

include('../includes/keuangan_header.php'); 


$total_lunas = 0;
$total_rupiah_masuk = 0;
$total_pending = 0;


$sql_lunas = "SELECT COUNT(id) as total, SUM(nominal) as total_rupiah FROM pembayaran WHERE status = 'Lunas'";
$result_lunas = $conn->query($sql_lunas);
if ($result_lunas->num_rows > 0) {
    $data_lunas = $result_lunas->fetch_assoc();
    $total_lunas = $data_lunas['total'] ?? 0;
    $total_rupiah_masuk = $data_lunas['total_rupiah'] ?? 0;
}


$sql_pending = "SELECT COUNT(id) as total FROM pembayaran WHERE status = 'Pending'";
$result_pending = $conn->query($sql_pending);
if ($result_pending->num_rows > 0) {
    $total_pending = $result_pending->fetch_assoc()['total'] ?? 0;
}


?>

<h3 class="mb-4">Rekapitulasi Keuangan PPDB</h3>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card text-white bg-success shadow">
            <div class="card-header">Total Pembayaran Lunas</div>
            <div class="card-body">
                <h1 class="card-title display-4"><?php echo $total_lunas; ?></h1>
                <p class="card-text">Siswa telah lunas</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card text-dark bg-warning shadow">
            <div class="card-header">Total Pembayaran Pending</div>
            <div class="card-body">
                <h1 class="card-title display-4"><?php echo $total_pending; ?></h1>
                <p class="card-text">Siswa menunggu verifikasi</p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-white bg-primary shadow">
            <div class="card-header">Total Dana Masuk (Lunas)</div>
            <div class="card-body">
                <h1 class="card-title display-5">Rp <?php echo number_format($total_rupiah_masuk, 0, ',', '.'); ?></h1>
                <p class="card-text">Dari <?php echo $total_lunas; ?> siswa lunas</p>
            </div>
        </div>
    </div>
</div>


<?php 
$conn->close();
include('../includes/keuangan_footer.php'); 
?>