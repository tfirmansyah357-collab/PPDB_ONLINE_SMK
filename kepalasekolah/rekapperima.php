<?php 
// File: ppdb/kepalasekolah/rekapperima.php
include('../includes/kepsek_header.php');

$pendaftar_diterima = [];

// Query untuk mengambil siswa yang DITERIMA FINAL
// Yaitu: status_administrasi = 'Diverifikasi' DAN status_pembayaran = 'Lunas'
$sql = "SELECT 
            p.no_pendaftaran,
            pds.nama_lengkap,
            pas.nama_sekolah,
            j.nama as nama_jurusan
        FROM pendaftar p
        JOIN pendaftar_data_siswa pds ON p.id = pds.pendaftar_id
        JOIN pendaftar_asal_sekolah pas ON p.id = pas.pendaftar_id
        JOIN jurusan j ON p.jurusan_id = j.id
        WHERE 
            p.status_administrasi = 'Diverifikasi' 
            AND p.status_pembayaran = 'Lunas'
        ORDER BY pds.nama_lengkap ASC";

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $pendaftar_diterima[] = $row;
    }
}
?>

<h3 class="mb-4 text-success">Rekap Daftar Calon Siswa Diterima (Final) ✅</h3>
<p class="text-muted">Menampilkan daftar siswa yang berkas administrasinya sudah divalidasi (oleh Admin) DAN pembayarannya sudah Lunas (oleh Keuangan).</p>

<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover">
        <thead class="bg-success text-white">
            <tr><th>No Pendaftaran</th><th>Nama Lengkap</th><th>Asal Sekolah</th><th>Pilihan Jurusan</th></tr>
        </thead>
        <tbody>
            <?php if (empty($pendaftar_diterima)): ?>
                <tr><td colspan="4" class="text-center">Belum ada siswa yang lolos seleksi final (Adm. Diverifikasi & Bayar Lunas).</td></tr>
            <?php else: ?>
                <?php foreach ($pendaftar_diterima as $pendaftar) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($pendaftar['no_pendaftaran']); ?></td>
                        <td><?php echo htmlspecialchars($pendaftar['nama_lengkap']); ?></td>
                        <td><?php echo htmlspecialchars($pendaftar['nama_sekolah']); ?></td>
                        <td><?php echo htmlspecialchars($pendaftar['nama_jurusan']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php 
$conn->close();
include('../includes/kepsek_footer.php'); 
?>