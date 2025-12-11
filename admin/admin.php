<?php 
include('../includes/admin_header.php'); 
//Pendaftar
$sql_total = "SELECT COUNT(id) as total FROM pendaftar";
$result_total = $conn->query($sql_total);
$total_pendaftar = $result_total->fetch_assoc()['total'] ?? 0;

//Kuota
$sql_kuota = "SELECT SUM(kuota) as total FROM jurusan";
$result_kuota = $conn->query($sql_kuota);
$target_kuota = $result_kuota->fetch_assoc()['total'] ?? 0;

// verif admin
$sql_verif = "SELECT COUNT(id) as total FROM pendaftar WHERE status_administrasi = 'Diverifikasi'";
$result_verif = $conn->query($sql_verif);
$total_verifikasi_adm = $result_verif->fetch_assoc()['total'] ?? 0;

//rekap keuangan 
$sql_uang = "SELECT SUM(nominal) as total, COUNT(id) as jumlah_lunas FROM pembayaran WHERE status = 'Lunas'";
$result_uang = $conn->query($sql_uang);
$data_uang = $result_uang->fetch_assoc();
$total_uang_masuk_raw = $data_uang['total'] ?? 0;
$total_uang_masuk_format = "Rp " . number_format($total_uang_masuk_raw, 0, ',', '.');
$jumlah_lunas = $data_uang['jumlah_lunas'] ?? 0;
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

// ambil data perhari
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
?>

<h3 class="mb-4">Statistik Pendaftaran (Dashboard Utama)</h3>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3 shadow">
            <div class="card-header">Total Pendaftar</div>
            <div class="card-body">
                <h1 class="card-title display-4"><?php echo $total_pendaftar; ?></h1>
                <p class="card-text">Dari Kuota <?php echo $target_kuota; ?></p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card text-white bg-success mb-3 shadow">
            <div class="card-header">Adm. Diverifikasi</div>
            <div class="card-body">
                <h1 class="card-title display-4"><?php echo $total_verifikasi_adm; ?></h1>
                <p class="card-text">Siswa yang berkasnya sudah valid</p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-dark bg-warning mb-3 shadow">
            <div class="card-header">Rekap Keuangan (Lunas)</div>
            <div class="card-body">
                <h1 class="card-title display-4"><?php echo $total_uang_masuk_format; ?></h1>
                <p class="card-text">Total Uang Masuk (<?php echo $jumlah_lunas; ?> Siswa)</p>
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

<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('statistikHarianChart').getContext('2d');
    const labels = <?php echo $json_labels; ?>;
    const dataPendaftar = <?php echo $json_data_pendaftar; ?>;
    const dataDiterima = <?php echo $json_data_diterima; ?>;
// grafik data 
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
                    label: 'Jumlah Diterima',
                    data: dataDiterima,
                    backgroundColor: 'rgba(255, 205, 86, 0.7)', 
                    borderColor: 'rgba(255, 205, 86, 1)',
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
include('../includes/admin_footer.php'); 
?>