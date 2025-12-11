<?php
session_start();
$message = '';
$base_url = "http://localhost/ppdb/"; 

include('includes/koneksi.php'); 
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $sql = "SELECT * FROM pengguna WHERE email = ? AND role = 'pendaftar'";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        $message = '<div class="alert alert-danger">Error: Gagal mempersiapkan statement.</div>';
    } else {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        //hasil
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

           //password hash
            if (password_verify($password, $user['password_hash'])) {
                
                // Login berhasil
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_mode'] = 'siswa';
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_id'] = $user['id']; 
                $_SESSION['user_nama'] = $user['nama'];
                header("Location: index.php"); // Arahkan ke index
                exit();
            } else {
                // Password salah
                $message = '<div class="alert alert-danger" role="alert">Email atau Password salah.</div>';
            }
        } else {
            // Email tidak ditemukan
            $message = '<div class="alert alert-danger" role="alert">Email atau Password salah.</div>';
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
    <title>Login Calon Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .login-container { max-width: 400px; margin: 100px auto; padding: 20px; box-shadow: 0 4px 8px rgba(0,0,0,.05); background-color: white; border-radius: 8px; }
        .btn-siswa { background-color: #28a745; color: white; border: none; }
        .btn-siswa:hover { background-color: #218838; }
    </style>
</head>
<body>
<div class="login-container">
    <h3 class="text-center text-success mb-4">Login Calon Siswa</h3>
    <?php echo $message; ?>
    <form method="POST" action="login_siswa.php">
        <div class="mb-3">
            <label for="email" class="form-label">Email Anda</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        
        <button type="submit" class="btn btn-siswa w-100 mt-3">Masuk</button>
    </form>
    <hr>
    <p class="text-center">Belum punya akun?</p>
    <a href="daftar.php" class="btn btn-outline-success w-100 mt-2">Buat Akun Sekarang</a>
    <p class="text-center mt-3"><a href="index.php">Kembali ke Pilihan Akses</a></p>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>