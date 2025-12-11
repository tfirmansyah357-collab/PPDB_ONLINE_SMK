<?php 
include('../includes/kepsek_header.php');
$total_lunas = 0;
$total_rupiah_masuk = 0;
$total_pending = 0;

// data lunas
$sql_lunas = "SELECT COUNT(id) as total, SUM(nominal) as total_rupiah FROM pembayaran WHERE status = 'Lunas'";
$result_lunas = $conn->query($sql_lunas);
if ($result_lunas->num_rows > 0) {
    $data_lunas = $result_lunas->fetch_assoc();
    $total_lunas = $data_lunas['total'] ?? 0;
    $total_rupiah_masuk = $data_lunas['total_rupiah'] ?? 0;
}
// data pending
$sql_pending = "SELECT COUNT(id) as total FROM pendaftar WHERE status_pembayaran = 'Pending Verifikasi' OR status_pembayaran = 'Belum Bayar'";
$result_pending = $conn->query($sql_pending);
if ($result_pending->num_rows > 0) {
    $total_pending = $result_pending->fetch_assoc()['total'] ?? 0;
}
$list_pembayaran = [];
$sql_detail = "SELECT 
                  p.no_pendaftaran,
                  pds.nama_lengkap,
                  j.nama as nama_jurusan,
                  p.status_pembayaran,
                  pemb.nominal,
                  pemb.tgl_verifikasi
              FROM pendaftar p
              JOIN pendaftar_data_siswa pds ON p.id = pds.pendaftar_id
              JOIN jurusan j ON p.jurusan_id = j.id
              LEFT JOIN pembayaran pemb ON p.id = pemb.pendaftar_id
              ORDER BY 
                  CASE 
                      WHEN p.status_pembayaran = 'Pending Verifikasi' THEN 1
                      WHEN p.status_pembayaran = 'Belum Bayar' THEN 2
                      WHEN p.status_pembayaran = 'Ditolak' THEN 3
                      WHEN p.status_pembayaran = 'Lunas' THEN 4
                  END ASC, 
                  p.tanggal_daftar ASC";

$result_detail = $conn->query($sql_detail);
if ($result_detail->num_rows > 0) {
    while($row = $result_detail->fetch_assoc()) {
        $list_pembayaran[] = $row;
    }
}
?>

<h3 class="mb-4 text-primary">Rekapitulasi Pembayaran & Keuangan 💵</h3>
<p class="text-muted">Menampilkan data yang divalidasi oleh Staf Keuangan.</p>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card text-white bg-success mb-3 shadow">
            <div class="card-header">Total Pembayaran Lunas</div>
            <div class="card-body">
                <h1 class="card-title display-4"><?php echo $total_lunas; ?></h1>
                <p class="card-text">Dari <?php echo $total_lunas; ?> Pendaftar</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-danger mb-3 shadow">
            <div class="card-header">Pembayaran Belum Lunas</div>
            <div class="card-body">
                <h1 class="card-title display-4"><?php echo $total_pending; ?></h1>
                <p class="card-text">Pendaftar (Belum Bayar / Pending)</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-light p-3 shadow border-primary">
            <h6 class="text-primary">Ringkasan Total Dana Masuk (Lunas)</h6>
            <h1 class="display-5 text-primary">Rp <?php echo number_format($total_rupiah_masuk, 0, ',', '.'); ?></h1>
        </div>
    </div>
</div>


<h4 class="mt-5">Laporan Detail Status Pembayaran Siswa</h4>
<div class="card shadow-sm mt-3">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead class="bg-info text-white">
                    <tr>
                        <th>No Pendaftaran</th>
                        <th>Nama Siswa</th>
                        <th>Jurusan</th>
                        <th>Nominal Bayar</th>
                        <th>Status Pembayaran</th>
                        <th>Tgl. Verifikasi Lunas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($list_pembayaran)): ?>
                        <tr><td colspan="6" class="text-center">Belum ada data pendaftar.</td></tr>
                    <?php else: ?>
                        <?php foreach ($list_pembayaran as $data): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($data['no_pendaftaran']); ?></td>
                                <td><?php echo htmlspecialchars($data['nama_lengkap']); ?></td>
                                <td><?php echo htmlspecialchars($data['nama_jurusan']); ?></td>
                                <td>
                                    <?php echo $data['nominal'] ? 'Rp ' . number_format($data['nominal'], 0, ',', '.') : '-'; ?>
                                </td>
                                <td>
                                    <?php
                                    $status_bayar = $data['status_pembayaran'];
                                    $badge_bayar = '';
                                    if ($status_bayar == 'Lunas') $badge_bayar = 'bg-success';
                                    elseif ($status_bayar == 'Pending Verifikasi') $badge_bayar = 'bg-warning text-dark';
                                    elseif ($status_bayar == 'Ditolak') $badge_bayar = 'bg-danger';
                                    else $badge_bayar = 'bg-secondary'; // Untuk 'Belum Bayar'
                                    ?>
                                    <span class="badge <?php echo $badge_bayar; ?>"><?php echo htmlspecialchars($status_bayar); ?></span>
                                </td>
                                <td>
                                    <?php echo $data['tgl_verifikasi'] ? date('d M Y', strtotime($data['tgl_verifikasi'])) : '-'; ?>
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