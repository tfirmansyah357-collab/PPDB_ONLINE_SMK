<?php 
include('../includes/kepsek_header.php'); 
// pendaftar
$sql_total = "SELECT COUNT(id) as total FROM pendaftar";
$result_total = $conn->query($sql_total);
$total_pendaftar = $result_total->fetch_assoc()['total'] ?? 0;

// target Kuota
$sql_kuota = "SELECT SUM(kuota) as total FROM jurusan";
$result_kuota = $conn->query($sql_kuota);
$target_kuota = $result_kuota->fetch_assoc()['total'] ?? 0;

$persentase_kuota = ($target_kuota > 0) ? round(($total_pendaftar / $target_kuota) * 100) : 0;
// rekap keuangan
$sql_uang = "SELECT SUM(nominal) as total FROM pembayaran WHERE status = 'Lunas'";
$result_uang = $conn->query($sql_uang);
$total_uang_masuk_raw = $result_uang->fetch_assoc()['total'] ?? 0;
$total_uang_masuk = "Rp " . number_format($total_uang_masuk_raw, 0, ',', '.');
// lunas / belum
$sql_diterima = "SELECT COUNT(id) as total 
                 FROM pendaftar 
                 WHERE status_administrasi = 'Diverifikasi' 
                 AND status_pembayaran = 'Lunas'";
$result_diterima = $conn->query($sql_diterima);
$total_diterima = $result_diterima->fetch_assoc()['total'] ?? 0;
$chart_labels = [];
$chart_data_pendaftar_raw = [];
$chart_data_diterima_raw = [];

for ($i = 6; $i >= 0; $i--) {
    $date = (new DateTime())->modify("-$i days");
    $date_string = $date->format('Y-m-d');
    $chart_labels[] = $date_string; 
    $chart_data_pendaftar_raw[$date_string] = 0; 
    $chart_data_diterima_raw[$date_string] = 0; 
}

// data per hari
$sql_chart_1 = "SELECT DATE(tanggal_daftar) as tanggal, COUNT(id) as jumlah 
                FROM pendaftar 
                WHERE tanggal_daftar >= CURDATE() - INTERVAL 6 DAY 
                GROUP BY DATE(tanggal_daftar)";
$result_chart_1 = $conn->query($sql_chart_1);
if ($result_chart_1->num_rows > 0) {
    while($row = $result_chart_1->fetch_assoc()) {
        if (isset($chart_data_pendaftar_raw[$row['tanggal']])) {
            $chart_data_pendaftar_raw[$row['tanggal']] = $row['jumlah'];
        }
    }
}
//data verif
$sql_chart_2 = "SELECT DATE(tgl_verifikasi_adm) as tanggal, COUNT(id) as jumlah 
                FROM pendaftar 
                WHERE status_administrasi = 'Diverifikasi' AND status_pembayaran = 'Lunas'
                AND tgl_verifikasi_adm >= CURDATE() - INTERVAL 6 DAY 
                GROUP BY DATE(tgl_verifikasi_adm)";
$result_chart_2 = $conn->query($sql_chart_2);
if ($result_chart_2->num_rows > 0) {
    while($row = $result_chart_2->fetch_assoc()) {
        if (isset($chart_data_diterima_raw[$row['tanggal']])) {
            $chart_data_diterima_raw[$row['tanggal']] = $row['jumlah'];
        }
    }
}

$json_labels = json_encode(array_values($chart_labels));
$json_data_pendaftar = json_encode(array_values($chart_data_pendaftar_raw));
$json_data_diterima = json_encode(array_values($chart_data_diterima_raw));
$sql_overload = "SELECT 
                    j.nama, 
                    j.kuota,
                    (SELECT COUNT(p.id) FROM pendaftar p WHERE p.jurusan_id = j.id) as pendaftar
                FROM jurusan j
                HAVING pendaftar > j.kuota
                ORDER BY pendaftar DESC
                LIMIT 1"; 

$result_overload = $conn->query($sql_overload);
$data_overload = $result_overload->fetch_assoc();
?>

<h3 class="mb-4 text-warning">Dashboard Monitoring Kepala Sekolah 📊</h3>

<div class="alert alert-warning border-0" role="alert">
  <strong>Selamat datang, Kepala Sekolah!</strong> Anda dapat memantau progres PPDB dari sini.
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card text-white bg-info mb-3 shadow">
            <div class="card-header">Total Pendaftar</div>
            <div class="card-body">
                <h1 class="card-title display-4"><?php echo $total_pendaftar; ?></h1>
                <p class="card-text">Telah mencapai <?php echo $persentase_kuota; ?>% dari target kuota (<?php echo $target_kuota; ?>).</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-dark bg-light mb-3 shadow border-success">
            <div class="card-header">Siswa Diterima (Final)</div>
            <div class="card-body">
                <h1 class="card-title display-4"><?php echo $total_diterima; ?></h1>
                <p class="card-text">Siap untuk pengumuman kelulusan.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-dark bg-warning mb-3 shadow">
            <div class="card-header">Rekap Keuangan (Lunas)</div>
            <div class="card-body">
                <h1 class="card-title display-4"><?php echo $total_uang_masuk; ?></h1>
                <p class="card-text">Total dana pendaftaran masuk (Lunas).</p>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mt-5">
    <div class="card-header bg-light">
        <h5 class="mb-0 text-primary">Statistik Pendaftaran 7 Hari Terakhir</h5>
    </div>
    <div class="card-body">
        <canvas id="statistikHarianChart"></canvas>
    </div>
</div>

<?php if ($data_overload)://peringatan taerlalu penuh?>
<h4 class="mt-5 text-danger">⚠️ Isu Prioritas (Kuota Overload)</h4>
<div class="card shadow">
    <div class="card-body">
        <p class="lead fw-bold">Jurusan: <?php echo htmlspecialchars($data_overload['nama']); ?></p>
        <p>Telah terjadi kelebihan pendaftar. Kuota <strong><?php echo $data_overload['kuota']; ?></strong> terisi oleh <strong><?php echo $data_overload['pendaftar']; ?></strong> siswa. Perlu dipertimbangkan penambahan kuota atau seleksi ketat.</p>
        <a href="rekapperima.php" class="btn btn-sm btn-success">Lihat Siswa Diterima</a>
    </div>
</div>
<?php endif; ?>


<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('statistikHarianChart').getContext('2d');
    
    const labels = <?php echo $json_labels; ?>;
    const dataPendaftar = <?php echo $json_data_pendaftar; ?>;
    const dataDiterima = <?php echo $json_data_diterima; ?>;
   
// grafik

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Jumlah Pendaftar',
                    data: dataPendaftar,
                    backgroundColor: 'rgba(255, 159, 64, 0.7)', 
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Jumlah Diterima (Final)',
                    data: dataDiterima,
                    backgroundColor: 'rgba(75, 192, 192, 0.7)', 
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            },
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Grafik Pendaftar vs Diterima (Per Hari)'
                }
            }
        }
    });
});
</script>

<?php 
$conn->close();
include('../includes/kepsek_footer.php'); 
?>