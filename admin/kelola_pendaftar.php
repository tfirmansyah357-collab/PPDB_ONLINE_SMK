<?php 
include('../includes/admin_header.php');

$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
$pendaftar_data = [];
$sql = "SELECT 
            p.id, 
            p.no_pendaftaran, 
            p.status_administrasi, 
            p.status_pembayaran,
            pds.nama_lengkap,
            pas.nama_sekolah,
            j.nama as nama_jurusan
        FROM pendaftar p
        JOIN pendaftar_data_siswa pds ON p.id = pds.pendaftar_id
        JOIN pendaftar_asal_sekolah pas ON p.id = pas.pendaftar_id
        JOIN jurusan j ON p.jurusan_id = j.id
        ORDER BY p.tanggal_daftar DESC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $pendaftar_data[] = $row;
    }
}
?>

<h3 class="mb-4">Kelola Data Pendaftar</h3>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover">
        <thead class="bg-dark text-white">
            <tr>
                <th>No Pendaftaran</th>
                <th>Nama Lengkap</th>
                <th>Jurusan Saat Ini</th>
                <th>Status Bayar</th>
                <th>Status Adm.</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pendaftar_data)): ?>
                <tr><td colspan="6" class="text-center">Belum ada data pendaftar.</td></tr>
            <?php else: ?>
                <?php foreach ($pendaftar_data as $pendaftar) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($pendaftar['no_pendaftaran']); ?></td>
                        <td><?php echo htmlspecialchars($pendaftar['nama_lengkap']); ?></td>
                        <td><?php echo htmlspecialchars($pendaftar['nama_jurusan']); ?></td>
                        <td>
                            <?php 
                           
                            $badge_bayar = 'bg-secondary';
                            if ($pendaftar['status_pembayaran'] == 'Lunas') $badge_bayar = 'bg-success';
                            if ($pendaftar['status_pembayaran'] == 'Pending Verifikasi') $badge_bayar = 'bg-warning text-dark';
                            if ($pendaftar['status_pembayaran'] == 'Ditolak') $badge_bayar = 'bg-danger';
                            ?>
                            <span class="badge <?php echo $badge_bayar; ?>"><?php echo htmlspecialchars($pendaftar['status_pembayaran']); ?></span>
                        </td>
                        <td>
                             <?php
                            $badge_adm = 'bg-secondary';
                            if ($pendaftar['status_administrasi'] == 'Diverifikasi') $badge_adm = 'bg-success';
                            if ($pendaftar['status_administrasi'] == 'Pending') $badge_adm = 'bg-warning text-dark';
                            if ($pendaftar['status_administrasi'] == 'Ditolak') $badge_adm = 'bg-danger';
                            ?>
                            <span class="badge <?php echo $badge_adm; ?>"><?php echo htmlspecialchars($pendaftar['status_administrasi']); ?></span>
                        </td>
                        <td>
                            <a href="edit_pendaftar.php?id=<?php echo $pendaftar['id']; ?>" class="btn btn-sm btn-info">
                                Edit Data
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php 
$conn->close();
include('../includes/admin_footer.php'); 
?>