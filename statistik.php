<?php 
session_start(); 
include('includes/koneksi.php'); 
include('includes/header.php'); 

//total pendaftar
$sql_pendaftar = "SELECT COUNT(id) as total FROM pendaftar";
$result_pendaftar = $conn->query($sql_pendaftar);
$total_pendaftar = $result_pendaftar->fetch_assoc()['total'] ?? 0;

//total kuota
$sql_kuota = "SELECT SUM(kuota) as total FROM jurusan";
$result_kuota = $conn->query($sql_kuota);
$total_kuota = $result_kuota->fetch_assoc()['total'] ?? 1;
if ($total_kuota == 0) $total_kuota = 1;

//untuk presentase
$persentase_terisi = round(($total_pendaftar / $total_kuota) * 100);
//ambil statistik jurusan
$stats_jurusan = [];
$sql_stats_jurusan = "SELECT 
                        j.nama,
                        (SELECT COUNT(p.id) FROM pendaftar p WHERE p.jurusan_id = j.id) as pendaftar
                    FROM jurusan j
                    ORDER BY pendaftar DESC";
$result_stats_jurusan = $conn->query($sql_stats_jurusan);
if ($result_stats_jurusan->num_rows > 0) {
    while($row = $result_stats_jurusan->fetch_assoc()) {
        $stats_jurusan[] = $row;
    }
}
//pengambilan data
?>

<h2 class="text-primary mb-4">Statistik Pendaftaran Siswa Baru (TA 2025/2026)</h2>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card shadow-sm h-100 border-primary">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Progres Pengisian Kuota</h5>
            </div>
            <div class="card-body">
                <h1 class="display-4 text-primary"><?php echo $total_pendaftar; ?></h1>
                <p class="lead">Siswa telah mendaftar dari total kuota <?php echo $total_kuota; ?>.</p>
                
                <p class="fw-bold mt-3">Telah Terisi: <?php echo $persentase_terisi; ?>%</p>
                <div class="progress" style="height: 30px;">
                    <div class="progress-bar progress-bar-striped bg-info" role="progressbar" 
                         style="width: <?php echo $persentase_terisi; ?>%;" 
                         aria-valuenow="<?php echo $persentase_terisi; ?>" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                         <?php echo $total_pendaftar; ?>/<?php echo $total_kuota; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm h-100 border-primary">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Peminat Jurusan (Top 3)</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php if (empty($stats_jurusan)): ?>
                        <li class="list-group-item">Belum ada pendaftar.</li>
                    <?php else: ?>
                        <?php foreach ($stats_jurusan as $jurusan): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo htmlspecialchars($jurusan['nama']); ?>
                                <span class="badge bg-primary rounded-pill"><?php echo $jurusan['pendaftar']; ?> Siswa</span>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
                <p class="mt-4 text-muted fst-italic">Statistik ini diperbarui secara real-time dari database.</p>
            </div>
        </div>
    </div>
</div>
<?php 
$conn->close();
include('includes/footer.php'); 
?>