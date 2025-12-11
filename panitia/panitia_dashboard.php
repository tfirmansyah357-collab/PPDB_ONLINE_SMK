<?php 
include('../includes/panitia_header.php'); 

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    
    $pendaftar_id = $_POST['pendaftar_id'] ?? 0;
    $catatan_adm = $_POST['catatan_adm'] ?? 'Verifikasi otomatis';
    if ($pendaftar_id > 0 && $panitia_id > 0) {
        
        $status_administrasi = '';
        if ($_POST['action'] == 'verifikasi_adm') {
            $status_administrasi = 'Diverifikasi';
            $message = '<div class="alert alert-success">Berkas siswa berhasil DIVERIFIKASI.</div>';
        } elseif ($_POST['action'] == 'tolak_adm') {
            $status_administrasi = 'Ditolak';
            $message = '<div class="alert alert-danger">Berkas siswa berhasil DITOLAK.</div>';
        }
        
        if (!empty($status_administrasi)) {
            $sql_update = "UPDATE pendaftar SET status_administrasi = ?, admin_verifikator_id = ?, tgl_verifikasi_adm = NOW(), catatan_adm = ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("sisi", $status_administrasi, $panitia_id, $catatan_adm, $pendaftar_id);
            
            if (!$stmt_update->execute()) {
                 $message = '<div class="alert alert-danger">Update GAGAL: ' . $conn->error . '</div>';
            }
        }
    } elseif ($panitia_id == 0) {
        $message = '<div class="alert alert-danger">Sesi Anda tidak valid. Silakan login ulang.</div>';
    }
}

$pendaftar_data = [];
$sql = "SELECT 
            p.id, 
            p.no_pendaftaran, 
            p.status_administrasi, 
            p.status_pembayaran,
            pds.nama_lengkap,
            pas.nama_sekolah,
            j.kode as kode_jurusan
        FROM pendaftar p
        JOIN pendaftar_data_siswa pds ON p.id = pds.pendaftar_id
        JOIN pendaftar_asal_sekolah pas ON p.id = pas.pendaftar_id
        JOIN jurusan j ON p.jurusan_id = j.id
        ORDER BY p.tanggal_daftar DESC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $berkas_pendaftar = [];
        $sql_berkas = "SELECT jenis, url_file FROM pendaftar_berkas WHERE pendaftar_id = ?";
        $stmt_berkas = $conn->prepare($sql_berkas);
        $stmt_berkas->bind_param("i", $row['id']);
        $stmt_berkas->execute();
        $result_berkas = $stmt_berkas->get_result();
        while($row_berkas = $result_berkas->fetch_assoc()) {
            $berkas_pendaftar[] = $row_berkas;
        }
        $row['berkas'] = $berkas_pendaftar;
        $pendaftar_data[] = $row;
    }
}
?>

<h3 class="mb-4">Daftar Pendaftar & Validasi Berkas Administrasi</h3>
<?php echo $message; ?>

<div class.table-responsive>
    <table class="table table-striped table-bordered table-hover">
        <thead class="bg-primary text-white">
            <tr>
                <th>No Pendaftaran</th>
                <th>Nama Lengkap</th>
                <th>Jurusan</th>
                <th>Status Bayar</th>
                <th>Status Adm.</th>
                <th>Berkas</th>
                <th>Aksi Verifikasi Berkas</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pendaftar_data)): ?>
                <tr><td colspan="7" class="text-center">Belum ada data pendaftar.</td></tr>
            <?php else: ?>
                <?php foreach ($pendaftar_data as $pendaftar) : ?>   
                    <tr>
                        <td><?php echo htmlspecialchars($pendaftar['no_pendaftaran']); ?></td>
                        <td><?php echo htmlspecialchars($pendaftar['nama_lengkap']); ?></td>
                        <td><?php echo htmlspecialchars($pendaftar['kode_jurusan']); ?></td>
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
                            <?php foreach($pendaftar['berkas'] as $berkas): ?>
                                <a href="<?php echo htmlspecialchars($berkas['url_file']); ?>" target="_blank" class="btn btn-sm btn-outline-info mb-1">
                                    <?php echo htmlspecialchars($berkas['jenis']); ?>
                                </a>
                            <?php endforeach; ?>
                        </td>
                        <td>
                            <?php if ($pendaftar['status_administrasi'] == 'Pending') : ?>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="pendaftar_id" value="<?php echo $pendaftar['id']; ?>">
                                    <input type="hidden" name="catatan_adm" value="Berkas valid">
                                    <button type="submit" name="action" value="verifikasi_adm" class="btn btn-sm btn-success">Verifikasi (ADM)</button>
                                </form>
                                <form method="POST" class="d-inline mt-1">
                                    <input type="hidden" name="pendaftar_id" value="<?php echo $pendaftar['id']; ?>">
                                    <input type="hidden" name="catatan_adm" value="Berkas tidak lengkap">
                                    <button type="submit" name="action" value="tolak_adm" class="btn btn-sm btn-danger">Tolak (ADM)</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted fst-italic">Tindakan Selesai</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php 
$conn->close();
include('../includes/panitia_footer.php');
?>