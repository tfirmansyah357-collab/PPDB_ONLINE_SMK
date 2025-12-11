<?php 
session_start();
$message = '';
$base_url = "http://localhost/ppdb/"; 
include('../includes/koneksi.php'); 
// log out
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $base_url . "index.php");
    exit();
}

// log

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    //email role
    $sql = "SELECT * FROM pengguna WHERE email = ? AND role = 'kepsek'";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        $message = '<div class="alert alert-danger">Error: Gagal mempersiapkan statement.</div>';
    } else {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // verif password
            if (password_verify($password, $user['password_hash'])) {
                $_SESSION['user_logged_in'] = true; 
                $_SESSION['user_mode'] = 'kepsek';  
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email']; 
                
                header("Location: kepsek_dashboard.php");
                exit();
            } else {
                $message = '<div class="alert alert-danger" role="alert">Email atau Password Kepala Sekolah salah.</div>';
            }
        } else {
            $message = '<div class="alert alert-danger" role="alert">Email atau Password Kepala Sekolah salah.</div>';
        }
        $stmt->close();
    }
    $conn->close();
}
// untuk login dan pindah ke dashboard
if (isset($_SESSION['user_logged_in']) && isset($_SESSION['user_mode']) && $_SESSION['user_mode'] === 'kepsek') {
    header("Location: kepsek_dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Kepala Sekolah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?php echo $base_url; ?>assets/images/logo_bn.jpeg">
    <style>
        body { background-color: #ffc10720; } 
        .login-container { max-width: 400px; margin: 100px auto; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,.1); background-color: white; border-radius: 8px; border-top: 5px solid #ffc107; }
        .btn-kepsek { background-color: #ffc107; color: black; border: none; }
        .btn-kepsek:hover { background-color: #e0a800; color: white; }
    </style>
</head>
<body>

<div class="login-container">
    <h3 class="text-center text-warning mb-4">Login Kepala Sekolah</h3>
    <p class="text-center text-muted small">Akses monitoring PPDB</p>
    
    <?php echo $message; ?>

    <form method="POST" action="login.php">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="kepsek@ppdb.com" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" value="kepsek123" required>
        </div>
        <button type="submit" class="btn btn-kepsek w-100 mt-3">Masuk Panel</button>
    </form>

    <p class="text-center mt-3 small"><a href="<?php echo $base_url; ?>index.php" class="text-secondary text-decoration-none">← Kembali ke Pilihan Akses</a></p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>