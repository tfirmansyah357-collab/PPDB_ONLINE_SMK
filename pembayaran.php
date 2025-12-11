<?php
session_start();
$base_url = "http://localhost/ppdb/"; 


include('includes/koneksi.php');

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_mode'] !== 'siswa') {
    header("Location: " . $base_url . "login_siswa.php");
    exit();
}

$user_id = $_SESSION['user_id'] ?? 0;
$email_siswa = $_SESSION['user_email'] ?? 'Calon Siswa';
$message = '';

$pendaftar_id = 0;
$biaya_pendaftaran_raw = 550000;
$status_pembayaran = 'Belum Daftar';
$sudah_upload_bukti = false;

$sql_cek = "SELECT p.id, p.status_pembayaran, g.biaya_daftar 
            FROM pendaftar p
            JOIN gelombang g ON p.gelombang_id = g.id
            WHERE p.user_id = ?";
$stmt_cek = $conn->prepare($sql_cek);
$stmt_cek->bind_param("i", $user_id);
$stmt_cek->execute();
$result_cek = $stmt_cek->get_result();

if ($result_cek->num_rows > 0) {
    $data_pendaftar = $result_cek->fetch_assoc();
    $pendaftar_id = $data_pendaftar['id'];
    $biaya_pendaftaran_raw = $data_pendaftar['biaya_daftar'];
    $status_pembayaran = $data_pendaftar['status_pembayaran'];
} else {
   // jika belum daftar akan masuk ke halaman daftar
    header("Location: " . $base_url . "daftar.php");
    exit();
}
$stmt_cek->close();

// biaya
$biaya_pendaftaran_format = "Rp " . number_format($biaya_pendaftaran_raw, 0, ',', '.');
$sql_cek_bayar = "SELECT id, status FROM pembayaran WHERE pendaftar_id = ?";
$stmt_cek_bayar = $conn->prepare($sql_cek_bayar);
$stmt_cek_bayar->bind_param("i", $pendaftar_id);
$stmt_cek_bayar->execute();
$result_cek_bayar = $stmt_cek_bayar->get_result();
if ($result_cek_bayar->num_rows > 0) {
    $sudah_upload_bukti = true;
    $data_pembayaran = $result_cek_bayar->fetch_assoc();
    $status_pembayaran = $data_pembayaran['status']; 
}
$stmt_cek_bayar->close();



if ($_SERVER["REQUEST_METHOD"] == "POST" && $pendaftar_id > 0 && !$sudah_upload_bukti) {
    
    $nama_pengirim = $_POST['nama_pengirim'] ?? null;
    $bank_pengirim = $_POST['bank_pengirim'] ?? null;
    
    if (isset($_FILES['bukti_transfer']) && $_FILES['bukti_transfer']['error'] == 0 && !empty($nama_pengirim) && !empty($bank_pengirim)) {
        
        $upload_dir = 'uploads/bukti_bayar/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = $_FILES['bukti_transfer']['name'];
        $new_file_name = "bayar_" . $pendaftar_id . "_" . basename($file_name);
        $target_file = $upload_dir . $new_file_name;
        $url_file = $base_url . $target_file; 

        if (move_uploaded_file($_FILES['bukti_transfer']['tmp_name'], $target_file)) {
            
            
            $conn->begin_transaction();
            try {
                // agar masuk ke pembayaran
                $sql_insert_bayar = "INSERT INTO pembayaran (pendaftar_id, nominal, nama_pengirim, bank_pengirim, url_bukti_transfer, tgl_upload, status) 
                                     VALUES (?, ?, ?, ?, ?, NOW(), 'Pending')";
                $stmt_insert = $conn->prepare($sql_insert_bayar);
                $stmt_insert->bind_param("idsss", $pendaftar_id, $biaya_pendaftaran_raw, $nama_pengirim, $bank_pengirim, $url_file);
                $stmt_insert->execute();

                
                $sql_update_pendaftar = "UPDATE pendaftar SET status_pembayaran = 'Pending Verifikasi' WHERE id = ?";
                $stmt_update = $conn->prepare($sql_update_pendaftar);
                $stmt_update->bind_param("i", $pendaftar_id);
                $stmt_update->execute();
                
                
                $conn->commit();
                
                $message = '<div class="alert alert-success">Konfirmasi berhasil diupload! Tim keuangan akan segera memverifikasi data Anda.</div>';
                $sudah_upload_bukti = true; 
                $status_pembayaran = 'Pending'; 

            } catch (mysqli_sql_exception $exception) {
                $conn->rollback();
                $message = '<div class="alert alert-danger">Gagal menyimpan data: ' . $exception->getMessage() . '</div>';
            }

        } else {
            $message = '<div class="alert alert-danger">Gagal meng-upload file bukti transfer.</div>';
        }
    } else {
        $message = '<div class="alert alert-danger">Harap isi semua field dan upload bukti transfer.</div>';
    }
}


