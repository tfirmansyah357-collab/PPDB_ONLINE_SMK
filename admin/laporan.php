<?php
// File: admin/laporan.php
include('../includes/admin_header.php');

// Ambil data siswa yang STATUSNYA DITERIMA (Bayar Lunas + Adm Diverifikasi)
$siswa_diterima = [];
$sql = "SELECT 
            p.no_pendaftaran,
            pds.nama_lengkap,
            pds.nis,
            pas.nama_sekolah,
            j.nama as jurusan
        FROM pendaftar p
        JOIN pendaftar_data_siswa pds ON p.id = pds.pendaftar_id
        JOIN pendaftar_asal_sekolah pas ON p.id = pas.pendaftar_id
        JOIN jurusan j ON p.jurusan_id = j.id
        WHERE p.status_pembayaran = 'Lunas' AND p.status_administrasi = 'Diverifikasi'
        ORDER BY j.nama ASC, pds.nama_lengkap ASC";

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $siswa_diterima[] = $row;
    }
}
?>

<h3 class="mb-4">Laporan Siswa Diterima</h3>
<p>Berikut adalah daftar siswa yang sudah lolos seleksi (Administrasi Valid & Pembayaran Lunas).</p>

<div class="card shadow-sm mb-4">
    <div class="card-body d-flex justify-content-between align-items-center">
        <span>Siap untuk dicetak?</span>
        <a href="cetak_pdf.php" target="_blank" class="btn btn-danger btn-lg">
            <i class="bi bi-file-pdf"></i> Download Laporan PDF
        </a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead class="bg-dark text-white">
            <tr>
                <th>No</th>
                <th>No Pendaftaran</th>
                <th>Nama Lengkap</th>
                <th>Asal Sekolah</th>
                <th>Jurusan</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($siswa_diterima)): ?>
                <tr><td colspan="5" class="text-center">Belum ada siswa yang diterima.</td></tr>
            <?php else: ?>
                <?php $no=1; foreach($siswa_diterima as $siswa): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $siswa['no_pendaftaran']; ?></td>
                    <td><?php echo $siswa['nama_lengkap']; ?></td>
                    <td><?php echo $siswa['nama_sekolah']; ?></td>
                    <td><?php echo $siswa['jurusan']; ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include('../includes/admin_footer.php'); ?>