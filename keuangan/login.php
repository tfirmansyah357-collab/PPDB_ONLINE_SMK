<?php
session_start();
$message = '';
$base_url = "http://localhost/ppdb/"; 


include('../includes/koneksi.php'); 

//  Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $base_url . "index.php");
    exit();
}
//login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM pengguna WHERE email = ? AND role = 'keuangan'";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        $message = '<div class="alert alert-danger">Error: Gagal mempersiapkan statement.</div>';
    } else {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // VERIFIKASI PASSWORD
            if (password_verify($password, $user['password_hash'])) {
                $_SESSION['keuangan_logged_in'] = true; 
                $_SESSION['keuangan_id'] = $user['id'];
                $_SESSION['keuangan_email'] = $user['email']; 
                header("Location: verifikasi.php"); 
                exit();
            } else {
                $message = '<div class="alert alert-danger" role="alert">Email atau Password Keuangan salah.</div>';
            }
        } else {
            $message = '<div class="alert alert-danger" role="alert">Email atau Password Keuangan salah.</div>';
        }
        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Staf Keuangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?php echo $base_url; ?>assets/images/logo_bn.jpeg">
    <style>
        body { background-color: #f0fff4; }
        .login-container { max-width: 400px; margin: 100px auto; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,.1); background-color: white; border-radius: 8px; border-top: 5px solid #198754; }
        .btn-keuangan { background-color: #198754; color: white; border: none; }
        .btn-keuangan:hover { background-color: #13653f; }
    </style>
</head>
<body>

<div class="login-container">
    <h3 class="text-center text-success mb-4">Login Staf Keuangan</h3>
    <p class="text-center text-muted small">Akses Verifikasi Pembayaran</p>
    
    <?php echo $message; ?>

    <form method="POST" action="login.php">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="keuangan@ppdb.com" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" value="uang123" required>
        </div>
        <button type="submit" class="btn btn-keuangan w-100 mt-3">Masuk Panel</button>
    </form>

    <p class="text-center mt-3 small"><a href="<?php echo $base_url; ?>index.php" class="text-secondary text-decoration-none">← Kembali ke Pilihan Akses</a></p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>