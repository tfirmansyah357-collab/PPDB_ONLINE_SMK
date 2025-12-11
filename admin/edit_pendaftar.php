<?php 
include('../includes/admin_header.php');

$pendaftar_id = $_GET['id'] ?? 0;
if ($pendaftar_id == 0) {
    // tidak ada id akan kembali ke kelola
    header("Location: kelola_pendaftar.php");
    exit();
}

// semua jurusan
$jurusan_options = [];
$sql_jurusan = "SELECT id, kode, nama FROM jurusan ORDER BY nama ASC";
$result_jurusan = $conn->query($sql_jurusan);
if ($result_jurusan->num_rows > 0) {
    while($row = $result_jurusan->fetch_assoc()) {
        $jurusan_options[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update_data') {
    
    $pendaftar_id_post = $_POST['pendaftar_id'];
    $jurusan_id_baru = $_POST['jurusan_id'];
    $nama_lengkap_baru = $_POST['nama_lengkap'];
    $asal_sekolah_baru = $_POST['asal_sekolah'];
    $conn->begin_transaction();
    try {
        // update pendaftar untuk jurusan
        $stmt1 = $conn->prepare("UPDATE pendaftar SET jurusan_id = ? WHERE id = ?");
        $stmt1->bind_param("ii", $jurusan_id_baru, $pendaftar_id_post);
        $stmt1->execute();

        // update pendaftar untuk nama
        $stmt2 = $conn->prepare("UPDATE pendaftar_data_siswa SET nama_lengkap = ? WHERE pendaftar_id = ?");
        $stmt2->bind_param("si", $nama_lengkap_baru, $pendaftar_id_post);
        $stmt2->execute();

        // update pendaftar untuk nama sekolah
        $stmt3 = $conn->prepare("UPDATE pendaftar_asal_sekolah SET nama_sekolah = ? WHERE pendaftar_id = ?");
        $stmt3->bind_param("si", $asal_sekolah_baru, $pendaftar_id_post);
        $stmt3->execute();
        $conn->commit();
        
        // untuk pesan jika berhasil
        $_SESSION['message'] = "Data pendaftar berhasil diperbarui!";
        header("Location: kelola_pendaftar.php");
        exit();
        
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        $message = '<div class="alert alert-danger">Update GAGAL: ' . $exception->getMessage() . '</div>';
    }
}
// data siswa di form
$data_siswa = null;
$sql = "SELECT 
            p.id, p.jurusan_id,
            pds.nama_lengkap,
            pas.nama_sekolah
        FROM pendaftar p
        JOIN pendaftar_data_siswa pds ON p.id = pds.pendaftar_id
        JOIN pendaftar_asal_sekolah pas ON p.id = pas.pendaftar_id
        WHERE p.id = ?
        LIMIT 1";
$stmt_data = $conn->prepare($sql);
$stmt_data->bind_param("i", $pendaftar_id);
$stmt_data->execute();
$result_data = $stmt_data->get_result();
$data_siswa = $result_data->fetch_assoc();

if (!$data_siswa) {
 // untuk id jika tidak ada
    echo "Data siswa tidak ditemukan.";
    include('../includes/admin_footer.php'); 
    exit();
}
?>

<h3 class="mb-4">Edit Data Pendaftar</h3>

<?php if (!empty($message)) echo $message; ?>

<div class="card shadow-sm">
    <div class="card-header">
        Mengedit: <strong><?php echo htmlspecialchars($data_siswa['nama_lengkap']); ?></strong>
    </div>
    <div class="card-body">
        <form method="POST" action="edit_pendaftar.php?id=<?php echo $pendaftar_id; ?>">
            <input type="hidden" name="action" value="update_data">
            <input type="hidden" name="pendaftar_id" value="<?php echo $pendaftar_id; ?>">
            
            <div class="mb-3">
                <label for="nama_lengkap" class="form-label">Nama Lengkap Siswa</label>
                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($data_siswa['nama_lengkap']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="asal_sekolah" class="form-label">Asal Sekolah</label>
                <input type="text" class="form-control" id="asal_sekolah" name="asal_sekolah" value="<?php echo htmlspecialchars($data_siswa['nama_sekolah']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="jurusan_id" class="form-label">Pilihan Jurusan (Ganti)</label>
                <select class="form-select" id="jurusan_id" name="jurusan_id" required>
                    <option value="">-- Pilih Jurusan Baru --</option>
                    <?php foreach ($jurusan_options as $jurusan): ?>
                        <option value="<?php echo $jurusan['id']; ?>" 
                            <?php if ($jurusan['id'] == $data_siswa['jurusan_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($jurusan['kode'] . ' - ' . $jurusan['nama']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">Ini akan mengubah jurusan yang dipilih oleh siswa.</div>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="kelola_pendaftar.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<?php 
$conn->close();
include('../includes/admin_footer.php'); 
?>