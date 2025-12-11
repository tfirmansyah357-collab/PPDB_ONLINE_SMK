<?php
include('../includes/admin_header.php');

$id = $_GET['id'] ?? 0;
$data = null;

// Ambil data lama
if ($id > 0) {
    $stmt = $conn->prepare("SELECT * FROM jurusan WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
}

if (!$data) {
    echo '<div class="alert alert-danger">Data jurusan tidak ditemukan. <a href="master_data.php">Kembali</a></div>';
    include('../includes/admin_footer.php');
    exit();
}

$message = '';

// Proses Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kode = strtoupper($_POST['kode']);
    $nama = $_POST['nama'];
    $kuota = $_POST['kuota'];
    $cek = $conn->query("SELECT id FROM jurusan WHERE kode = '$kode' AND id != $id");
    if ($cek->num_rows > 0) {
        $message = '<div class="alert alert-danger">Gagal! Kode jurusan sudah digunakan jurusan lain.</div>';
    } else {
        $stmt = $conn->prepare("UPDATE jurusan SET kode = ?, nama = ?, kuota = ? WHERE id = ?");
        $stmt->bind_param("ssii", $kode, $nama, $kuota, $id);
        
        if ($stmt->execute()) {
            echo "<script>alert('Data jurusan berhasil diperbarui!'); window.location='master_data.php';</script>";
            exit();
        } else {
            $message = '<div class="alert alert-danger">Gagal menyimpan: ' . $conn->error . '</div>';
        }
        $stmt->close();
    }
}
?>

<h3 class="mb-4">Edit Data Jurusan</h3>

<?php echo $message; ?>

<div class="card shadow-sm" style="max-width: 600px;">
    <div class="card-body">
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Kode Jurusan</label>
                <input type="text" class="form-control" name="kode" value="<?php echo htmlspecialchars($data['kode']); ?>" required maxlength="10">
            </div>
            <div class="mb-3">
                <label class="form-label">Nama Lengkap Jurusan</label>
                <input type="text" class="form-control" name="nama" value="<?php echo htmlspecialchars($data['nama']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Total Kuota Siswa</label>
                <input type="number" class="form-control" name="kuota" value="<?php echo htmlspecialchars($data['kuota']); ?>" required min="1">
            </div>
            <button type="submit" class="btn btn-info text-white">Update Data</button>
            <a href="master_data.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<?php include('../includes/admin_footer.php'); ?>