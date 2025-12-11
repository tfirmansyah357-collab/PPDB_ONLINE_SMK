<?php 
include('../includes/admin_header.php');

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'hapus') {
    $id_hapus = $_POST['id_hapus'];
    $cek_siswa = $conn->query("SELECT count(*) as total FROM pendaftar WHERE jurusan_id = $id_hapus");
    $data_cek = $cek_siswa->fetch_assoc();
    
    if ($data_cek['total'] > 0) {
        $message = '<div class="alert alert-danger">Gagal menghapus! Masih ada ' . $data_cek['total'] . ' siswa yang mendaftar di jurusan ini. Pindahkan mereka terlebih dahulu.</div>';
    } else {
        $stmt = $conn->prepare("DELETE FROM jurusan WHERE id = ?");
        $stmt->bind_param("i", $id_hapus);
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Jurusan berhasil dihapus.</div>';
        } else {
            $message = '<div class="alert alert-danger">Gagal menghapus: ' . $conn->error . '</div>';
        }
        $stmt->close();
    }
}

// data jurusan
$jurusan_list = [];
$sql = "SELECT 
            j.id, j.kode, j.nama, j.kuota,
            (SELECT COUNT(p.id) FROM pendaftar p WHERE p.jurusan_id = j.id) as pendaftar
        FROM jurusan j
        ORDER BY j.nama";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $jurusan_list[] = $row;
    }
}
?>

<h3 class="mb-4">Manajemen Data Master & Jurusan</h3>

<?php echo $message; ?>

<a href="jurusan_tambah.php" class="btn btn-primary mb-3">+ Tambah Jurusan Baru</a>

<div class="table-responsive">
    <table class="table table-striped table-bordered table-sm">
        <thead class="bg-primary text-white">
            <tr>
                <th>Kode</th>
                <th>Nama Jurusan</th>
                <th>Kuota Total</th>
                <th>Pendaftar</th>
                <th>Status Kuota</th>
                <th style="width: 150px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($jurusan_list)): ?>
                <tr><td colspan="6" class="text-center">Data jurusan kosong.</td></tr>
            <?php else: ?>
                <?php foreach ($jurusan_list as $jurusan) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($jurusan['kode']); ?></td>
                        <td><?php echo htmlspecialchars($jurusan['nama']); ?></td>
                        <td><?php echo htmlspecialchars($jurusan['kuota']); ?></td>
                        <td><?php echo htmlspecialchars($jurusan['pendaftar']); ?></td>
                        <td>
                            <?php 
                            echo ($jurusan['pendaftar'] >= $jurusan['kuota']) ? 
                                '<span class="badge bg-danger">PENUH</span>' : 
                                '<span class="badge bg-success">Tersedia</span>';
                            ?>
                        </td>
                        <td>
                            <a href="jurusan_edit.php?id=<?php echo $jurusan['id']; ?>" class="btn btn-sm btn-info text-white">Edit</a>
                            
                            <form method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus jurusan ini?');">
                                <input type="hidden" name="action" value="hapus">
                                <input type="hidden" name="id_hapus" value="<?php echo $jurusan['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
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