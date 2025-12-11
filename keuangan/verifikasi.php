<?php 
include('../includes/keuangan_header.php'); 

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    
    $pembayaran_id = $_POST['pembayaran_id'] ?? 0;
    $pendaftar_id = $_POST['pendaftar_id'] ?? 0;
    $catatan_verifikasi = $_POST['catatan'] ?? 'Verifikasi otomatis';

    if ($pembayaran_id > 0 && $pendaftar_id > 0) {
        
        $conn->begin_transaction();
        try {
            $status_pembayaran_pendaftar = '';
            $status_pembayaran_bayar = '';

            if ($_POST['action'] == 'verifikasi_lunas') {
                $status_pembayaran_pendaftar = 'Lunas';
                $status_pembayaran_bayar = 'Lunas';
                $message_type = 'success';
                $message_text = 'Pembayaran berhasil diverifikasi (LUNAS).';
            } elseif ($_POST['action'] == 'tolak_bayar') {
                $status_pembayaran_pendaftar = 'Ditolak';
                $status_pembayaran_bayar = 'Ditolak';
                $message_type = 'warning';
                $message_text = 'Pembayaran berhasil DITOLAK.';
            }

            if (!empty($status_pembayaran_bayar)) {
                $sql_bayar = "UPDATE pembayaran SET status = ?, verifikator_id = ?, tgl_verifikasi = NOW(), catatan_verifikasi = ? WHERE id = ?";
                $stmt_bayar = $conn->prepare($sql_bayar);
                $stmt_bayar->bind_param("sisi", $status_pembayaran_bayar, $verifikator_id, $catatan_verifikasi, $pembayaran_id);
                $stmt_bayar->execute();
                $sql_pendaftar = "UPDATE pendaftar SET status_pembayaran = ? WHERE id = ?";
                $stmt_pendaftar = $conn->prepare($sql_pendaftar);
                $stmt_pendaftar->bind_param("si", $status_pembayaran_pendaftar, $pendaftar_id);
                $stmt_pendaftar->execute();
                $conn->commit();
                $message = '<div class="alert alert-' . $message_type . '">' . $message_text . '</div>';
            }

        } catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            $message = '<div class="alert alert-danger">Update GAGAL: ' . $exception->getMessage() . '</div>';
        }
    }
}


$list_pembayaran = [];

$sql = "SELECT 
            p.id as pembayaran_id, 
            p.pendaftar_id, 
            p.status, 
            p.tgl_upload, 
            p.nama_pengirim, 
            p.bank_pengirim, 
            p.nominal, 
            p.url_bukti_transfer,
            pds.nama_lengkap,
            peng.email
        FROM pembayaran p
        JOIN pendaftar pft ON p.pendaftar_id = pft.id
        JOIN pendaftar_data_siswa pds ON pft.id = pds.pendaftar_id
        JOIN pengguna peng ON pft.user_id = peng.id
        ORDER BY 
            CASE WHEN p.status = 'Pending' THEN 1 ELSE 2 END, 
            p.tgl_upload ASC";

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $list_pembayaran[] = $row;
    }
}

?>

<h3 class="mb-4">Daftar Pembayaran Masuk (Verifikasi)</h3>

<?php echo $message; ?>

<div class="card shadow-sm">
    <div class="card-header bg-light">
        Daftar Keseluruhan Pembayaran
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Nama Pendaftar</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Tgl Upload</th>
                        <th>Info Bank Pengirim</th>
                        <th>Nominal</th>
                        <th>Bukti Bayar</th>
                        <th>Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($list_pembayaran)): ?>
                        <tr>
                            <td colspan="8" class="text-center">Belum ada data pembayaran masuk.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($list_pembayaran as $data) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($data['nama_lengkap']); ?></td>
                                <td><?php echo htmlspecialchars($data['email']); ?></td>
                                <td>
                                    <?php 
                                    $badge_class = 'bg-secondary';
                                    if ($data['status'] == 'Lunas') $badge_class = 'bg-success';
                                    if ($data['status'] == 'Pending') $badge_class = 'bg-warning text-dark';
                                    if ($data['status'] == 'Ditolak') $badge_class = 'bg-danger';
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars($data['status']); ?></span>
                                </td>
                                <td><?php echo date('d M Y, H:i', strtotime($data['tgl_upload'])); ?></td>
                                <td><?php echo htmlspecialchars($data['nama_pengirim']); ?> (<?php echo htmlspecialchars($data['bank_pengirim']); ?>)</td>
                                <td>Rp <?php echo number_format($data['nominal'], 0, ',', '.'); ?></td>
                                <td>
                                    <a href="<?php echo htmlspecialchars($data['url_bukti_transfer']); ?>" class="btn btn-sm btn-info" target="_blank">Lihat Bukti</a>
                                </td>
                                <td>
                                    <?php if ($data['status'] == 'Pending') : ?>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="pembayaran_id" value="<?php echo $data['pembayaran_id']; ?>">
                                            <input type="hidden" name="pendaftar_id" value="<?php echo $data['pendaftar_id']; ?>">
                                            <input type="hidden" name="catatan" value="Diverifikasi Lunas">
                                            <button type="submit" name="action" value="verifikasi_lunas" class="btn btn-sm btn-success">Verifikasi (Lunas)</button>
                                        </form>
                                        <form method="POST" class="d-inline mt-1">
                                            <input type="hidden" name="pembayaran_id" value="<?php echo $data['pembayaran_id']; ?>">
                                            <input type="hidden" name="pendaftar_id" value="<?php echo $data['pendaftar_id']; ?>">
                                            <input type="hidden" name="catatan" value="Bukti transfer tidak valid">
                                            <button type="submit" name="action" value="tolak_bayar" class="btn btn-sm btn-danger">Tolak</button>
                                        </form>
                                    <?php else : ?>
                                        <span class="text-muted fst-italic">Tindakan Selesai</span>
                                    <?php endif; ?>
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
include('../includes/keuangan_footer.php'); 
?>