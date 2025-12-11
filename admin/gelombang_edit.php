<?php
include('../includes/admin_header.php');

$id = $_GET['id'] ?? 0;
$data = $conn->query("SELECT * FROM gelombang WHERE id = $id")->fetch_assoc();

if (!$data) {
    header("Location: gelombang.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tahun = $_POST['tahun'];
    $nama = $_POST['nama'];
    $tgl_mulai = $_POST['tgl_mulai'];
    $tgl_selesai = $_POST['tgl_selesai'];
    $biaya = $_POST['biaya'];
    
    $stmt = $conn->prepare("UPDATE gelombang SET nama=?, tahun=?, tgl_mulai=?, tgl_selesai=?, biaya_daftar=? WHERE id=?");
    $stmt->bind_param("sssssi", $nama, $tahun, $tgl_mulai, $tgl_selesai, $biaya, $id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Berhasil diupdate!'); window.location='gelombang.php';</script>";
    } else {
        echo "<div class='alert alert-danger'>Gagal update.</div>";
    }
}
?>

<h3 class="mb-4">Edit Gelombang</h3>

<div class="card shadow-sm" style="max-width: 600px;">
    <div class="card-body">
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Tahun Ajaran</label>
                <input type="text" name="tahun" class="form-control" value="<?php echo htmlspecialchars($data['tahun']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nama Gelombang</label>
                <input type="text" name="nama" class="form-control" value="<?php echo htmlspecialchars($data['nama']); ?>" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Buka</label>
                    <input type="date" name="tgl_mulai" class="form-control" value="<?php echo $data['tgl_mulai']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Tutup</label>
                    <input type="date" name="tgl_selesai" class="form-control" value="<?php echo $data['tgl_selesai']; ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Biaya Pendaftaran (Rp)</label>
                <input type="number" name="biaya" class="form-control" value="<?php echo $data['biaya_daftar']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Data</button>
            <a href="gelombang.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
<?php include('../includes/admin_footer.php'); ?>