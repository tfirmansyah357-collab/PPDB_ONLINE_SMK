<?php 
include('../includes/admin_header.php');

$user_id = $_GET['id'] ?? 0;
$message = '';
$message_type = 'info';

if ($user_id == 0) {
    // Jika tidak ada ID, kembali ke halaman users
    header("Location: users.php");
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'reset_password') {
    
    $user_id_post = $_POST['user_id'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $message = "Password baru dan konfirmasi password tidak cocok.";
        $message_type = 'danger';
    } elseif (strlen($new_password) < 3) {
         $message = "Password baru minimal harus 3 karakter.";
        $message_type = 'danger';
    } else {
        $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $sql_update = "UPDATE pengguna SET password_hash = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $new_password_hash, $user_id_post);
        if ($stmt_update->execute()) {
            $_SESSION['message'] = "Password untuk user ID $user_id_post berhasil di-reset!";
            header("Location: users.php");
            exit();
        } else {
            $message = "Gagal memperbarui password. Silakan coba lagi.";
            $message_type = 'danger';
        }
    }
}
$data_user = null;
$sql = "SELECT nama, email, role FROM pengguna WHERE id = ?";
$stmt_data = $conn->prepare($sql);
$stmt_data->bind_param("i", $user_id);
$stmt_data->execute();
$result_data = $stmt_data->get_result();
$data_user = $result_data->fetch_assoc();

if (!$data_user) {
    echo "Data user tidak ditemukan.";
    include('../includes/admin_footer.php'); 
    exit();
}
?>

<h3 class="mb-4">Reset Password Pengguna</h3>

<?php if (!empty($message)): ?>
    <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-header">
        Me-reset password untuk: <strong><?php echo htmlspecialchars($data_user['nama']); ?></strong> 
        (Email: <?php echo htmlspecialchars($data_user['email']); ?> | Role: <?php echo htmlspecialchars($data_user['role']); ?>)
    </div>
    <div class="card-body">
        <form method="POST" action="reset_password_admin.php?id=<?php echo $user_id; ?>">
            <input type="hidden" name="action" value="reset_password">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            
            <div class="mb-3">
                <label for="new_password" class="form-label">Password Baru</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required minlength="3">
                <div class="form-text">Masukkan password baru untuk pengguna ini.</div>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="3">
            </div>

            <button type="submit" class="btn btn-danger">Reset Password</button>
            <a href="users.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<?php 
$conn->close();
include('../includes/admin_footer.php'); 
?>