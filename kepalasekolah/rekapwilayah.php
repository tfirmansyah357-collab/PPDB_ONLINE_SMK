<?php 
include('../includes/kepsek_header.php');
$wilayah_rekap = [];
$total_pendaftar_wilayah = 0;
$sql = "SELECT 
            rwa.kelompok, 
            COUNT(p.id) as total
        FROM ref_wilayah_asal rwa
        LEFT JOIN pendaftar_data_siswa pds ON rwa.id = pds.wilayah_asal_id
        LEFT JOIN pendaftar p ON pds.pendaftar_id = p.id
        GROUP BY rwa.kelompok
        ORDER BY total DESC";

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $wilayah_rekap[] = $row;
        $total_pendaftar_wilayah += $row['total'];
    }
}
$list_siswa_wilayah = [];
$sql_detail = "SELECT 
                p.no_pendaftaran,
                pds.nama_lengkap,
                rwa.nama_wilayah,
                rwa.kelompok
               FROM pendaftar p
               JOIN pendaftar_data_siswa pds ON p.id = pds.pendaftar_id
               JOIN ref_wilayah_asal rwa ON pds.wilayah_asal_id = rwa.id
               ORDER BY rwa.kelompok ASC, pds.nama_lengkap ASC";

$result_detail = $conn->query($sql_detail);
if ($result_detail->num_rows > 0) {
    while($row_detail = $result_detail->fetch_assoc()) {
        $list_siswa_wilayah[] = $row_detail;
    }
}
?>

<h3 class="mb-4 text-secondary">Rekapitulasi Asal Sekolah Berdasarkan Wilayah 📍</h3>
<p class="text-muted">Data ini penting untuk analisis pemasaran PPDB tahun berikutnya.</p>

<div class="card shadow-sm mb-5">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">Ringkasan Statistik</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-sm mb-0">
                <thead class="bg-light">
                    <tr><th>Wilayah Asal</th><th>Total Pendaftar</th><th>Persentase</th></tr>
                </thead>
                <tbody>
                    <?php if ($total_pendaftar_wilayah == 0): ?>
                        <tr><td colspan="3" class="text-center">Belum ada data pendaftar.</td></tr>
                    <?php else: ?>
                        <?php foreach ($wilayah_rekap as $data) : ?>
                            <?php 
                            $persentase = ($total_pendaftar_wilayah > 0) ? round(($data['total'] / $total_pendaftar_wilayah) * 100, 2) : 0;
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($data['kelompok']); ?></td>
                                <td><?php echo htmlspecialchars($data['total']); ?></td>
                                <td><?php echo $persentase; ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer text-muted small">
            Total Seluruh Pendaftar: <strong><?php echo $total_pendaftar_wilayah; ?></strong> siswa.
        </div>
    </div>
</div>

<h4 class="mb-3 text-secondary">Detail Data Siswa per Wilayah</h4>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th>No Pendaftaran</th>
                        <th>Nama Lengkap Siswa</th>
                        <th>Kota/Kabupaten Asal</th>
                        <th>Kelompok Wilayah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($list_siswa_wilayah)): ?>
                        <tr><td colspan="4" class="text-center">Belum ada data siswa.</td></tr>
                    <?php else: ?>
                        <?php foreach ($list_siswa_wilayah as $siswa) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($siswa['no_pendaftaran']); ?></td>
                                <td><?php echo htmlspecialchars($siswa['nama_lengkap']); ?></td>
                                <td><?php echo htmlspecialchars($siswa['nama_wilayah']); ?></td>
                                <td>
                                    <?php 
                                    $badge_color = 'bg-light text-dark border';
                                    if($siswa['kelompok'] == 'Bandung Raya') $badge_color = 'bg-primary text-white';
                                    elseif($siswa['kelompok'] == 'Luar Bandung') $badge_color = 'bg-warning text-dark';
                                    elseif($siswa['kelompok'] == 'Luar Jawa Barat') $badge_color = 'bg-danger text-white';
                                    ?>
                                    <span class="badge <?php echo $badge_color; ?>">
                                        <?php echo htmlspecialchars($siswa['kelompok']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
$conn->close();
include('../includes/kepsek_footer.php'); 
?>