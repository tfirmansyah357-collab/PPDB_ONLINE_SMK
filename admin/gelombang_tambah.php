<?php
include('../includes/admin_header.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tahun = $_POST['tahun'];
    $nama = $_POST['nama'];
    $tgl_mulai = $_POST['tgl_mulai'];
    $tgl_selesai = $_POST['tgl_selesai'];
    $biaya = $_POST['biaya'];
    
    $stmt = $conn->prepare("INSERT INTO gelombang (nama, tahun, tgl_mulai, tgl_selesai, biaya_daftar, aktif) VALUES (?, ?, ?, ?, ?, 1)");
    $stmt->bind_param("sssss", $nama, $tahun, $tgl_mulai, $tgl_selesai, $biaya);
    
    if ($stmt->execute()) {
        echo "<script>alert('Berhasil disimpan!'); window.location='gelombang.php';</script>";
    } else {
        echo "<div class='alert alert-danger'>Gagal menyimpan.</div>";
    }
}
?>

<h3 class="mb-4">Tambah Gelombang Baru</h3>

<div class="card shadow-sm" style="max-width: 600px;">
    <div class="card-body">
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Tahun Ajaran</label>
                <input type="text" name="tahun" class="form-control" placeholder="Contoh: 2025/2026" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nama Gelombang</label>
                <input type="text" name="nama" class="form-control" placeholder="Contoh: Gelombang 1" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Buka</label>
                    <input type="date" name="tgl_mulai" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Tutup</label>
                    <input type="date" name="tgl_selesai" class="form-control" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Biaya Pendaftaran (Rp)</label>
                <input type="number" name="biaya" class="form-control" value="550000" required>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="gelombang.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
<?php include('../includes/admin_footer.php'); ?>