<?php 
include('../includes/admin_header.php');
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']); 
$user_list = [];
$sql = "SELECT id, nama, email, role, aktif FROM pengguna ORDER BY role, nama";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $user_list[] = $row;
    }
}
?>

<h3 class="mb-4">Manajemen Akun Pengguna Sistem</h3>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>

<a href="user_tambah.php" class="btn btn-primary mb-3">+ Tambah Akun Baru</a>

<div class="table-responsive">
    <table class="table table-striped table-bordered table-sm align-middle">
        <thead class="bg-dark text-white">
            <tr>
                <th>ID</th>
                <th>Nama Lengkap</th>
                <th>Username/Email</th>
                <th>Role (Peran)</th>
                <th>Status</th>
                <th style="width: 150px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($user_list)): ?>
                <tr><td colspan="6" class="text-center">Data pengguna kosong.</td></tr>
            <?php else: ?>
                <?php foreach ($user_list as $user) : ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['nama']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <?php 
                            $role_badge = 'bg-secondary';
                            if($user['role'] == 'admin') $role_badge = 'bg-danger';
                            elseif($user['role'] == 'keuangan') $role_badge = 'bg-success';
                            elseif($user['role'] == 'panitia') $role_badge = 'bg-primary';
                            elseif($user['role'] == 'kepsek') $role_badge = 'bg-warning text-dark';
                            ?>
                            <span class="badge <?php echo $role_badge; ?>"><?php echo htmlspecialchars(ucfirst($user['role'])); ?></span>
                        </td>
                        <td>
                            <?php 
                            $badge = ($user['aktif'] == 1) ? 'bg-success' : 'bg-secondary';
                            $status = ($user['aktif'] == 1) ? 'Aktif' : 'Nonaktif';
                            ?>
                            <span class="badge <?php echo $badge; ?>"><?php echo $status; ?></span>
                        </td>
                        <td>
                            <a href="reset_password_admin.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger w-100">
                                Reset Password
                            </a>
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