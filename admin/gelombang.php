<?php
include('../includes/admin_header.php');

$message = '';

if (isset($_POST['action']) && $_POST['action'] == 'hapus') {
    $id = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM gelombang WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = '<div class="alert alert-success">Data berhasil dihapus.</div>';
    } else {
        $message = '<div class="alert alert-danger">Gagal menghapus (Mungkin ada pendaftar di gelombang ini).</div>';
    }
}

if (isset($_GET['toggle_id'])) {
    $id = $_GET['toggle_id'];
    $status_sekarang = $_GET['status'];
    $status_baru = ($status_sekarang == 1) ? 0 : 1;

    $stmt = $conn->prepare("UPDATE gelombang SET aktif = ? WHERE id = ?");
    $stmt->bind_param("ii", $status_baru, $id);
    $stmt->execute();
    echo "<script>window.location='gelombang.php';</script>";
}
$data_gelombang = [];
$sql = "SELECT * FROM gelombang ORDER BY tahun DESC, tgl_mulai DESC";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $data_gelombang[] = $row;
}
?>

<h3 class="mb-4">Manajemen Tahun Ajaran & Gelombang</h3>
<?php echo $message; ?>

<a href="gelombang_tambah.php" class="btn btn-primary mb-3">+ Buat Gelombang Baru</a>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Tahun Ajaran</th>
                        <th>Nama Gelombang</th>
                        <th>Tanggal Buka</th>
                        <th>Tanggal Tutup</th>
                        <th>Biaya</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($data_gelombang)): ?>
                        <tr><td colspan="7" class="text-center">Belum ada data.</td></tr>
                    <?php else: ?>
                        <?php foreach($data_gelombang as $row): ?>
                            <tr>
                                <td class="fw-bold"><?php echo htmlspecialchars($row['tahun']); ?></td>
                                <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                <td><?php echo date('d M Y', strtotime($row['tgl_mulai'])); ?></td>
                                <td><?php echo date('d M Y', strtotime($row['tgl_selesai'])); ?></td>
                                <td>Rp <?php echo number_format($row['biaya_daftar'], 0, ',', '.'); ?></td>
                                <td>
                                    <?php if($row['aktif'] == 1): ?>
                                        <a href="gelombang.php?toggle_id=<?php echo $row['id']; ?>&status=1" class="btn btn-sm btn-success fw-bold">AKTIF</a>
                                    <?php else: ?>
                                        <a href="gelombang.php?toggle_id=<?php echo $row['id']; ?>&status=0" class="btn btn-sm btn-secondary">NON-AKTIF</a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="gelombang_edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning text-white">Edit</a>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Hapus gelombang ini?');">
                                        <input type="hidden" name="action" value="hapus">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('../includes/admin_footer.php'); ?>