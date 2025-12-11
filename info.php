<?php 
session_start(); 
include('includes/koneksi.php'); 
include('includes/header.php'); 


$gelombang_data = [];
$sql_gelombang = "SELECT * FROM gelombang WHERE tahun = 2025 ORDER BY tgl_mulai ASC";
$result_gelombang = $conn->query($sql_gelombang);
if ($result_gelombang->num_rows > 0) {
    while($row = $result_gelombang->fetch_assoc()) {
        $gelombang_data[] = $row;
    }
}
//ambil biaya / gelombang
$biaya_referensi = $gelombang_data[0]['biaya_daftar'] ?? 550000;
$biaya_referensi_format = "Rp " . number_format($biaya_referensi, 0, ',', '.');
//pengambilan data
?>

<h2 class="text-primary mb-4">Informasi Pendaftaran Siswa Baru</h2>

<div class="card p-4 shadow-sm">
    
    <h3 class="text-danger mb-3">Jadwal Penting Pendaftaran (Tahun 2025)</h3>
    
    <?php if (empty($gelombang_data)): ?>
        <p class="text-muted">Jadwal pendaftaran belum dibuka. Silakan hubungi admin.</p>
    <?php else: ?>
        <?php foreach ($gelombang_data as $gelombang): ?>
            <h5 class="text-secondary mt-3"><?php echo htmlspecialchars($gelombang['nama']); ?></h5>
            <table class="table table-bordered table-striped mt-2">
                <tr>
                    <td class="fw-bold bg-light" style="width: 30%;">Pembukaan Pendaftaran</td>
                    <td><?php echo date('d F Y', strtotime($gelombang['tgl_mulai'])); ?></td>
                </tr>
                <tr>
                    <td class="fw-bold bg-light">Batas Akhir Pendaftaran</td>
                    <td><?php echo date('d F Y', strtotime($gelombang['tgl_selesai'])); ?></td>
                </tr>
                <tr>
                    <td class="fw-bold bg-light">Biaya Pendaftaran</td>
                    <td><?php echo "Rp " . number_format($gelombang['biaya_daftar'], 0, ',', '.'); ?></td>
                </tr>
            </table>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <h3 class="mt-5 text-success">Persyaratan Umum</h3>
    <ul class="list-group list-group-flush mb-4">
        <li class="list-group-item">Lulusan SMP/MTs atau sederajat.</li>
        <li class="list-group-item">Mengisi formulir pendaftaran lengkap di website ini.</li>
        <li class="list-group-item">Scan Asli/Fotokopi Rapor Semester Terakhir.</li>
        <li class="list-group-item">Scan Asli/Fotokopi Akta Kelahiran.</li>
        <li class="list-group-item">Scan Asli/Fotokopi Kartu Keluarga (KK).</li>
        <li class="list-group-item">Membayar biaya pendaftaran (sesuai gelombang, mulai dari <?php echo $biaya_referensi_format; ?>).</li>
    </ul>

    <h3 class="mt-5 text-primary">Tata Cara Pendaftaran</h3>
    <ol class="list-group list-group-numbered">
        <li class="list-group-item d-flex justify-content-between align-items-start">
            <div class="ms-2 me-auto">
                <div class="fw-bold">Akses dan Buat Akun</div>
                Akses halaman <a href="daftar.php">Pendaftaran</a>, lalu buat akun baru (atau login jika sudah punya).
            </div>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-start">
            <div class="ms-2 me-auto">
                <div class="fw-bold">Pengisian Formulir</div>
                Isi semua bagian formulir (Biodata, Orang Tua, Asal Sekolah, dan Pilihan Jurusan).
            </div>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-start">
            <div class="ms-2 me-auto">
                <div class="fw-bold">Upload Berkas</div>
                Upload semua dokumen persyaratan (Rapor, Akta, KK) pada bagian akhir formulir.
            </div>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-start">
            <div class="ms-2 me-auto">
                <div class="fw-bold">Pembayaran</div>
                Lakukan pembayaran biaya pendaftaran ke rekening sekolah dan upload bukti transfer di halaman <a href="pembayaran.php">Pembayaran</a>.
            </div>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-start">
            <div class="ms-2 me-auto">
                <div class="fw-bold">Tunggu Verifikasi dan Pengumuman</div>
                Tim Keuangan dan Admin akan memverifikasi pembayaran dan berkas Anda. Status pendaftaran dapat dilihat setelah login.
            </div>
        </li>
    </ol>
</div>

<?php 
$conn->close();
include('includes/footer.php'); 
?>