include('includes/header.php'); 
?>

<h2 class="text-center text-primary mb-4">Konfirmasi Pembayaran Pendaftaran</h2>
<p class="text-center lead">Selesaikan pembayaran untuk memvalidasi pendaftaran Anda.</p>

<?php echo $message; ?>

<div class="card shadow-lg p-4">
    <div class="card-body">
        
        <div class="alert alert-info" role="alert">
          <h4 class="alert-heading">Penting!</h4>
          <p>Hai, <strong><?php echo htmlspecialchars($email_siswa); ?></strong>. Total biaya pendaftaran Anda adalah <strong><?php echo $biaya_pendaftaran_format; ?></strong>. Silakan lakukan pembayaran agar data Anda dapat segera kami verifikasi.</p>
          <hr>
          <p class="mb-0">Status Pembayaran Anda Saat Ini: <strong><?php echo htmlspecialchars($status_pembayaran); ?></strong></p>
        </div>

        <?php if ($sudah_upload_bukti): ?>
            <div class="alert alert-success text-center">
                <h4 class="alert-heading">Upload Bukti Berhasil</h4>
                <p>Anda sudah meng-upload bukti pembayaran. Saat ini statusnya adalah <strong><?php echo htmlspecialchars($status_pembayaran); ?></strong>.</p>
                <p>Tim keuangan akan segera memeriksanya. Terima kasih.</p>
            </div>
        <?php else: // ini untuk tampilan jika belum upload ?>
        <div class="row g-5 mt-3">
            <div class="col-lg-7">
                <h4 class="text-secondary border-bottom pb-2 mb-3">Metode Pembayaran</h4>
                
                <h5>1. Transfer Bank (Manual)</h5>
                <p>Silakan transfer ke rekening berikut:</p>
                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item"><strong>Bank:</strong> Bank Rakyat Indonesia (BRI)</li>
                    <li class="list-group-item"><strong>No. Rekening:</strong> 1234-56-789012-345</li>
                    <li class="list-group-item"><strong>Atas Nama:</strong> YAYASAN SMK BAKTI NUSANTARA 666</li>
                    <li class="list-group-item"><strong>Nominal:</strong> <?php echo $biaya_pendaftaran_format; ?></li>
                </ul>

                <h5>2. Konfirmasi Pembayaran Anda</h5>
                <p class="text-muted">Setelah melakukan transfer, mohon isi form di bawah ini dan upload bukti transfer Anda.</p>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="nama_pengirim" class="form-label">Nama Pemilik Rekening Pengirim</label>
                        <input type="text" class="form-control" id="nama_pengirim" name="nama_pengirim" required>
                    </div>
                    <div class="mb-3">
                        <label for="bank_pengirim" class="form-label">Transfer dari Bank</label>
                        <input type="text" class="form-control" id="bank_pengirim" name="bank_pengirim" placeholder="Contoh: BCA" required>
                    </div>
                    <div class="mb-3">
                        <label for="bukti_transfer" class="form-label">Upload Bukti Transfer (JPG/PNG/PDF)</label>
                        <input class="form-control" type="file" id="bukti_transfer" name="bukti_transfer" required>
                    </div>
                    <button type="submit" class="btn btn-ppdb btn-lg w-100 mt-3">Konfirmasi Pembayaran</button>
                </form>
            </div>

            <div class="col-lg-5">
                <div class="card bg-light p-3 text-center sticky-top" style="top: 20px;">
                    <h4 class="text-primary">Bayar Cepat dengan QRIS</h4>
                    <p class="text-muted">Scan kode QR di bawah ini dengan aplikasi E-Wallet (GoPay, OVO, DANA, ShopeePay) atau M-Banking Anda.</p>
                    
                    <img src="<?php echo $base_url; ?>assets/images/Qris1.jpeg" class="img-fluid rounded shadow-sm mx-auto" alt="Kode QRIS Pembayaran" style="max-width: 300px;">
                    
                    <h5 class="mt-3">SMK BAKTI NUSANTARA 666</h5>
                    <p class="fw-bold fs-4 text-danger mb-0"><?php echo $biaya_pendaftaran_format; ?></p>
                </div>
            </div>
        </div>
        <?php endif; //untuk bukti updload ?>

    </div>
</div>

<?php 
$conn->close();
include('includes/footer.php'); 
?>